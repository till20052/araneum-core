(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudForm', CRUDFormDirective);

    CRUDFormDirective.$inject = ['CRUDFormLoader', '$compile'];

    /**
     * CRUD From Directive
     *
     * @param CRUDFormLoader
     * @param helper
     * @param $compile
     * @returns {object}
     * @constructor
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
            templateUrl: 'crud/form.html',
            scope: {
                data: '=',
                source: '=',
                options: '='
            }
        };

        function link(scope, element) {
            if(scope.data instanceof Object){
                element.replaceWith($compile(createForm(scope.data))(scope));
            }
            else if(scope.source !== undefined){
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
                scope.$watch('data', function(data){
                    if(data !== undefined){
                        link(scope, element);
                    }
                });
            }

            /*
            var structure = scope.structure;

            if (structure instanceof Object) {
                if (structure.hasOwnProperty('source')) {
                    CRUDFormLoader
                        .setUrl(structure.source)
                        .load({
                            onSuccess: function (structure) {
                                createForm(controller, iElement, structure)(scope);
                                CRUDFormLoader.clearPromise();
                            }
                        });
                }
            }

            scope.$watch(function () {
                return scope.structure instanceof Object;
            }, function (isReady) {
                if (isReady) {
                    createForm(controller, iElement, scope.structure)(scope);
                }
            });
            */
        }

        /**
         * Create form
         */
        function createForm(data) {
            var form = $('<form />');

            for(var id in data.children){
                var type = data.children[id].vars.block_prefixes[1],
                    child = createChild(type, data.children[id].vars);

                form.append(child);
            }

            form.append(createChild('submit', {
                submit: {
                    label: 'admin.general.SAVE'
                },
                cancel: {
                    label: 'admin.general.CANCEL'
                }
            }));

            return form;
            /*
            if (
                structure.hasOwnProperty('vars') &&
                structure.vars instanceof Object
            ) {
                $('>form', placement).attr('name', 'controller.form.validation');
                controller.form = angular.extend(controller.form, {
                    name: structure.vars.name,
                    method: structure.vars.method,
                    action: structure.vars.action
                });
            }

            if (structure.hasOwnProperty('children')) {
                for (var key in structure.children) {
                    if (structure.children.hasOwnProperty(key)) {
                        controller.form.origin[key] = structure.children[key].vars;
                        controller.form.data[key] = structure.vars.value[key];
                        $('*[id="' + key + '"]', createUiControlSection(
                            $('>form', placement),
                            structure.children[key].vars.block_prefixes[1],
                            structure.children[key].vars
                        )).attr({
                            name: structure.children[key].vars.full_name,
                            'ng-model': 'controller.form.data.' + key
                        });
                    }
                }
            }

            createUiControlSection($('>form', placement), 'submit', {
                submit: {
                    label: 'admin.general.SAVE',
                    onClick: controller.form.submit
                },
                cancel: {
                    label: 'admin.general.CANCEL',
                    onClick: controller.form.cancel
                }
            });

            return function (scope) {
                $compile($('>form', placement))(scope);
            };
            */
        }

        /**
         * Create form child
         */
        function createChild(type, data) {
            /* jshint -W106, -W014, eqeqeq: false, validthis: true */
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

            if(type == 'hidden'){
                return child;
            }

            var group = $('<div class="form-group" />').append(child);

            if(type == 'submit'){
                group.addClass('mb0');
            }

            return group;

            /*
            var section = ()[element](angular.extend(
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
                return form.prepend(section);
            }

            section = $('<div class="form-group" />')
                .append(section);

            if (element == 'submit') {
                section.addClass('mb0');
            }

            // @todo need a condition for render different form layout
            //section = $('<div class="col-lg-6" />').append(section);

            return form.append(section);
            */
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