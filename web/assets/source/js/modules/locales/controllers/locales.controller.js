(function (ng) {
	'use strict';

	ng.module('app.locales')
		.controller('LocalesController', LocalesController);

	LocalesController.$inject = ['$scope', '$http', 'DTOptionsBuilder'];
	function LocalesController($scope, $http, DTOptionsBuilder) {
		/**
		 * Constructor
		 */
		(function (vm) {

			vm.dt = {
				initialized: false,
				options: DTOptionsBuilder
					.newOptions()
					.withOption('processing', true)
					.withOption('serverSide', true)
					.withOption('sAjaxSource', '/admin/grid/locale.json')
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
											null, null
										]);
								}, response.aaData);
								callback(response);
							}
						});
					})
					.withPaginationType('full_numbers'),
				columns: []
			};

			init(function (response) {
				ng.forEach(response.headers, function (f) {
					this.push(f);
				}, vm.dt.columns);
				vm.dt.initialized = true;
			});

		})($scope);

		function init(onSuccess, onError) {
			$http
				.get('/admin/grid/locale.json')
				.success(onSuccess)
		}


	}

})(angular);