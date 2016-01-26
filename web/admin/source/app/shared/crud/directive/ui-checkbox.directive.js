(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudUiCheckbox', CRUDUiCheckboxDirective);

    CRUDUiCheckboxDirective.$inject = ['$compile'];

    function CRUDUiCheckboxDirective($compile) {
        return {
            link: link,
            restrict: 'E',
            scope: {}
        };

        function link(scope, iElement) {
            scope.$watch(function () {
                return $(iElement).parent().length > 0;
            }, function (ready) {
                if (ready) {
                    var checkbox = $('<div class="checkbox c-checkbox" />')
                        .append(
                            $('<label />')
                                .append($('<input type="checkbox" />'))
                                .append($('<span class="fa fa-check" />'))
                        );
                    iElement.replaceWith($compile(checkbox)(scope));
                }
            });
        }
    }

})();