(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudToolbar', CRUDToolbarDirective);

    CRUDToolbarDirective.$inject = ['$compile', 'supervisor'];

    function CRUDToolbarDirective($compile, supervisor) {
        var controller;

        $.fn = angular.extend($.fn, {
            setAvailable: function () {
                $('button', this)
                    .filter(function () {
                        return ['setState', 'remove'].indexOf($(this).data('action')) !== -1;
                    })
                    .prop('disabled', !$(selector, t.body)
                        .toArray()
                        .some(function (checkbox) {
                            return !!$(checkbox).prop('checked');
                        }));
            }
        });

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDActionsController',
            controllerAs: 'controller',
            scope: {
                id: '@'
            }
        };

        function link(scope, element) {
            controller = scope.controller;
            supervisor.loader.config
                .onLoaded({
                    onSuccess: function (data) {
                        element.replaceWith($compile(
                            supervisor.toolBar(scope.id, createToolBar(data.action.top))
                        )(scope));
                    }
                });
        }

        /**
         * Create toolBar
         *
         * @param {Object} options
         * @returns {JQuery|jQuery}
         */
        function createToolBar(options) {
            return $('<div />')
                .append(
                    Object
                        .keys(options)
                        .map(function (key) {
                            return createGroup(options[key]);
                        })
                )
                .find('>*:not(:first-child)')
                .addClass('mr')
                .parent();
        }

        /**
         * Create group of toolBar buttons
         *
         * @param {Array} buttons
         * @returns {JQuery|jQuery}
         */
        function createGroup(buttons) {
            return $('<div class="btn-group pull-right" />')
                .append(
                    buttons.map(function (buttonData) {
                        return createButton(buttonData);
                    })
                );
        }

        /**
         * Create toolBar button
         *
         * @param {Object} options
         * @returns {JQuery|jQuery}
         */
        function createButton(options) {
            return $('<button class="btn btn-sm" />')
                .addClass(options.display.btnClass)
                .data({
                    action: ({
                        create: 'create',
                        editRow: 'setState',
                        deleteRow: 'remove'
                    })[options.callback]
                })
                .attr('uib-tooltip', '{{ "' + options.display.label + '" | translate }}')
                .click(controller.defineAction)
                .append(
                    $('<em />').addClass(options.display.icon)
                );
        }

        function normalizeData(data) {
            return angular.extend({
                action: ({
                    create: 'create',
                    editRow: 'setState',
                    deleteRow: 'remove'
                })[data.callback],
                available: function () {
                    return !!(
                        ['setState', 'remove'].indexOf(this.action) === -1 ||
                        this.supervisor.dataTable.selected().length > 0
                    );
                }
            }, (function (ext) {
                if (data.hasOwnProperty('resource'))
                    ext.url = data.resource;
                return ext;
            })({}), (function (ext) {
                if (data.hasOwnProperty('form'))
                    ext.form = {
                        url: data.form
                    };
                return ext;
            })({}), (function (ext) {
                if (
                    data.hasOwnProperty('confirm') &&
                    data.confirm instanceof Object
                ) {
                    var c = data.confirm;
                    ext.confirm = {
                        title: c.title,
                        buttons: {
                            yes: {
                                title: c.yes.title
                            },
                            no: {
                                title: c.no.title
                            }
                        }
                    };
                }
                return ext;
            })({}));
        }
    }

})();