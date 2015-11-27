(function (ng) {
	'use strict';

	ng.module('app.locales')
		.controller('LocalesController', LocalesController);

	LocalesController.$inject = ['$compile', '$scope', '$http', 'DTOptionsBuilder'];
	function LocalesController($compile, $scope, $http, DTOptionsBuilder) {

		/**
		 * Constructor
		 */
		(function (vm) {

			initialization(onInitSuccess, onInitError);

			vm.errors = [];

			vm.dt = {
				initialized: false,
				options: DTOptionsBuilder
					.newOptions()
					.withOption('processing', true)
					.withOption('serverSide', true)
					.withOption('sAjaxSource', '/admin/locales/datatable.json')
					.withOption('fnServerData', function (source, data, callback, settings) {
						settings.jqXHR = $.ajax({
							dataType: 'json',
							type: "POST",
							url: source,
							data: data,
							success: function (response) {
								ng.forEach(response.aaData, function (item, i) {
									this[i] = item
										.splice(0, item.length - 1)
										.concat([
											'<div widget="actions" />',
											'<div widget="checkbox" />'
										]);
								}, response.aaData);
								callback(response);
								ng.element($('div[widget="actions"]'))
									.replaceWith(
										$compile($('widget#locales-actions > div'))($scope)
									)
								ng.element($('div[widget="checkbox"]'))
									.replaceWith(
										$compile($('widget#locales-checkbox > div'))($scope)
									)
							}
						});
					})
					.withPaginationType('full_numbers'),
				columns: []
			};

			/**
			 * Initialization event in success case
			 * @param response
			 */
			function onInitSuccess(response) {
				ng.forEach(response.datatable.columns, function (f) {
					this.push(f);
				}, vm.dt.columns);
				vm.dt.initialized = true;
			}

			/**
			 * Initialization event in error case
			 */
			function onInitError() {
				vm.errors.push('Can\'t load data to datatable');
			}

		})($scope);

		/**
		 * Initialization of module
		 * @param onSuccess
		 * @param onError
		 */
		function initialization(onSuccess, onError) {
			$http
				.get('/admin/locales/init.json')
				.success(onSuccess)
				.error(onError);
		}

	}

})(angular);