(function () {
    angular
        .module('app.formBuilder')
        .directive('formBuilder', ['$compile, fromBuilderFactory', function ($compile, formBuilderFactory) {
            return {
                restrict: 'AE',
                template: '/assets/build/html/widget/from-builder.html',
                link: function ($scope, element, attr) {
                    formBuilderFactory.setData($scope.elementsData);
                    element.attr(formBuilderFactory.getFormOptions());
                    element.find('form').append($compile(formBuilderFactory.buildForm())($scope));
                }
            }
        }])
})();