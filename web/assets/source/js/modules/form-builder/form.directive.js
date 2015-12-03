(function() {
	angular
		.module('app.formBuilder')
		.directive('formBuilder', ['$compile', 'formDataService', 'formBuilderFactory', 'RouteHelpers', function($compile, formDataService, formBuilderFactory, RouteHelpers) {
			return {
				restrict: 'AE',
				templateUrl: RouteHelpers.basepath('widget/from-builder.html'),
				link: function($scope, element, attr) {
					var type = element.data('item');
					var builder = formBuilderFactory.getBuilder(type, $scope);
					var promise = formDataService.getPromise();

					promise.then(function(response) {
						builder.setData(response[type]);
						if (builder !== undefined) {
							element
								.find('form').attr(builder.getFormOptions())
								.append($compile(builder.buildForm())($scope))
								.closest('.panel-body')
								.find('.row')
								.first()
								.append($compile(builder.getButtonsForForm(response.grid.source, builder.getFormOptions().id))($scope));
						}
					});
				}
			};
		}]);
})();
