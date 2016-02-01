(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudToolbar', CRUDToolbarDirective);

    CRUDToolbarDirective.$inject = ['CRUDConfigLoader', '$compile'];

    function CRUDToolbarDirective(CRUDConfigLoader, $compile) {
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
            CRUDConfigLoader.load({
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

                if(keys.indexOf(key) > 0){
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

            return toolbar;
        }

        function createGroup(data) {
            var group = $('<div class="btn-group" />');

            if(
                data.hasOwnProperty('buttons') &&
                data.buttons instanceof Array
            ){
                data.buttons.forEach(function (data) {
                    group.append(createButton(data));
                });
            }

            return group;
        }

        function createButton(data) {
            return $('<button class="btn btn-sm" />')
                .data('config', data)
                .attr({
                    'ng-click': 'controller.click($event)',
                    'uib-tooltip': '{{ "' + data.display.label + '" | translate }}'
                })
                .addClass(data.display.btnClass)
                .append(
                    $('<em />').addClass(data.display.icon)
                );
        }
    }

})();