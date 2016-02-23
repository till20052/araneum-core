(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('FormFactory', FormFactory);

    FormFactory.$inject = [];

    /**
     *
     * @constructor
     */
    function FormFactory() {
        return new FormService();
    }

    function FormService() {
        var self = angular.extend(this, {
            create: create
        });

        return self;

        /**
         * Create From
         *
         * @param {{
         *  actions: Object<String, Object>
         *  children: Array<Object>,
         *  view: {
         *      layout: { render: Function }
         *  }
         * }} data
         * @returns {self}
         */
        function create(data) {
            var jFrom = form(data),
                children = [];

            data.children
                .forEach(function (data) {
                    /* jshint -W061, eqeqeq: false */
                    var type = data.type,
                        child = {
                            hidden: hidden,
                            checkbox: checkbox,
                            text: text,
                            select: select
                        }[type](data);

                    if (type == 'hidden')
                        return this.prepend(child);

                    children.push(child);
                }, jFrom);

            // group of buttons
            children.push([
                Object.keys(data.actions)
                    .map(function (key) {
                        return button(data.actions[key]);
                    })
            ]);

            return data.view.layout.render(jFrom, children);
        }
    }

    /**
     * Create jQuery From Element
     *
     * @param {{
     *  name: String,
     *  action: String,
     *  method: String
     * }} data
     * @returns {jQuery}
     */
    function form(data) {
        return $('<form role="form" novalidate class="form-validate" />').attr({
            name: data.name,
            action: data.action,
            method: data.method
        });
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
     * @returns {Array<jQuery>}
     */
    function checkbox(data) {
        return [
            $('<div class="checkbox c-checkbox pt0" />').css('minHeight', '0')
                .append(
                    $('<label />').html(data.label)
                        .prepend(
                            $('<input type="hidden" />'),
                            $('<span class="fa fa-check" />')
                        )
                )
        ];
    }

    /**
     * Create text input
     *
     * @param data
     * @returns {Array<jQuery>}
     */
    function text(data) {
        return [
            $('<label class="control-label mt" />').html(data.label),
            $('<input type="text" class="form-control" />').attr({
                placeholder: data.placeholder
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
            $('<label class="control-label mt" />').html(data.label),
            $('<select class="form-control" />').attr({
                placeholder: data.placeholder
                //'ng-options': 'option.data as option.label for option in controller.form.children.' + data.id + '.choices'
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

        if (data.hasOwnProperty('action'))
            btn.click(data.action);

        return btn;
    }

})();