(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudUi', CRUDUiDirective);

    CRUDUiDirective.$inject = ['$compile'];

    function CRUDUiDirective($compile) {
        return {
            link: link,
            restrict: 'E',
            scope: {
                type: '@'
            }
        };

        function link(scope, iElement, iAttrs) {
            scope.$watch(function () {
                return $(iElement).parent().length > 0;
            }, function (ready) {
                if (ready) {
                    var directive = $('<crud-ui-'+scope.type+' />');

                    if(iAttrs.hasOwnProperty('view')){
                        scope.view = iAttrs.view;
                    }

                    iElement.replaceWith($compile(directive)(scope));
                }
            });
        }
    }

})();