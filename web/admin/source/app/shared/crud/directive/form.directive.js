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
        var controller,
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
                choice: select
            };

        return {
            link: link,
            restrict: 'E',
            controller: 'CRUDFormController',
            controllerAs: 'controller',
            scope: {
                struct: '='
            }
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            var struct = scope.struct;
            controller = scope.controller;

            if (!(struct instanceof Object))
                return; // @todo need to create error handler

            if (
                struct.hasOwnProperty('children') &&
                struct.children.constructor === Array &&
                struct.children.length > 0
            ) {
                var form = createForm(struct);

                if (
                    struct.hasOwnProperty('options') &&
                    struct.options instanceof Object
                ) {
                    var options = struct.options;

                    if (options.hasOwnProperty('style')) {
                        if (options.style == 'cols')
                            $('> div.form-group', form).addClass('col-sm-6');
                    }
                }

                return element.replaceWith($compile(form)(scope));
            }

            var stopWatching = scope.$watch('struct', function (struct, prevValue) {
                if (struct === undefined || struct === prevValue)
                    return;
                link(scope, element);
                stopWatching();
            }, true);
        }

        function CAP() {
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

                //if (typeof scope.events.wasCreated === 'function') {
                //    scope.events.wasCreated();
                //}
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

            }
        }

        /**
         * Create form
         *
         * @param {Object} struct
         * @returns {jQuery}
         */
        function createForm(struct) {
            var form = $('<form class="form-horizontal" />').attr({
                name: '',
                novalidate: ''
            });

            form.append(
                struct.children.map(function (struct) {
                    return $('<div class="form-group" />')
                        .append(createChild(struct));
                })
            );

            return form;

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
         * @param {Object} struct
         * @returns {*}
         */
        function createChild(struct) {
            var type = struct.type;

            if (!children.hasOwnProperty(type))
                return console.error('[ERROR]: Try to create form child by type: ' + type + ', but this child doesn\'t defined');

            var child = $('<div />')
                .addClass('row m0')
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
                })(struct)));

            controller.form.children[struct.name] = struct;

            $('input, select', child).attr(angular.extend({
                name: struct.full_name,
                'ng-model': 'controller.form.data.' + struct.name
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
         * Create actions
         *
         * @param {object} data
         * @returns {jQuery}
         */
        function actions(data) {
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