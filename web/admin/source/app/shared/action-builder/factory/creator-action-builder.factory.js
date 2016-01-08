(function() {
	"use strict";

	angular
		.module('app.action-builder' )
		.factory('creatorActionBuilder', creatorActionBuilder);

	creatorActionBuilder.$inject = ['topActionBuilderService', 'rowActionBuilderService'];

	function creatorActionBuilder(topActionBuilderService, rowActionBuilderService) {
		var factory = {
				builder: undefined,
				getBuilder: getBuilder
			};

		return factory;

		/**
		 * Return action builder
		 * @param type
		 * @returns {*}
		 */
		function getBuilder(type) {
			if ( type === 'top' ) {
				this.builder = topActionBuilderService;
			}

			if ( type === 'row' ) {
				this.builder = rowActionBuilderService;
			}

			return this.builder;
		}
	}
})();