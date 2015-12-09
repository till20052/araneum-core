(function() {
	"use strict";

	angular
		.module( 'app.action-builder' )
		.directive( 'actionBuilder', actionBuilder );

	actionBuilder.$inject = ['$compile', 'formDataService', 'creatorActionBuilder', 'RouteHelpers'];

	function actionBuilder( $compile, formDataService, creatorActionBuilder ) {
		var directive = {
			restrict: 'E',
			link: link
		};
		
		return directive;

		function link( $scope, element, attrs ) {
			var type = element.data('item' ), // top or row
				builder = creatorActionBuilder.getBuilder(type), // return top or row action service builder
				promise = formDataService.getPromise();

			if ( typeof type === "undefined" ) {
				element.remove();
				return false;
			}

			promise.then(getDataForActions);

			function getDataForActions( response ) {
				var actions = response.action,
					actionsTemplate = '';

				builder.setData(actions[type], attrs.model, $scope);
				actionsTemplate = builder.getActionsTemplate();
				element.append($compile(actionsTemplate)($scope));
			}
		}
	}
})();
