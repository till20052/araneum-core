(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('Layout', LayoutFactory);

    LayoutFactory.$inject = [];

    /**
     *
     * @constructor
     */
    function LayoutFactory() {
        return function (name) {
            return {
                grid: GridLayout,
                group: GroupLayout
            }[name].apply(null, getArguments(arguments));

            /**
             * Get arguments
             *
             * @param {Object} args
             * @returns {Array}
             */
            function getArguments(args) {
                return Object.keys(args)
                    .splice(1)
                    .map(function (i) {
                        return this[i];
                    }, args);
            }
        };
    }

    function GridLayout(colsCount) {
        var groupLayout = new GroupLayout(2, [3, 9]);

        return {
            render: render
        };

        /**
         *
         * @param {jQuery} form
         * @param {Array} children
         */
        function render(form, children) {
            return form.append($('<div class="row" />').append(
                children.map(function (child) {
                    return $('<div class="col-lg-' + parseInt(12 / colsCount) + '" />').append(
                        groupLayout.render(child)
                    );
                })
            ));
        }
    }

    function GroupLayout(colsCount, ratio) {
        return {
            render: render
        };

        /**
         *
         * @param {Array} children
         * @returns {jQuery}
         */
        function render(children) {
            return $('<div class="form-group" />').append(
                $('<div class="row" />').append(
                    children.map(function (child, i) {
                        var div = $('<div />').append(child);

                        if (children.length > 1)
                            div.addClass('col-lg-' + ratio[i]);
                        else if (children.length > 0)
                            div.addClass('col-lg-offset-' + ratio[0])
                                .addClass('col-lg-' + ratio[1]);

                        return div;
                    })
                )
            );
        }
    }

})();