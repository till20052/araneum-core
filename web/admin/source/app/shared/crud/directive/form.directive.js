(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudForm', CRUDFormDirective);

    CRUDFormDirective.$inject = ['$compile', 'supervisor'];

    /**
     * CRUD From Directive
     */
    function CRUDFormDirective($compile, supervisor) {
        /* jshint -W106, eqeqeq: false */
        var controller = {},
        // @todo need to move this object to separated service, which will be called bootstrap-helper
            bootstrap = {
                col: (function (self) {
                    ['left', 'right', 'offsetLeft', 'offsetRight']
                        .forEach(function (getter) {
                            self = Object.defineProperty(self, getter, {
                                get: function () {
                                    return (function (parts) {
                                        var tokens = [
                                            'col',
                                            self.type,
                                            self.width[
                                                (typeof parts[1] != 'undefined' ?
                                                    parts[1].toLowerCase() :
                                                    parts[0])
                                                ]
                                        ];
                                        if (parts[0] == 'offset')
                                            tokens.splice(2, 0, parts[0]);
                                        return tokens.join('-');
                                    })(getter.split(/(?=[A-Z0-9])/));
                                }
                            });
                        });
                    return self;
                })({
                    type: 'lg',
                    width: {
                        left: 3,
                        right: 9
                    }
                })
            },
            children = {
                hidden: inputHidden,
                checkbox: inputCheckbox,
                text: inputText,
                choice: select,
                controls: controls
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

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            if (scope.data instanceof Object) {
                if (scope.hasOwnProperty('controller'))
                    controller = scope.controller;

                ['options', 'events'].forEach(function (key) {
                    if (!(scope[key] instanceof Object)) {
                        scope[key] = {};
                    }
                });

                element.replaceWith(
                    $compile(createForm(scope.data, scope.options))(scope)
                );

                if (typeof scope.events.wasCreated === 'function') {
                    scope.events.wasCreated();
                }
            }
            else if (scope.source !== undefined) {
                supervisor
                    .loader('form')
                    .load(scope.source)
                    .onLoaded({
                        onSuccess: function (data) {
                            link(angular.extend(scope, {data: data}), element);
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
         *
         * @param {{
         *      children: Object.<String, Object>,
         *      vars: Object
         * }} data
         * @param {Object} options
         * @returns {jQuery}
         */
        function createForm(data, options) {
            var form = $('<form class="form-horizontal" />').attr({
                name: 'controller.form.' + data.vars.name,
                novalidate: ''
            });

            data.children = angular.extend(data.children, {
                controls: {
                    submit: {
                        class: 'btn-primary',
                        icon: 'icon-check',
                        label: 'admin.general.SAVE',
                        click: 'submit'
                    },
                    cancel: {
                        icon: 'icon-close',
                        label: 'admin.general.CANCEL',
                        click: 'cancel'
                    }
                }
            });

            for (var id in data.children) {
                if (!data.children.hasOwnProperty(id))
                    continue;

                /**
                 * @typedef {{
                 *      block_prefixes: Array<String>
                 * }}
                 */
                var config = data.children[id].hasOwnProperty('vars') ?
                        data.children[id].vars :
                        data.children[id],
                    type = config.hasOwnProperty('block_prefixes') ?
                        config.block_prefixes[1] :
                        id,
                    child = createChild(type, config);

                if (type == 'hidden') {
                    form.prepend(child);
                    continue;
                }

                var formGroup = $('<div class="form-group" />').append(child);

                if (type == 'controls')
                    formGroup.addClass('mb0');

                if (
                    options.hasOwnProperty('layout') &&
                    options.layout == 'cols'
                )
                    formGroup.addClass('col-sm-6');

                form.append(formGroup);
            }

            return form;
        }

        /**
         * Create form child
         *
         * @param type
         * @param data
         * @returns {*}
         */
        function createChild(type, data) {
            if (!children.hasOwnProperty(type)) {
                return console.error('[ERROR]: Try to create form child by type: ' + type + ', but this child doesn\'t defined');
            }

            var child = $('<div />').addClass('row m0')
                .append(children[type]((function (data) {
                    return angular.forEach(
                        (data = (function (data) {
                            return angular.extend(
                                (function (ext) {
                                    ['name:id', 'label', 'placeholder'].forEach(function (key) {
                                        var linkedKeys = controller.linkKeys(key.split(':'));
                                        if (!data.hasOwnProperty(linkedKeys.from))
                                            return;
                                        ext[linkedKeys.to] = data[linkedKeys.from];
                                    });
                                    return ext;
                                })({}),
                                (function (ext) {
                                    if (data.attr instanceof Object) {
                                        ['translateLabel:label', 'placeholder'].forEach(function (key) {
                                            var linkedKeys = controller.linkKeys(key.split(':'));
                                            if (!data.attr.hasOwnProperty(linkedKeys.from))
                                                return;
                                            ext[linkedKeys.to] = data.attr[linkedKeys.from];
                                        });
                                    }
                                    return ext;
                                })({}),
                                (function (ext) {
                                    if (type == 'controls') {
                                        ext = data;
                                    }
                                    return ext;
                                })({})
                            );
                        })(data)),
                        function (value, key) {
                            if (['label', 'placeholder'].indexOf(key) !== -1)
                                this[key] = '{{ "' + value + '" | translate }}';
                        },
                        data
                    );
                })(data)));

            controller.form.children[data.name] = data;

            $('input, select', child).attr(angular.extend({
                name: data.full_name,
                'ng-model': 'controller.form.data.' + data.name
            }));

            return type == 'hidden' ?
                $('>input', child) :
                child;
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
            return $('<div />')
                .addClass([bootstrap.col.offsetLeft, bootstrap.col.right].join(' '))
                .append(
                    $('<div class="checkbox c-checkbox pt0" />')
                        .css('minHeight', '0')
                        .append(
                            $('<label />')
                                .html(data.label)
                                .prepend(
                                    $('<input type="hidden" />'),
                                    $('<span class="fa fa-check" />')
                                )
                        )
                );
        }

        /**
         * Create input text
         *
         * @param {Object} data
         * @returns {Array<jQuery>}
         */
        function inputText(data) {
            return [
                $('<label class="control-label" />')
                    .addClass(bootstrap.col.left)
                    .html(data.label),
                $('<div />')
                    .addClass(bootstrap.col.right)
                    .append(
                        $('<input class="form-control" />')
                            .attr({
                                type: 'text',
                                placeholder: data.placeholder
                            })
                    )
            ];
        }

        /**
         * Create select
         *
         * @param data
         * @returns {*[]}
         */
        function select(data) {
            return [
                $('<label class="control-label" />')
                    .addClass(bootstrap.col.left)
                    .html(data.label),
                $('<div />')
                    .addClass(bootstrap.col.right)
                    .append(
                        $('<select class="form-control" />')
                            .attr({
                                placeholder: data.placeholder,
                                'ng-options': 'option.data as option.label for option in controller.form.children.' + data.id + '.choices'
                            })
                    )
            ];
        }

        /**
         * Create controls
         *
         * @param {object} data
         * @returns {jQuery}
         */
        function controls(data) {
            var buttons = [],
                keys = Object.keys(data);
            return $('<div />')
                .addClass([bootstrap.col.offsetLeft, bootstrap.col.right].join(' '))
                .append(
                    angular.forEach(data, function (data, key) {
                        console.log(data);
                        var button = $('<button class="btn btn-default" />')
                            .click(data.click)
                            .html('{{ "' + data.label + '" | translate }}');

                        if (data.hasOwnProperty('class'))
                            button.removeClass('btn-default')
                                .addClass(data.class);

                        if (keys.indexOf(key) + 1 < keys.length)
                            button.addClass('mr');

                        if (data.hasOwnProperty('icon'))
                            button.prepend($('<em class="mr" />').addClass(data.icon));

                        this.push(button);
                    }, buttons) && buttons
                );
        }
    }

})();