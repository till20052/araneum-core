(function() {
	"use strict";

	angular
		.module('app.formBuilder')
		.factory('formBuildCreateUpdateService', formBuildCreateUpdateService);

	formBuildCreateUpdateService.$inject = ['fromBuilderService'];

	function formBuildCreateUpdateService( fromBuilderService ) {
		var formBuildCreateUpdateService = {};
		$.extend(formBuildCreateUpdateService,fromBuilderService);

		formBuildCreateUpdateService.templates = {
			text: '<div class="form-group">' +
			'<label class="col-lg-2 control-label"></label>' +
			'<div class="col-lg-10">' +
			'<input type="text" class="form-control" />' +
			'</div>' +
			'</div>',
			email: '<div class="form-group">' +
			'<label class="col-lg-2 control-label"></label>' +
			'<div class="col-lg-10">' +
			'<input type="email" class="form-control" />' +
			'</div>' +
			'</div>',
			datetime: '<div class="form-group">' +
			'<label class="col-lg-2 control-label"></label>' +
			'<div class="col-lg-10">' +
			'<input type="date" class="form-control" />' +
			'</div>' +
			'</div>',
			choice: '<div class="form-group">' +
			'<label class="col-lg-2 control-label"></label>' +
			'<div class="col-lg-10">' +
			'<select class="form-control"></select>' +
			'</div>' +
			'</div>'
		};


		formBuildCreateUpdateService.getButtonsForForm = getButtonsForForm;

		return formBuildCreateUpdateService;

		function getButtonsForForm( url, id ) {
			var templateButtons = '<div class="col-lg-2">' +
					'<fieldset>' +
					'<div class="form-group">' +
					'<button type="submit" class="btn btn-default mr-sm" id="reset">' +
					'<em class="icon-refresh mr-sm"></em>' +
					'Reset' +
					'</button>' +
					'<button type="submit" class="btn btn-primary" id="search">' +
					'<em class="icon-magnifier mr-sm"></em>' +
					'Search' +
					'</button>' +
					'</div>' +
					'</fieldset>' +
					'</div>';
			templateButtons = $(templateButtons);
			$(templateButtons).find('#search').attr('ng-click', 'send("' + url + '"); closeThisDialog()');
			$(templateButtons).find('#reset').attr('ng-click', 'closeThisDialog()');

			return templateButtons[0].outerHTML;
		}
	}
})();