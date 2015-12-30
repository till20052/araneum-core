(function () {
    "use strict";

    angular
        .module('app.formBuilder')
        .factory('formBuildCreateUpdateService', formBuildCreateUpdateService);

    formBuildCreateUpdateService.$inject = ['fromBuilderService'];

    function formBuildCreateUpdateService(fromBuilderService) {
        var formBuildCreateUpdateService = {};
        $.extend(formBuildCreateUpdateService, fromBuilderService);

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
            '</div>',
            hidden: '<div class="form-group">' +
            '<label></label>' +
            '<input type="hidden" class="form-control" id="usr">' +
            '</div>',
        };


        formBuildCreateUpdateService.getButtonsForForm = getButtonsForForm;
        formBuildCreateUpdateService.addOptionsToInputElement = addOptionsToInputElement;
        formBuildCreateUpdateService.addOptionsToSelectElement = addOptionsToSelectElement;

        return formBuildCreateUpdateService;

        function addOptionsToInputElement(el, template) {
            $('input', template).attr($.extend(el.attrs, {
                name: el.name_create,
                ngModel: el.name,
                placeholder: '{{"' + el.attrs.placeholder + '" | translate}}'
            })).val(el.value);

            $('label', template).attr({
                'for': el.value,
                'translate': el.translateLabel
            });

            return template;
        };


        /**
         * Add attributes to input
         * @param el object with data
         * @param template base html template
         * @returns {*} html template
         */
        function addOptionsToSelectElement(el, template) {
            $('select', template).attr($.extend(el.attrs, {
                name: el.name_create,
                ngModel: el.name,
                placeholder: '{{"' + el.attrs.placeholder + '" | translate}}'
            })).val(el.value);

            $('label', template).attr({
                'for': el.value,
                'translate': el.translateLabel
            });

            $('select', template)
                .append($('<option></option>')
                    .attr({
                        'selected': ''
                    })
                    .text('{{"' + el.emptyValue + '" | translate}}'));

            for (var key in el.choices) {
                $('select', template).append($('<option />').val(el.choices[key].value).text(el.choices[key].label));
            }
            $('label', template).attr('translate', el.translatelabel)

            return template;
        };


        function getButtonsForForm(url, id) {
            var templateButtons = '<div class="col-lg-2">' +
                '<fieldset>' +
                '<div class="form-group">' +
                '<button class="btn btn-default mr-sm" id="cancel">' +
                '<em class="icon-refresh mr-sm"></em>' +
                'Cancel' +
                '</button>' +
                '<button class="btn btn-primary" id="create">' +
                '<em class="icon-magnifier mr-sm"></em>' +
                'Create' +
                '</button>' +
                '</div>' +
                '</fieldset>' +
                '</div>';
            templateButtons = $(templateButtons);
            $(templateButtons).find('#create').attr('ng-click', 'send($event, " ' + url + '", closeThisDialog);');
            $(templateButtons).find('#cancel').attr('ng-click', 'closeThisDialog()');

            return templateButtons[0].outerHTML;
        };
    }
})();