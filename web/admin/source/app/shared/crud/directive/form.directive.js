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
                config: '=crudForm'
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

                    if (
                        form.hasOwnProperty('vars') &&
                        form.vars instanceof Object
                    ) {
                        $('>form', iElement)
                            .attr({
                                name: 'controller.form.validation'
                            });
                        controller.form = {
                            name: form.vars.name,
                            method: form.vars.method,
                            action: form.vars.action,
                            data: {}
                        };
                    }

                    if (form.hasOwnProperty('children')) {
                        for (var key in form.children) {
                            if (form.children.hasOwnProperty(key)) {
                                $('*[name="' + form.children[key].vars.full_name + '"]', build(
                                    $('>form', iElement),
                                    form.children[key].vars.block_prefixes[1],
                                    form.children[key].vars,
                                    form.vars.value
                                )).attr({
                                    'ng-model': 'controller.form.data.' + key
                                });
                            }
                        }
                    }

                    build($('>form', iElement), 'submit', {
                        submit: {
                            label: 'admin.general.SAVE',
                            onClick: scope.controller.submit
                        },
                        cancel: {
                            label: 'admin.general.CANCEL',
                            onClick: scope.controller.cancel
                        }
                    });

                    $compile($('>form', iElement))(scope);
                }
            });
        }

        function build(form, element, config, data) {
            /* jshint -W106, -W014, eqeqeq: false, validthis: true */
            var uiRow = ({
                hidden: inputHidden,
                checkbox: inputCheckbox,
                text: inputText,
                choice: select,
                submit: submit
            })[element](angular.extend(
                (function (ext) {
                    ['full_name:name', 'label']
                        .forEach(function (key) {
                            var map = (function (str) {
                                var s = String(str).split(':');
                                return {
                                    a: s[0],
                                    b: s[1] || s[0]
                                };
                            })(key);
                            if (!config.hasOwnProperty(map.a))
                                return;
                            ext[map.b] = config[map.a];
                        });
                    return ext;
                })({}),
                (function (ext) {
                    if (
                        data instanceof Object &&
                        data.hasOwnProperty(config.name)
                    ) {
                        ext.value = data[config.name];
                    }
                    return ext;
                })({}),
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
                    if (config.hasOwnProperty('choices')) {
                        ext.empty = config.empty_value;
                        ext.options = $.map(config.choices, function (option) {
                            return {
                                value: option.data,
                                text: option.label
                            };
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
            return $('<input type="hidden" />')
                .attr({
                    name: config.name
                });
        }

        function inputCheckbox(config) {
            return $('<div class="col-lg-offset-3 col-lg-9" />').append(
                $('<div class="checkbox c-checkbox" />').append(
                    $('<label />')
                        .html('{{ "' + config.label + '" | translate }}')
                        .prepend(
                            $('<input />')
                                .attr({
                                    type: 'checkbox',
                                    checked: config.value
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
                            type: 'text',
                            name: config.name,
                            placeholder: '{{ "' + config.placeholder + '" | translate }}'
                        })
                        .val(config.value)
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
                            name: config.name,
                            placeholder: '{{ "' + config.placeholder + '" | translate }}'
                        })
                        .append(
                            $('<option />')
                                .html('{{ "' + config.empty + '" | translate }}')
                        )
                        .append(
                            $.map(config.options, function (option) {
                                return $('<option value="' + option.value + '" />')
                                    .html('{{ "' + option.text + '" | translate }}');
                            })
                        )
                        .val(config.value)
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