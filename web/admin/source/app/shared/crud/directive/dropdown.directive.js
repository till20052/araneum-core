(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDropdown', CRUDDropdownDirective);

    CRUDDropdownDirective.$inject = ['$compile', 'supervisor'];

    /**
     * CRUD DropDown Directive
     *
     * @param $compile
     * @param supervisor
     * @returns {{
     *      link: link,
     *      restrict: string
     * }}
     * @constructor
     */
    function CRUDDropdownDirective($compile, supervisor) {
        return {
            link: link,
            restrict: 'E'
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            supervisor
                .loader('config')
                .onLoaded({
                    onSuccess: function (data) {
                        $(element)
                            .parent()
                            .addClass('text-center');
                        element.replaceWith($compile(createDropdown(data.action.row))(scope));
                    }
                });
        }

        /**
         * Create DropDown
         *
         * @param list
         * @returns {*|jQuery}
         */
        function createDropdown(list) {
            return $('<div class="btn-group" />')
                .attr('uib-dropdown', '')
                .append(
                    $('<button class="btn btn-xs btn-default dropdown-toggle" />')
                        .attr({
                            type: 'button',
                            'uib-dropdown-toggle': ''
                        })
                        .append($('<em class="icon-settings" />')),
                    createDropdownMenu(list)
                );
        }

        /**
         * Create DropDownMenu
         *
         * @param list
         * @returns {*|jQuery}
         */
        function createDropdownMenu(list) {
            var groups = Object.keys(list);
            return $('<ul class="dropdown-menu-right" />')
                .attr({
                    role: 'menu',
                    'uib-dropdown-menu': ''
                })
                .append($.map(list, function (list, groupKey) {
                    var container = [];

                    if (groups.indexOf(groupKey) !== 0)
                        container.push($('<li class="divider" />'));

                    list.forEach(function (options) {
                        container.push($('<li role="menuitem" />')
                            .append(
                                $('<a href="javascript:void(0);" />')
                                    .html('{{ "' + options.display.label + '" | translate }}')
                                    .prepend(
                                        $('<em class="mr" />').addClass(options.display.icon)
                                    )
                            ));
                    });

                    return container;
                }));
        }
    }

})();