(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('FormFactory', FormFactory);

    FormFactory.$inject = ['$compile'];

    /**
     *
     * @constructor
     */
    function FormFactory($compile) {
        return new FormService($compile);
    }

    function FormService(compile) {
        var jQFrom,
            self = angular.extend(this, {
                create: create,
                build: build
            });

        return self;

        /**
         * Create From
         *
         * @param {{
         *  children: Array<Object>
         * }} data
         * @returns {self}
         */
        function create(data) {
            data.children.forEach(function (data) {
                /* jshint -W061, eqeqeq: false */
                var type = data.type,
                    child = assign({
                        hidden: hidden,
                        checkbox: checkbox,
                        text: text,
                        select: select
                    }[type](data), {
                        name: data.name
                    });

                if (type == 'hidden')
                    return this.prepend(child);

                this.append(formGroup(child));

            }, (jQFrom = form(data)));
            return self;
        }

        function assign(child, data) {
            console.log($('input, select', formGroup(child)));

            return child;
        }

        /**
         * Compile Form
         *
         * @param scope
         * @returns {jQuery}
         */
        function build(scope) {
            return compile(jQFrom)(scope);
        }
    }

    /**
     * Create hidden input
     *
     * @returns {jQuery}
     */
    function hidden() {
        return $('<input type="hidden" />');
    }

    /**
     * Create checkbox
     *
     * @param data
     * @returns {jQuery}
     */
    function checkbox(data) {
        return $('<div class="col-lg-offset-3 col-lg-9" />').append(
            $('<div class="checkbox c-checkbox pt0" />').css('minHeight', '0')
                .append(
                    $('<label />').html(data.label)
                        .prepend(
                            $('<input type="hidden" />'),
                            $('<span class="fa fa-check" />')
                        )
                )
        );
    }

    /**
     * Create text input
     *
     * @param data
     * @returns {Array<jQuery>}
     */
    function text(data) {
        return [
            $('<label class="col-lg-3 control-label" />').html(data.label),
            $('<div class="col-lg-9" />').append(
                $('<input type="text" class="form-control" />').attr({
                    placeholder: data.placeholder
                })
            )
        ];
    }

    /**
     * Create select
     *
     * @param data
     * @returns {Array<jQuery>}
     */
    function select(data) {
        return [
            $('<label class="col-lg-3 control-label" />').html(data.label),
            $('<div class="col-lg-9" />').append(
                $('<select class="form-control" />').attr({
                    placeholder: data.placeholder
                    //'ng-options': 'option.data as option.label for option in controller.form.children.' + data.id + '.choices'
                })
            )
        ];
    }

    function formGroup(child) {
        return $('<div class="form-group" />').append(
            $('<div class="row" />').append(child)
        );
    }

    /**
     * Create jQuery From Element
     *
     * @param {Object} data
     * @returns {jQuery}
     */
    function form(data) {
        return $('<form role="form" novalidate class="form-validate" />').attr({
            name: data.name,
            action: data.action,
            method: data.method
        });
    }

})();