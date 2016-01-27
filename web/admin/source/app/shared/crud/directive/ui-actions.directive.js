(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudUiActions', CRUDActionsDirective);

    CRUDActionsDirective.$inject = ['$compile', 'CRUDConfigLoader'];

    function CRUDActionsDirective($compile, CRUDConfigLoader) {
        return {
            link: link,
            restrict: 'E'
        };

        function link(scope, iElement) {
            scope.$watch(function () {
                return $(iElement).parent().length > 0;
            }, function (ready) {
                if (ready) {
                    iElement.replaceWith($compile(buildActions(scope.view))(scope));
                }
            });
        }

        function buildActions(view) {
            return ({
                menu: buildMenuActions
            })[view]();
        }

        function buildMenuActions() {
            var dropdown = $('<div class="btn-group" uib-dropdown />').append(
                $('<button type="button" class="btn btn-xs btn-default dropdown-toggle" uib-dropdown-toggle />')
                    .append($('<em class="icon-settings" />'))
                    .click(function (e) {
                        $(this).blur();
                        e.stopPropagation();
                    }),
                $('<ul uib-dropdown-menu role="menu" class="dropdown-menu-right" />')
            );

            CRUDConfigLoader.load({
                onSuccess: function (data) {
                    var groups = Object.keys(data.action.row);
                    for (var key in data.action.row) {
                        /*jshint -W083 */
                        if (data.action.row.hasOwnProperty(key)) {
                            if (groups.indexOf(key) !== 0) {
                                $('>ul', dropdown).append($('<li class="divider" />'));
                            }
                            data.action.row[key].forEach(function (action) {
                                var li = $('<li role="menuitem" />').append(
                                    $('<a href="#" />')
                                        .data('crud-action', action)
                                        .html('{{ "' + action.display.label + '" | translate }}')
                                );
                                if (action.display.hasOwnProperty('icon')) {
                                    $('>a', li).prepend($('<em class="mr" />').addClass(action.display.icon));
                                }
                                $('>ul', dropdown).append(li);
                            });
                        }
                    }

                    //$('>ul', dropdown).append($.map(groups, function(){
                    //    return
                    //}));
                }
            });

            return dropdown;
        }
    }

})();