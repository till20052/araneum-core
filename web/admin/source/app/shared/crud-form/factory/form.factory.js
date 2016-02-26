(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('Form', FormFactory);

    /**
     * Form Factory
     *
     * @returns {getForm}
     * @constructor
     */
    function FormFactory() {
        return getForm;

        /**
         * Get form
         *
         * @param {FormHandler} $
         * @returns {jQuery}
         */
        function getForm($) {
            return form($.name)
                .append(getChildren($.children))
                .append(getActionBar($.actionBar));
        }

        /**
         * Get children
         *
         * @param {Array<Object>} children
         * @returns {Array}
         */
        function getChildren(children) {
            return $('<children />').append(
                children.map(function (child, i) {
                    /* jshint -W061, eqeqeq: false */
                    return $('<child />').append({
                        hidden: hidden,
                        checkbox: checkbox,
                        text: text,
                        select: select
                    }[child.type](angular.extend({
                        index: i,
                        model: 'form.data().' + child.id
                    }, child)));
                })
            );
        }

        /**
         * Get actions
         *
         * @param {Array} actionBar
         * @returns {Array}
         */
        function getActionBar(actionBar) {
            return $('<action-bar />').append(
                actionBar.map(function (data, i) {
                    var btn = button(data);
                    if (i !== 0)
                        btn.addClass('ml');
                    return btn;
                })
            );
        }
    }

    /**
     * Create jQuery From Element
     *
     * @param {{String}} name
     * @returns {jQuery}
     */
    function form(name) {
        return $('<form class="form-validate" role="form" novalidate />').attr('name', name);
    }

    /**
     * Create hidden input
     *
     * @param data
     * @returns {jQuery}
     */
    function hidden(data) {
        return $('<input type="hidden" />').attr({
            name: data.name,
            'ng-model': data.model
        });
    }

    /**
     * Create checkbox
     *
     * @param data
     * @returns {jQuery}
     */
    function checkbox(data) {
        return $('<div class="checkbox c-checkbox pt0" />').css('minHeight', '0')
            .append(
                $('<label />').html(data.label)
                    .prepend(
                        $('<input type="checkbox" />').attr({
                            name: data.name,
                            'ng-model': data.model
                        }),
                        $('<span class="fa fa-check" />')
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
            $('<label class="control-label mt-sm" />').html(data.label),
            $('<input type="text" class="form-control" />').attr({
                name: data.name,
                placeholder: data.placeholder,
                'ng-model': data.model
            })
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
            $('<label class="control-label mt-sm" />').html(data.label),
            $('<select class="form-control" />').attr({
                name: data.name,
                'ng-model': data.model,
                'ng-options': 'option.id as option.text | translate for option in form.getChild(' + data.index + ').options'
            })
        ];
    }

    /**
     * Create button
     *
     * @param data
     * @returns {jQuery}
     */
    function button(data) {
        var btn = $('<button class="btn btn-default" />').html('{{ "' + data.title + '" | translate }}');

        if (data.hasOwnProperty('class'))
            btn.removeClass('btn-default')
                .addClass('btn-' + data.class);

        if (data.hasOwnProperty('icon'))
            btn.prepend(
                $('<em class="mr-sm" />').addClass(data.icon)
            );

        if (data.hasOwnProperty('$$'))
            btn.data('$$', data.$$).click(function () {
                var form = angular.element(this).scope().form,
                    $$ = $(this).data('$$');

                form.event($$).invoke(form);
            });

        return btn;
    }

})();