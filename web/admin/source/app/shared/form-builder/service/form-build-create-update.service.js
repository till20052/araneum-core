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
            '<label></label>' +
            '<input type="text" class="form-control" />' +
            '</div>',
            email: '<div class="form-group">' +
            '<label></label>' +
            '<input type="email" class="form-control" />' +
            '</div>',
            datetime: '<div class="form-group">' +
            '<label></label>' +
            '<input type="date" class="form-control" />' +
            '</div>',
            choice: '<div class="form-group">' +
            '<label></label>' +
            '<select class="form-control"></select>' +
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
                placeholder: '{{"' + el.attrs.placeholder + '" | translate}}',
                value: el.value
            }));

            $('label', template).attr({
                'for': el.value,
                'translate': el.translateLabel
            }).text(el.label);
            
            return template;
        }


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
            }));

            $('label', template).attr({
                'for': el.value,
                'translate': el.translateLabel
            }).text(el.label);
            
            $('select', template)
                .append($('<option></option>')
                    .attr({'selected': 'selected'})
                    .text('{{"' + el.emptyValue + '" | translate}}'));

            for (var key in el.choices) {
                var isChecked = false;

                if (el.value === el.choices[key].value) {
                    isChecked = true;
                    $('option', template).removeAttr('selected');
                }

                $('select', template).append($('<option />').attr({'selected': isChecked}).val(el.choices[key].value).text(el.choices[key].label));
            }
            $('label', template).attr('translate', el.translatelabel);

            return template;
        }


        function getButtonsForForm(url, id) {
            var templateButtons = '<div class="form-group">' +
                '<div class="col-sm-offset-2 col-sm-10">' +
                '<button class="btn btn-default mr-sm" id="cancel" translate="admin.general.CANCEL">' +
                '<em class="icon-refresh mr-sm"></em>' +
                'Cancel' +
                '</button>' +
                '<button class="btn btn-primary" id="create">' +
                '<em class="icon-magnifier mr-sm"></em>' +
                '{{ actionName | translate}}' +
                '</button>' +
                '</div>' +
                '</div>';

            templateButtons = $(templateButtons);
            $(templateButtons).find('#create').attr('ng-click', 'send($event, " ' + url + '", closeThisDialog);');
            $(templateButtons).find('#cancel').attr('ng-click', 'closeThisDialog()');

            return templateButtons[0].outerHTML;
        }
    }
})();