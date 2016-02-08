(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudCheckbox', CRUDCheckboxDirective);

    CRUDCheckboxDirective.$inject = ['CRUDSupervisor', '$compile'];

    function CRUDCheckboxDirective(supervisor, $compile) {
        return {
            link: link,
            restrict: 'E'
        };

        function link(scope, element) {
            var inputCheckbox = $('<input type="checkbox" />').change(selectRow),
                checkbox = $('<div class="checkbox c-checkbox" />')
                    .append(
                        $('<label />')
                            .append(inputCheckbox)
                            .append($('<span class="fa fa-check" />'))
                    );
            element.replaceWith($compile(checkbox)(scope));
        }

        /**
         *
         * @param event
         */
        function selectRow(event) {
            var checkbox = $(event.target),
                selector = 'input[type="checkbox"]',
                checked = checkbox.prop('checked'),
                t = {};

            ['head', 'body'].forEach(function (key) {
                t[key] = $(checkbox).parents('t' + key).eq(0);
            });

            if ($(t.head).parent('table[datatable]').length !== 0) {
                $(t.head).next()
                    .find(selector)
                    .prop('checked', checked)
                    .change();
            } else {
                supervisor.dataTable.selectRow($(checkbox).parents('tr').eq(0).index(), checked);
                defineCheckboxState($(selector, $(t.body).prev()), $(selector, t.body).toArray());
            }
        }

        /**
         * Define state of checkbox, which in table head placement
         *
         * @param {jQuery} checkbox
         * @param {Array<jQuery>} checkboxes
         */
        function defineCheckboxState(checkbox, checkboxes) {
            var checked = true;
            try {
                checkboxes.forEach(function (checkbox) {
                    if (!$(checkbox).prop('checked'))
                        throw false;
                });
            } catch (state) {
                checked = state;
            } finally {
                $(checkbox).prop('checked', checked);
            }
        }
    }

})();