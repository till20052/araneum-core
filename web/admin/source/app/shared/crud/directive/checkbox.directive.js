(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudCheckbox', CRUDCheckboxDirective);

    CRUDCheckboxDirective.$inject = ['$compile', 'supervisor'];

    /**
     * CRUD CheckBox Directive
     *
     * @param $compile
     * @returns {{
     *      link: link,
     *      restrict: string
     * }}
     * @constructor
     */
    function CRUDCheckboxDirective($compile, supervisor) {
        return {
            link: link,
            restrict: 'E'
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            var tr = $(element).parent(),
                input = $('<input type="checkbox" />').change(check),
                div = $('<div class="checkbox c-checkbox mr0" />').append(
                    $('<label />')
                        .append(input)
                        .append($('<span class="fa fa-check mr0" />'))
                );

            tr.addClass('text-center')
                .empty()
                .append($compile(div)(scope));
        }

        /**
         * Event of checkbox state changing
         */
        function check() {
            /* jshint validthis: true */
            var t = {},
                checkbox = $(this),
                selector = 'input[type="checkbox"]',
                checked = checkbox.prop('checked');

            ['head', 'body'].forEach(function (key) {
                t[key] = $(checkbox).parents('t' + key).eq(0);
            });

            if ($(t.head).length !== 0) {
                $(t.head).next()
                    .find(selector)
                    .prop('checked', checked)
                    .change();
            } else {
                $(selector, $(t.body).prev())
                    .prop('checked', $(selector, t.body)
                        .toArray()
                        .every(function (checkbox) {
                            return !!$(checkbox).prop('checked');
                        }));
            }
        }
    }

})();