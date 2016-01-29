(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatable', CRUDDataTableDirective);

    CRUDDataTableDirective.$inject = ['$compile'];

    /**
     * CRUD DataTable Directive
     *
     * @param $compile
     * @returns {Object}
     * @constructor
     */
    function CRUDDataTableDirective($compile) {
        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDDataTableController',
            controllerAs: 'vm',
            templateUrl: 'crud/datatable.html',
            replace: true
        };

        function link(scope, iElement, iAttrs, controller) {
            scope.$watch(function () {
                return $('table', iElement).length > 0;
            }, function (ready) {
                if (!ready) {
                    return;
                }
                $('table', iElement).click(function (event) {
                    controller.onDatatableClick(event, this);
                });
            });

            var observer = new MutationObserver(function () {
                var table = $('table', iElement);
                if (table.length > 0) {
                    $compile($('>tbody', table))(scope);
                    $('div.checkbox', table)
                        .addClass('mr0')
                        .parent()
                        .addClass('text-center')
                        .find('span.fa-check')
                        .addClass('mr0');
                }
            });

            observer.observe(iElement[0], {
                childList: true,
                subtree: true
            });
        }
    }

})();