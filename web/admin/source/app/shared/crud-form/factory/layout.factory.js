(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('Layout', LayoutFactory);

    /**
     * Layout Factory
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

    /**
     * Grid Form Layout
     *
     * @param {Number} colsCount
     * @returns {{render: render}}
     * @constructor
     */
    function GridLayout(colsCount) {
        var groupLayout = new GroupLayout([3, 9]);

        return {
            render: render
        };

        /**
         * Render layout
         *
         * @param {jQuery} form
         * @param {Array} children
         * @returns {jQuery}
         */
        function render(form, children) {
            return form.append($('<div class="row" />').append(
                children.map(function (child, i) {
                    var fg = $(groupLayout.render(undefined, [child])[0]).addClass('col-lg-' + parseInt(12 / colsCount));
                    if(i !== children.length - 1)
                        fg.removeClass('mb0');
                    return fg;
                })
            ));
        }
    }

    /**
     * Group Form Layout
     *
     * @param {Array} ratio
     * @returns {{render: render}}
     * @constructor
     */
    function GroupLayout(ratio) {
        return {
            render: render
        };

        /**
         * Render layout
         *
         * @param {undefined|jQuery} form
         * @param {Array<jQuery|Array>} children
         * @returns {jQuery|Array}
         */
        function render(form, children) {
            var layout = children.map(function (child, i) {
                /* jshint eqeqeq: false */

                if (child instanceof jQuery) {
                    if (child.attr('type') == 'hidden') {
                        form.prepend(child);
                        return;
                    }

                    child = [child];
                }

                var fg = formGroup(child.map(function (element, i) {
                    return bsCol(element, i, child.length === 1);
                }));

                if (i === children.length - 1)
                    fg.addClass('mb0');

                return fg;
            });

            if (form !== undefined)
                return form.append(layout);

            return layout;
        }

        /**
         * Create Form Group
         *
         * @param {jQuery} child
         * @returns {jQuery}
         */
        function formGroup(child) {
            return $('<div class="form-group" />').append(
                $('<div class="row" />').append(child)
            );
        }

        /**
         * Create Bootstrap Column
         *
         * @param {jQuery} child
         * @param {Number} index
         * @param {Boolean} offset
         * @returns {jQuery}
         */
        function bsCol(child, index, offset) {
            return $('<div />').append(child)
                .addClass(
                    offset ?
                    'col-lg-offset-' + ratio[index] + ' col-lg-' + ratio[index + 1] :
                    'col-lg-' + ratio[index]
                );
        }
    }

})();