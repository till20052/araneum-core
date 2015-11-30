(function () {
    angular
        .module('app.formBuilder')
        .directive('formBuilder', ['$compile', 'formBuilderFactory', 'RouteHelpers', function ($compile, formBuilderFactory, RouteHelpers) {
            return {
                restrict: 'AE',
                template: RouteHelpers.basepath('widget/from-builder.html'),
                link: function ($scope, element, attr) {
                    var builder = formBuilderFactory.getBuilder($scope);

                    element
                        .find('form').attr(builder.getFormOptions())
                        .append($compile(builder.buildForm())($scope));
                }
            }
        }])
})();