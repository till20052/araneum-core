(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudForm', CRUDFormDirective);

    CRUDFormDirective.$inject = ['CRUDFormLoader', 'RouteHelpers', '$compile'];

    function CRUDFormDirective(CRUDFormLoader, helper, $compile) {
        return {
            link: link,
            controller: 'CRUDFormController',
            controllerAs: 'controller',
            restrict: 'A',
            replace: true,
            templateUrl: helper.basepath('crud/form.html'),
            scope: {
                CRUDForm: '=crudForm'
            }
        };

        function link(scope, iElement, iAttr, controller) {
            CRUDFormLoader.load({
                onSuccess: /**
                 * @param {{
                 *      children: object
                 *      vars: object
                 * }} form
                 */
                    function (form) {
                    /* jshint -W106 */

                    controller.form = angular.extend(controller.form, {
                        origin: {},
                        data: {}
                    });

                    if (
                        form.hasOwnProperty('vars') &&
                        form.vars instanceof Object
                    ) {
                        $('>form', iElement).attr('name', 'controller.form.validation');
                        controller.form = angular.extend(controller.form, {
                            name: form.vars.name,
                            method: form.vars.method,
                            action: form.vars.action
                        });
                    }

                    if (form.hasOwnProperty('children')) {
                        for (var key in form.children) {
                            if (form.children.hasOwnProperty(key)) {
                                controller.form.origin[key] = form.children[key].vars;
                                controller.form.data[key] = form.vars.value[key];
                                $('*[id="' + key + '"]', build(
                                    $('>form', iElement),
                                    form.children[key].vars.block_prefixes[1],
                                    form.children[key].vars
                                )).attr({
                                    name: form.children[key].vars.full_name,
                                    'ng-model': 'controller.form.data.' + key
                                });
                            }
                        }
                    }

                    build($('>form', iElement), 'submit', {
                        submit: {
                            label: 'admin.general.SAVE',
                            onClick: controller.form.submit
                        },
                        cancel: {
                            label: 'admin.general.CANCEL',
                            onClick: controller.form.cancel
                        }
                    });

                    $compile($('>form', iElement))(scope);
                }
            });
        }

        function build(form, element, config) {
            /* jshint -W106, -W014, eqeqeq: false, validthis: true */
            var uiRow = ({
                hidden: inputHidden,
                checkbox: inputCheckbox,
                text: inputText,
                choice: select,
                submit: submit
            })[element](angular.extend(
                {
                    uid: config.name,
                    label: config.label
                },
                (function (ext) {
                    if (config.attr instanceof Object) {
                        ['translateLabel', 'placeholder'].forEach(function (key) {
                            if (!config.attr.hasOwnProperty(key))
                                return;
                            ext[key] = config.attr[key];
                        });
                    }
                    return ext;
                })({}),
                (function (ext) {
                    if (element == 'submit') {
                        ext = config;
                    }
                    return ext;
                })({})
            ));

            if (element == 'hidden') {
                return form.prepend(uiRow);
            }

            uiRow = $('<div class="form-group" />')
                .append(uiRow);

            if (element == 'submit') {
                uiRow.addClass('mb0');
            }

            return form.append(uiRow);
        }

        function inputHidden(config) {
            return $('<input />').attr({
                id: config.uid,
                type: 'hidden'
            });
        }

        function inputCheckbox(config) {
            return $('<div class="col-lg-offset-3 col-lg-9" />').append(
                $('<div class="checkbox c-checkbox" />').append(
                    $('<label />')
                        .html('{{ "' + config.label + '" | translate }}')
                        .prepend(
                            $('<input />').attr({
                                id: config.uid,
                                type: 'checkbox'
                            }),
                            $('<span class="fa fa-check" />')
                        )
                )
            );
        }

        function inputText(config) {
            return [
                $('<label class="col-lg-3 control-label" />')
                    .html('{{ "' + config.label + '" | translate }}'),
                $('<div class="col-lg-9" />').append(
                    $('<input class="form-control" />')
                        .attr({
                            id: config.uid,
                            type: 'text',
                            placeholder: '{{ "' + config.placeholder + '" | translate }}'
                        })
                )
            ];
        }

        function select(config) {
            return [
                $('<label class="col-lg-3 control-label" />')
                    .html('{{ "' + config.label + '" | translate }}'),
                $('<div class="col-lg-9" />').append(
                    $('<select class="form-control" />')
                        .attr({
                            id: config.uid,
                            placeholder: '{{ "' + config.placeholder + '" | translate }}',
                            'ng-options': 'choice.data as choice.label for choice in controller.form.origin.' + config.uid + '.choices'
                        })
                )
            ];
        }

        function submit(config) {
            return $('<div class="col-lg-offset-3 col-lg-9" />').append(
                $('<button class="btn btn-primary mr" />')
                    .data('onButtonClick', config.submit.onClick)
                    .click(clickEventHandler)
                    .html('{{ "' + config.submit.label + '" | translate }}')
                    .prepend(
                        $('<em class="icon-check mr" />')
                    ),
                $('<button class="btn btn-default" />')
                    .data('onButtonClick', config.cancel.onClick)
                    .click(clickEventHandler)
                    .html('{{ "' + config.cancel.label + '" | translate }}')
                    .prepend(
                        $('<em class="icon-ban mr" />')
                    )
            );

            function clickEventHandler() {
                /* jshint validthis: true, eqeqeq: false */
                if (
                    $(this).data('onButtonClick') !== undefined &&
                    typeof $(this).data('onButtonClick') == 'function'
                ) {
                    $(this).data('onButtonClick')();
                }
            }

        }
    }

})();