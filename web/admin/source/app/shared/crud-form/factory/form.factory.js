(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('Form', FormFactory);

    FormFactory.$inject = [];

    /**
     *
     * @constructor
     */
    function FormFactory() {
        return function (data) {
            return render(data);
        };

        /**
         * Get children
         *
         * @param {{
         *  children: Array<Object>,
         *  data: Object
         * }} form
         * @returns {Array}
         */
        function children(form) {
            return form.children.map(function (child, i) {
                /* jshint -W061, eqeqeq: false */
                return {
                    hidden: hidden,
                    checkbox: checkbox,
                    text: text,
                    select: select
                }[child.type](angular.extend({
                    index: i,
                    model: 'form.data().' + child.id
                }, child));
            });
        }

        /**
         * Get actions
         *
         * @param data
         * @returns {Array}
         */
        function actions(data) {
            return Object.keys(data.actions)
                .map(function (key, i) {
                    var btn = button(data.actions[key]);
                    if (i !== 0)
                        btn.addClass('ml');
                    return btn;
                });
        }

        /**
         * Render form
         *
         * @param data
         * @returns {jQuery}
         */
        function render(data) {
            return form(data).append(
                data.getView().layout.render(children(data).concat([[actions(data)]]))
            );
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
        return $('<form class="form-validate" role="form" novalidate />').attr({
            name: data.name
        });
    }

    /**
     * Create hidden input
     *
     * @param data
     * @returns {jQuery}
     */
    function hidden(data) {
        return $('<input type="hidden" />').attr('ng-model', data.model);
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
                            $('<input type="hidden" />').attr({
                                name: data.name,
                                'ng-model': data.model
                            }),
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

        if (data.hasOwnProperty('action'))
            btn.data('action', data.action)
                .click(function () {
                    $(this).data('action')
                        .call($(this), angular.element($(this)).scope());
                });

        return btn;
    }

})();