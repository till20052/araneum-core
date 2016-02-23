(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatable', CRUDDataTableDirective);

    CRUDDataTableDirective.$inject = ['DTOptionsBuilder', '$compile', '$state'];

    /**
     * CRUD DataTable Directive
     *
     * @returns {Object}
     * @constructor
     */
    function CRUDDataTableDirective(DTOptionsBuilder, $compile, $state) {

        return {
            link: link,
            restrict: 'E',
            scope: {
                datatable: '=manifest',
                toolBarId: '@toolbar'
            }
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            if (!(scope.datatable instanceof Object))
                return;

            var dt = manifest(scope);

            var stopWatch = scope.$watch(function () {
                return dt.options.hasOwnProperty('sAjaxSource') && dt.options.sAjaxSource.length > 0;
            }, function (ready) {
                if (ready) {
                    element.replaceWith($compile(createTable({
                        columns: dt.columns
                    }))(scope));
                    stopWatch();
                }
            });
        }

        function manifest(scope) {
            /* jshint validthis: true */
            return angular.extend(scope.datatable, {
                instance: {},
                options: DTOptionsBuilder.newOptions()
                    .withOption('processing', true)
                    .withOption('serverSide', true)
                    .withFnServerData(fnServerData)
                    .withPaginationType('full_numbers'),
                setColumns: setColumns,
                setAjaxSource: setAjaxSource,
                getAjaxSource: getAjaxSource
            });

            /**
             * Get data for datatable from server
             *
             * @param source
             * @param data
             * @param callback
             * @param settings
             */
            function fnServerData(source, data, callback, settings) {
                var actions = [
                    '<crud-dropdown />',
                    '<crud-checkbox />'
                ];

                settings.jqXHR = $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: source,
                    data: data,
                    success: function (data) {
                        callback(angular.extend(data, {
                            aaData: $.map(data.aaData, function (cols) {
                                return [cols.splice(0, cols.length - 1).concat(actions)];
                            })
                        }));
                        $compile($('> tbody > tr', scope.datatable.instance.dataTable)
                            .data({
                                selected: false
                            })
                            .find('> td > *')
                            .filter(function () {
                                return /^crud\-.*$/ig.test($(this).prop('tagName'));
                            })
                        )(scope);
                    },
                    error: function (response) {
                        if (response.status !== 401)
                            return;
                        $state.go('login');
                    }
                });
            }

            /**
             * Set columns
             *
             * @param columns
             * @returns {setColumns}
             */
            function setColumns(columns) {
                this.columns = columns;

                return this;
            }

            /**
             * Set ajax source
             *
             * @param source
             * @returns {Object}
             */
            function setAjaxSource(source) {
                this.options.sAjaxSource = source;

                if (this.instance.hasOwnProperty('dataTable'))
                    this.instance.dataTable.fnDraw(false);

                return this;
            }

            /**
             * Get ajax source
             *
             * @returns {string|null|*}
             */
            function getAjaxSource() {
                return this.options.sAjaxSource;
            }

            function reload(ajaxSource) {

                //if (sNewSource !== undefined && sNewSource !== null) {
                //    oSettings.sAjaxSource = sNewSource;
                //}

                // Server-side processing should just call fnDraw
                //if (oSettings.oFeatures.bServerSide) {
                //    this.fnDraw();
                //    return;
                //}

                //this.oApi._fnProcessingDisplay(oSettings, true);
                //var that = this;
                //var iStart = oSettings._iDisplayStart;
                //var aData = [];

                //this.oApi._fnServerParams(oSettings, aData);

                //oSettings.fnServerData.call(oSettings.oInstance, oSettings.sAjaxSource, aData, function (json) {
                //    /* Clear the old information from the table */
                //    that.oApi._fnClearTable(oSettings);
                //
                //    /* Got the data - add it to the table */
                //    var aData = (oSettings.sAjaxDataProp !== "") ?
                //        that.oApi._fnGetObjectDataFn(oSettings.sAjaxDataProp)(json) : json;
                //
                //    for (var i = 0; i < aData.length; i++) {
                //        that.oApi._fnAddData(oSettings, aData[i]);
                //    }
                //
                //    oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
                //
                //    that.fnDraw();
                //
                //    if (bStandingRedraw === true) {
                //        oSettings._iDisplayStart = iStart;
                //        that.oApi._fnCalculateEnd(oSettings);
                //        that.fnDraw(false);
                //    }
                //
                //    that.oApi._fnProcessingDisplay(oSettings, false);
                //
                //}, oSettings);
            }
        }

        /**
         * Create table
         *
         * @param data
         * @returns {jQuery}
         */
        function createTable(data) {
            return $('<table class="table-bordered table-striped hover" />')
                .attr({
                    datatable: 'crud',
                    'dt-instance': 'datatable.instance',
                    'dt-options': 'datatable.options'
                })
                .append(createTableHead(data.columns));
        }

        /**
         * Create head of table
         *
         * @param columns
         * @returns {jQuery}
         */
        function createTableHead(columns) {
            return $('<thead />').append(
                $('<tr />').append(
                    $.map(columns, function (column) {
                        return $('<th class="bt0 bl0" />')
                            .html('{{ "' + column + '" | translate }}');
                    }),
                    $('<th class="bt0 bl0" />')
                        .attr({
                            width: 1,
                            'data-sortable': false
                        }),
                    $('<th class="bt0 bl0 p text-center" />')
                        .attr({
                            width: 1,
                            'data-sortable': false
                        })
                        .append($('<crud-checkbox />'))
                )
            );
        }

        /**
         * Get selected rows
         *
         * @returns {Array<jQuery>}
         */
        function getSelectedRows() {
            /* jshint validthis: true */
            if (['TABLE', 'TBODY'].indexOf(this.prop('tagName')) < 0)
                return [];
            return $('> tr, > tbody > tr', this)
                .filter(function () {
                    return $(this).data('selected') === true;
                })
                .toArray();
        }

        /**
         * Set row state selection
         *
         * @param state
         */
        function selectRow(state) {
            /* jshint validthis: true, eqeqeq: false */
            if (this.prop('tagName') != 'TR')
                return;
            this.data('selected', !!state);
        }
    }

})();