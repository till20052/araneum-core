(function () {
    angular
        .module('app.formBuilder')
        .directive('formBuilder', ['$compile', 'fromBuilderFactory', 'RouteHelpers', function ($compile, formBuilderFactory, RouteHelpers) {
            return {
                restrict: 'AE',
                template: RouteHelpers.basepath('widget/from-builder.html'),
                link: function ($scope, element, attr) {
                    formBuilderFactory.setData($scope.elementsData);
                    element
                        .find('form').attr(formBuilderFactory.getFormOptions())
                        .append($compile(formBuilderFactory.buildForm())($scope));
                }
            }
        }])
})();