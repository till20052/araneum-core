(function() {
    angular
        .module('app.formBuilder')
        .directive('formBuilder', ['$compile', 'formBuilderFactory', 'RouteHelpers', function($compile, formBuilderFactory, RouteHelpers) {
            return {
                restrict: 'AE',
                templateUrl: RouteHelpers.basepath('widget/from-builder.html'),
                link: function($scope, element, attr) {
                    var type = element.data('item');
                    var builder = formBuilderFactory.getBuilder(type, $scope);

                    $scope.vm.formObj.$promise.then(function(response) {
                        builder.setData(response[type]);
                        if (builder !== undefined) {
                            element
                                .find('form').attr(builder.getFormOptions())
                                .append($compile(builder.buildForm())($scope))
                                .closest('.panel-body')
                                .find('.row')
                                .first()
                                .append($compile(builder.getButtonsForForm())($scope));
                        }
                    });
                }
            };
        }]);
})();
