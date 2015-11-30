(function () {
    angular
        .module('app.formBuilder')
        .directive('formBuilder', ['$compile, fromBuilderFactory', function ($compile, formBuilderFactory) {
            return {
                restrict: 'AE',
                template: '/assets/build/html/widget/from-builder.html',
                link: function ($scope, element, attr) {
                    formBuilderFactory.setData($scope.elementsData);
                    element
                        .find('form').attr(formBuilderFactory.getFormOptions())
                        .append($compile(formBuilderFactory.buildForm())($scope));
                }
            }
        }])
})();