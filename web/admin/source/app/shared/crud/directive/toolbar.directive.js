(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudToolbar', CRUDToolbarDirective);

    CRUDToolbarDirective.$inject = ['$compile', 'supervisor'];

    function CRUDToolbarDirective($compile, supervisor) {
        var controller;

        $.fn = angular.extend($.fn, {
            setAvailable: function (state) {
                $('button', this)
                    .filter(function () {
                        return ['setState', 'remove'].indexOf($(this).data('action')) !== -1;
                    })
                    .prop('disabled', state);
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

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            controller = scope.controller;
            supervisor
                .loader('config')
                .onLoaded({
                    onSuccess: function (data) {
                        element.replaceWith($compile(createToolBar(data.action.top))(scope));
                    }
                });
        }

        /**
         * Create toolBar
         *
         * @param {Object} options
         * @returns {jQuery}
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
         * @returns {jQuery}
         */
        function createGroup(buttons) {
            return $('<div class="btn-group pull-right" />')
                .append(
                    buttons.map(function (options) {
                        return createButton(options);
                    })
                );
        }

        /**
         * Create toolBar button
         *
         * @param {Object} options
         * @returns {jQuery}
         */
        function createButton(options) {
            return $('<button class="btn btn-sm" />')
                .addClass(options.display.btnClass)
                .attr('uib-tooltip', '{{ "' + options.display.label + '" | translate }}')
                .append(
                    $('<em />').addClass(options.display.icon)
                );
        }
    }

})();