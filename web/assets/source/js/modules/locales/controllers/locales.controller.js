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
					.withOption('sAjaxSource', '/manage/locales/datatable.json')
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
								$('.dataTable td').each(function () {
									$(this).addClass('bb0 bl0');
								});


								$('div[widget]').each(function () {
									var ui = $(this);
									$(ui.parents('td').eq(0)).addClass('text-center p0');
									ui.replaceWith(
										$compile($('widget#locales-' + ui.attr('widget') + ' > div').clone())($scope)
									);
								});
							}
						});
					})
					.withPaginationType('full_numbers'),
				columns: []
			};

			vm.onTableClickEvent = onTableClickEvent;

			/**
			 * Initialization event in success case
			 * @param response
			 */
			function onInitSuccess(response) {
				ng.forEach(response.grid.columns, function (f) {
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

			/**
			 * Click Table Event
			 * @param e
			 */
			function onTableClickEvent(e) {
				var tag = $(e.target)
				if (tag.attr('type') == 'checkbox') {
					if (tag.attr('rel') == 'select-all') {
						$('tbody input[type="checkbox"]', $(tag.parents('table').eq(0)))
							.prop('checked', tag.prop('checked'));
					}
					else if (!tag.prop('checked')) {
						$('thead input[type="checkbox"]', $(tag.parents('table').eq(0)))
							.prop('checked', false);
					}
				}
			}

		})($scope);

		/**
		 * Initialization of module
		 * @param onSuccess
		 * @param onError
		 */
		function initialization(onSuccess, onError) {
			$http
				.get('/manage/locales/init.json')
				.success(onSuccess)
				.error(onError);
		}

	}

})(angular);