(function () {
    'use strict';

    angular
        .module('crud.datatable')
        .factory('DTHandler', DTHandlerFactory);

    DTHandlerFactory.$inject = ['DataTable', 'EventsHandler', 'DTOptionsBuilder'];

    /**
     *
     * @returns {DTHandler}
     * @constructor
     */
    function DTHandlerFactory(DataTable, EventsHandler, DTOptionsBuilder) {
        return DTHandler;

        /**
         * DataTable Handler
         *
         * @param {Object} manifest
         * @param {{
         *  compile: Function
         * }} external
         * @returns {*}
         * @constructor
         */
        function DTHandler(manifest, external) {
            var $this = angular.extend(this, external, {
                instance: {},
                columns: [],
                source: '',
                options: {},
                selectRow: selectRow,
                getSelectedRows: getSelectedRows,
                refresh: refresh,
                build: build
            });

            return activate();

            /**
             * Activation
             *
             * @returns {*}
             */
            function activate() {
                if (manifest.hasOwnProperty('events') && manifest.events instanceof Object) {
                    if (manifest.hasOwnProperty('actions') && manifest.actions instanceof Object)
                        angular.extend(manifest.events, manifest.actions);

                    angular.extend($this, new EventsHandler(manifest.events));
                }

                $this.options = DTOptionsBuilder.newOptions()
                    .withOption('processing', true)
                    .withOption('serverSide', true)
                    .withOption('fnServerData', fnServerData)
                    .withPaginationType('full_numbers');

                Object.keys($this)
                    .forEach(function (key) {
                        if (key === 'event' || typeof this[key] !== 'function')
                            return;
                        manifest[key] = this[key];
                    }, $this);

                return $this;

                /**
                 * Get data for datatable from server
                 *
                 * @param source
                 * @param data
                 * @param callback
                 * @param settings
                 * @private
                 */
                function fnServerData(source, data, callback, settings) {
                    console.log(source);
                    settings.jqXHR = $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        url: source,
                        data: data,
                        success: function (data) {
                            callback(angular.extend(data, {
                                aaData: $.map(data.aaData, function (cols) {
                                    return [cols.splice(0, cols.length - 1).concat(['<dropdown />', '<checkbox />'])];
                                })
                            }));
                            $this.compile(
                                $('> tbody > tr', settings.nTable)
                                    .each(function () {
                                        $(this).data('$$', {
                                            id: parseInt($('> td:first-child', this).text()),
                                            selected: false
                                        });
                                    })
                                    .find('> td > *')
                                    .filter(function () {
                                        return /^(dropdown|checkbox)$/ig.test($(this).prop('tagName'));
                                    })
                            );
                            $this.event('onRenderRows').invoke($this);
                        },
                        error: function (response) {
                            //if (response.status !== 401)
                            //    return;
                            //$state.go('login');
                        }
                    });
                }
            }

            /**
             * Refresh DataTable
             */
            function refresh() {
                var dt = $this.instance.DataTable;

                dt.ajax.reload();

            }

            /**
             * Select row
             *
             * @param {jQuery} row
             * @param {Boolean} state
             */
            function selectRow(row, state) {
                if (!($(row).data('$$') instanceof Object))
                    throw console.error('[ERROR]: Cannot set row selection state', row);
                row.data('$$').selected = !!state;
                $this.event('onSelectRow').invoke($this);
            }

            /**
             * Get Selected Rows
             *
             * @returns {Array<jQuery>}
             */
            function getSelectedRows() {
                return $('> tbody > tr', $this.instance.dataTable).filter(function () {
                    return $(this).data('$$').selected;
                }).toArray();
            }

            /**
             * Build DataTable
             *
             * @param data
             */
            function build(data) {
                delete manifest.build;
                (angular.extend($this, data))
                    .event('afterBuild')
                    .invoke(null, new DataTable($this));
                $this.options.sAjaxSource = data.source;
            }
        }

    }

})();