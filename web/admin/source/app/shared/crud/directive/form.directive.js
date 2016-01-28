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
            controllerAs: 'vm',
            restrict: 'A',
            replace: true,
            templateUrl: helper.basepath('crud/form.html'),
            scope: {
                source: '=crudForm'
            }
        };

        function link(scope, iElement) {
            CRUDFormLoader.load({
                onSuccess: function (form) {
                    /* jshint -W106 */
                    if (form.hasOwnProperty('children')) {
                        for (var key in form.children) {
                            if (!form.children.hasOwnProperty(key))
                                return;
                            append(
                                $('>form', iElement),
                                form.children[key].vars.block_prefixes[1],
                                form.children[key].vars
                            );
                        }
                    }

                    $compile($('>form', iElement))(scope);
                }
            });
        }

        function append(form, element, config) {
            /* jshint -W106, -W014, eqeqeq: false, validthis: true */
            var uiRow = ({
                hidden: inputHidden,
                checkbox: inputCheckbox,
                text: inputText,
                choice: select
            })[element](angular.extend(
                {
                    name: config.full_name,
                    value: config.value,
                    label: config.attr.translateLabel
                },
                (function (o) {
                    if (config.attr.hasOwnProperty('placeholder'))
                        return {
                            placeholder: config.attr.placeholder
                        };
                    return o;
                })({}),
                (function (o) {
                    if (config.hasOwnProperty('choices'))
                        return {
                            empty: config.empty_value,
                            options: $.map(config.choices, function (option) {
                                return {
                                    value: option.data,
                                    text: option.label
                                };
                            })
                        };
                    return o;
                })({})
            ));

            if (element == 'hidden') {
                return form.prepend(uiRow);
            }

            console.log(uiRow);

            uiRow = $('<div class="form-group" />')
                .append(uiRow);

            form.append(uiRow);
        }

        function inputHidden(config) {
            return $('<input type="hidden" name="' + config.name + '" />');
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
                                    checked: false
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
                )
            ];
        }
    }

})();