(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudForm', CRUDFormDirective);

    CRUDFormDirective.$inject = ['CRUDFormLoader', '$compile'];

    /**
     * CRUD From Directive
     */
    function CRUDFormDirective(CRUDFormLoader, $compile) {
        var children = {
            hidden: inputHidden,
            checkbox: inputCheckbox,
            text: inputText,
            choice: select,
            submit: submit
        };

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDFormController',
            controllerAs: 'controller',
            scope: {
                data: '=',
                source: '=',
                options: '='
            }
        };

        function link(scope, element) {
            if (scope.data instanceof Object) {
                if (scope.options === undefined) {
                    scope.options = {};
                }

                element.replaceWith(
                    $compile(createForm(scope.data, scope.options))(scope)
                );
            }
            else if (scope.source !== undefined) {
                //CRUDFormLoader
                //    .setUrl(structure.source)
                //    .load({
                //        onSuccess: function (structure) {
                //
                //            CRUDFormLoader.clearPromise();
                //        }
                //    });
            }
            else {
                scope.$watch('data', function (data) {
                    if (data !== undefined) {
                        link(scope, element);
                    }
                });
            }
        }

        /**
         * Create form
         */
        function createForm(data, options) {
            var form = $('<form />').attr({
                name: data.vars.name,
                novalidate: ''
            });

            for (var id in data.children) {
                var type = data.children[id].vars.block_prefixes[1],
                    child = createChild(type, data.children[id].vars, options);

                $('input, select', child).attr({
                    'ng-model': 'controller.form.data.' + id
                });

                form.append(child);
            }

            form.append(createChild('submit', {
                submit: {
                    label: 'admin.general.SAVE'
                },
                cancel: {
                    label: 'admin.general.CANCEL'
                }
            }, options));

            return form;
        }

        /**
         * Create form child
         */
        function createChild(type, data, options) {
            /* jshint eqeqeq: false */
            if (!children.hasOwnProperty(type)) {
                return console.error('[ERROR]: Try to create form child by type: ' + type + ', but this child doesn\'t defined');
            }

            var child = children[type](angular.extend(
                {
                    id: data.name,
                    label: data.label
                },
                (function (ext) {
                    if (data.attr instanceof Object) {
                        ['translateLabel', 'placeholder'].forEach(function (key) {
                            if (!data.attr.hasOwnProperty(key))
                                return;
                            ext[key] = data.attr[key];
                        });
                    }
                    return ext;
                })({}),
                (function (ext) {
                    if (type == 'submit') {
                        ext = data;
                    }
                    return ext;
                })({})
            ));

            if (type == 'hidden') {
                return child;
            }

            var group = $('<div class="form-group" />').append(child);

            if (type == 'submit') {
                group.addClass('mb0');
            }

            if (
                options.hasOwnProperty('layout') &&
                options.layout instanceof Object
            ) {
                if (options.layout.hasOwnProperty('class')) {
                    $(group).addClass(options.layout.class);
                }
            }

            return group;
        }

        /**
         * Create input element with type eq hidden
         *
         * @param config
         * @returns {jQuery}
         */
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