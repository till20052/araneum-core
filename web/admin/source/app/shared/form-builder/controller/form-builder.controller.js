(function() {
	'use strict';

	angular
		.module('app.formBuilder')
		.controller('FormBuilderController', FormBuilderController);

	FormBuilderController.$inject = ['$state', '$scope', '$http', '$compile', 'DTOptionsBuilder', 'DTInstances', 'formDataService', 'TranslateDatatablesService'];

	/**
	 *
	 * @param $state
	 * @param $scope
	 * @param $http
	 * @param $compile
	 * @param DTOptionsBuilder options for datatabe
	 * @param DTInstances changing data in datatable
	 * @param formDataService factory for store data form server
	 * @param TranslateDatatablesService
	 * @constructor
	 */
	function FormBuilderController($state, $scope, $http, $compile, DTOptionsBuilder, DTInstances, formDataService, TranslateDatatablesService) {
		var vm = this;
		var formJsonUrl = $state.$current.initialize;
		formDataService.setFromUrl(formJsonUrl);
		var promise = formDataService.getPromise();
		vm.onTableClickEvent = onTableClickEvent;

		vm.dt = {
			initialized: false,
			instance: {},
			options: DTOptionsBuilder
				.newOptions()
				.withOption('processing', true)
				.withOption('serverSide', true)
				.withOption('fnServerData', function(source, data, callback, settings) {
					settings.jqXHR = $.ajax({
						dataType: 'json',
						type: "POST",
						url: source,
						data: data,
						success: function(response) {
							angular.forEach(response.aaData, function(item, i) {
								this[i] = item
									.splice(0, item.length - 1)
									.concat([
										'<div widget="actions" />',
										'<div widget="checkbox" />'
									]);
							}, response.aaData);
							callback(response);
							$('.dataTable td').each(function() {
								$(this).addClass('bb0 bl0');
							});

							$('div[widget]').each(function() {
								var ui = $(this);
								$(ui.parents('td').eq(0)).addClass('text-center p0');
								ui.replaceWith(
									$compile($('widget#locales-' + ui.attr('widget') + ' > div').clone())($scope)
								);
							});
						}
					});
				})
				.withOption('language', TranslateDatatablesService.translateTable())
				.withPaginationType('full_numbers'),
			columns: []
		};

		/**
		 * Set url to datatable
		 * @param url
		 */
		vm.search = function(url) {
			var formData = $('form').serialize();
			vm.dt.options.sAjaxSource = url + '?' + formData;
		};

		/**
		 * Reset datatable url
		 * @param $event
		 * @param url
		 * @param id
		 */
		vm.reset = function($event, url, id) {
			$($event.currentTarget).closest('.row').find('#' + id)[0].reset();
			vm.dt.options.sAjaxSource = url;
		};

		vm.errors = [];

		promise.then(function(response) {
			onInitSuccess(response);
		});

		/**
		 * get data form server and add colums to datatable
		 * @param response
		 */
		function onInitSuccess(response) {
			var massTranslate = [];
			vm.dt.options.sAjaxSource = response.grid.source;

			angular.forEach(response.grid.columns, function(value){
				massTranslate.push(value);
			});

			vm.dt.columns = massTranslate;
			vm.dt.initialized = true;
		}

		/**
		 * Click Table Event
		 * @param e
		 */
		function onTableClickEvent(e) {
			var tag = $(e.target);
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
	}
})();
