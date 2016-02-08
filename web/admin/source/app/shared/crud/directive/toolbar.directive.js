(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudToolbar', CRUDToolbarDirective);

    CRUDToolbarDirective.$inject = ['CRUDConfigLoader', 'CRUDSupervisor', '$compile'];

    function CRUDToolbarDirective(configLoader, supervisor, $compile) {
        var controller;

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDActionsController',
            controllerAs: 'controller',
            scope: {
                options: '='
            }
        };

        function link(scope, element) {
            controller = scope.controller;

            configLoader.load({
                onSuccess: function (data) {
                    element.replaceWith($compile(createToolbar(data.action.top, scope.options))(scope));
                }
            });
        }

        function createToolbar(data, options) {
            var toolbar = $('<div />'),
                keys = Object.keys(data);

            for (var key in data) {
                if (!data.hasOwnProperty(key))
                    continue;

                var group = createGroup({
                    buttons: data[key]
                });

                if (keys.indexOf(key) > 0) {
                    group.addClass('mr');
                }

                if (options instanceof Object) {
                    if (
                        options.hasOwnProperty('pull') &&
                        ['left', 'right'].indexOf(options.pull) !== -1
                    ) {
                        group.addClass('pull-' + options.pull);
                    }
                }

                toolbar.append(group);
            }

            supervisor.setToolBar(toolbar);

            return toolbar;
        }

        function createGroup(data) {
            var group = $('<div class="btn-group" />');

            if (
                data.hasOwnProperty('buttons') &&
                data.buttons instanceof Array
            ) {
                data.buttons.forEach(function (data) {
                    group.append(createButton(data));
                });
            }

            return group;
        }

        function createButton(data) {
            return $('<button class="btn btn-sm" />')
                .addClass(data.display.btnClass)
                .data('crud', normalizeData(data))
                .attr('uib-tooltip', '{{ "' + data.display.label + '" | translate }}')
                .click(controller.defineAction)
                .append(
                    $('<em />').addClass(data.display.icon)
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