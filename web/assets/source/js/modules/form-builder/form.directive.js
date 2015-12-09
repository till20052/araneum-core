(function() {
	angular
		.module('app.formBuilder')
		.directive('formBuilder', ['$compile', '$resource', 'formDataService', 'formBuilderFactory', 'RouteHelpers', function($compile, $resource, formDataService, formBuilderFactory, RouteHelpers) {
			return {
				restrict: 'AE',
				templateUrl: RouteHelpers.basepath('widget/from-builder.html'),
				link: function($scope, element, attr) {
					var type = element.data('type');
					var builder = formBuilderFactory.getBuilder(type);
					var promise = {},
						dataUrl = $scope.url;

					if (builder === undefined) {
						return false;
					}
					
					if ( dataUrl !== undefined ) {
						formDataService.setFromUrl($scope.url);
					}

					promise = formDataService.getPromise();
					promise.then(function(response) {
						var data = response[type];

						if ( dataUrl !== undefined ) {
							data = response;
						}
						
						builder.setData(data);
						element
							.find('form').attr(builder.getFormOptions())
							.append($compile(builder.buildForm())($scope));

						if ( dataUrl === undefined ) {
							element
									.closest('.panel-body')
									.find('.row')
									.first()
									.append($compile(builder.getButtonsForForm(response.grid.source, builder.getFormOptions().id))($scope));
						}

						if ( dataUrl !== undefined ) {
							element
									.find('form')
									.append($compile(builder.getButtonsForForm(data.var.action))($scope));
						}
					});
				}
			};
		}]);
})();
