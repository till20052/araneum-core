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
                source: '=',
                data: '=',
                options: '=',
                events: '='
            }
        };

        function link(scope, element) {
            if (scope.data instanceof Object) {
                ['options', 'events'].forEach(function (key) {
                    if (!(scope[key] instanceof Object)) {
                        scope[key] = {};
                    }
                });

                element.replaceWith(
                    $compile(createForm(scope.data, scope.options, scope.controller.form))(scope)
                );

                if (typeof scope.events.wasCreated === 'function') {
                    scope.events.wasCreated();
                }
            }
            else if (scope.source !== undefined) {
                CRUDFormLoader
                    .setUrl(scope.source)
                    .load({
                        onSuccess: function (data) {
                            link(angular.extend(scope, {data: data}), element);
                            CRUDFormLoader.clearPromise();
                        }
                    });
            }
            else {
                var stopWatching = scope.$watch('data', function (data) {
                    if (data !== undefined) {
                        link(scope, element);
                        stopWatching();
                    }
                });
            }
        }

        /**
         * Create form
         */
        function createForm(data, options, formModel) {
            var form = $('<form class="form-horizontal" />').attr({
                name: 'controller.form.' + data.vars.name,
                novalidate: ''
            });

            for (var id in data.children) {
                var type = data.children[id].vars.block_prefixes[1],
                    child = createChild(type, data.children[id].vars, options);

                // @todo need to set data to model
                //model.data[id] =
                formModel.children[id] = data.children[id].vars;

                $('input, select', child).attr({
                    'ng-model': 'controller.form.data.' + id
                });

                form.append(
                    $('<div class="form-group" />')
                        .addClass(type == 'submit' ? 'mb0' : '')
                        .addClass(
                            options.layout !== undefined && options.layout == 'columns' ?
                                'col-sm-6' : ''
                        )
                        .append(child)
                );
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
        function createChild(type, data) {
            /* jshint eqeqeq: false */
            if (!children.hasOwnProperty(type)) {
                return console.error('[ERROR]: Try to create form child by type: ' + type + ', but this child doesn\'t defined');
            }

            return children[type](angular.extend(
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
        }

        /**
         * Create hidden field
         *
         * @returns {jQuery}
         */
        function inputHidden() {
            return $('<input type="hidden" />');
        }

        /**
         * Create checkbox
         *
         * @param {object} data
         * @returns {jQuery}
         */
        function inputCheckbox(data) {
            return $('<div class="col-lg-offset-3 col-lg-9" />').append(
                $('<div class="checkbox c-checkbox" />').append(
                    $('<label />')
                        .html('{{ "' + data.label + '" | translate }}')
                        .prepend(
                            $('<input type="hidden" />'),
                            $('<span class="fa fa-check" />')
                        )
                )
            );
        }

        /**
         *
         * @param {Object} data
         * @returns {Array<jQuery>}
         */
        function inputText(data) {
            return [
                $('<label class="col-lg-3 control-label" />')
                    .html('{{ "' + data.label + '" | translate }}'),
                $('<div class="col-lg-9" />').append(
                    $('<input class="form-control" />')
                        .attr({
                            type: 'text',
                            placeholder: '{{ "' + data.placeholder + '" | translate }}'
                        })
                )
            ];
        }

        /**
         *
         * @param data
         * @returns {*[]}
         */
        function select(data) {
            return [
                $('<label class="col-lg-3 control-label" />')
                    .html('{{ "' + data.label + '" | translate }}'),
                $('<div class="col-lg-9" />').append(
                    $('<select class="form-control" />')
                        .attr({
                            placeholder: '{{ "' + data.placeholder + '" | translate }}',
                            'ng-options': 'choice.data as choice.label for choice in controller.form.children.' + data.id + '.choices'
                        })
                )
            ];
        }

        /**
         *
         * @param {object} data
         * @returns {jQuery}
         */
        function submit(data) {
            return $('<div class="col-lg-offset-3 col-lg-9" />').append(
                $('<button class="btn btn-primary mr" />')
                    .attr('ng-click', 'controller.submit($event)')
                    .html('{{ "' + data.submit.label + '" | translate }}')
                    .prepend($('<em class="icon-check mr" />')),
                $('<button class="btn btn-default" />')
                    .attr('ng-click', 'controller.click($event)')
                    .html('{{ "' + data.cancel.label + '" | translate }}')
                    .prepend($('<em class="icon-ban mr" />'))
            );
        }
    }

})();