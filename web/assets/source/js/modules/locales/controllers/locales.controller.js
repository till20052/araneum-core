(function (ng) {
    'use strict';

    ng.module('app.locales')
        .controller('LocalesController', LocalesController);

    LocalesController.$inject = ['$compile', '$scope', '$http', 'DTOptionsBuilder', '$translate', '$rootScope'];
    function LocalesController($compile, $scope, $http, DTOptionsBuilder, $translate, $rootScope) {

        /**
         * Constructor
         */
        (function (vm) {

                initialization(onInitSuccess, onInitError);

                var language = {
                    "decimal": "",
                    "emptyTable": "No data available in table",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Show _MENU_ entries",
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                    "search": "Search:",
                    "zeroRecords": "No matching records found",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    }
                };


                $rootScope.$on('language',function(newValue, oldValue){
                    translateTable($translate,language);
                });

                translateTable($translate,language);

                vm.errors = [];

                vm.dt = {
                    initialized: false,
                    options: DTOptionsBuilder
                        .newOptions()
                        .withOption('processing', true)
                        .withOption('serverSide', true)
                        .withOption('sAjaxSource', '/admin/locales/datatable.json')
                        .withOption('fnServerData', function (source, data, callback, settings) {
                            settings.jqXHR = $.ajax({
                                dataType: 'json',
                                type: "POST",
                                url: source,
                                data: data,
                                success: function (response) {
                                    ng.forEach(response.aaData, function (item, i) {
                                        this[i] = item
                                            .splice(0, item.length - 1)
                                            .concat([
                                                '<div widget="actions" />',
                                                '<div widget="checkbox" />'
                                            ]);
                                    }, response.aaData);
                                    callback(response);
                                    $('div[widget]').each(function () {
                                        var ui = $(this);
                                        $(ui.parents('td').eq(0)).addClass('text-center p0');
                                        ui.replaceWith(
                                            $compile($('widget#locales-' + ui.attr('widget') + ' > div').clone())($scope)
                                        );
                                    });
                                }
                            });
                        })
                        .withPaginationType('full_numbers')
                        .withOption('language', language),

                    columns: []
                };

                vm.onTableClickEvent = onTableClickEvent;

                /**
                 * Initialization event in success case
                 * @param response
                 */
                function onInitSuccess(response) {
                    ng.forEach(response.datatable.columns, function (f) {
                        this.push(f);
                    }, vm.dt.columns);

                    vm.dt.initialized = true;

                    //console.log($translate.getTranslations($translate.use()));

                }

                /**
                 * Initialization event in error case
                 */
                function onInitError() {
                    vm.errors.push('Can\'t load data to datatable');
                }

                /**
                 * Click Table Event
                 * @param e
                 */
                function onTableClickEvent(e) {
                    var tag = $(e.target);
                    if (tag.attr('type') == 'checkbox') {
                        if (tag.attr('rel') == 'select-all') {
                            $('tbody input[type="checkbox"]', $(tag.parents('table').eq(0)))
                                .prop('checked', tag.prop('checked'));
                        }
                        else if (!tag.prop('checked')) {
                            $('thead input[type="checkbox"]', $(tag.parents('table').eq(0)))
                                .prop('checked', false);
                        }
                    }
                }

            })($scope);

        /**
         * Initialization of module
         * @param onSuccess
         * @param onError
         */
        function initialization(onSuccess, onError) {
            $http
                .get('/admin/locales/init.json')
                .success(onSuccess)
                .error(onError);
        }


        function translateTable($translate, language){
            $translate([
                'datatables.PROCESSING',
                'datatables.SEARCH',
                'datatables.LENGTH_MENU',
                'datatables.INFO',
                'datatables.INFO_EMPTY',
                'datatables.INFO_FILTERED',
                'datatables.LOADING_RECORDS',
                'datatables.ZERO_RECORDS',
                'datatables.EMPTY_TABLE',
                'datatables.paginate.FIRST',
                'datatables.paginate.PREVIUOS',
                'datatables.paginate.NEXT',
                'datatables.paginate.LAST',
                'datatables.aria.SORTASCENDING',
                'datatables.aria.SORTDESCENDINT'

            ]).then(function (translations) {
                language.processing = translations['datatables.PROCESSING'];
                language.search = translations['datatables.SEARCH'];
                language.lengthMenu = translations['datatables.LENGTH_MENU'];
                language.info = translations['datatables.INFO'];
                language.infoEmpty = translations['datatables.INFO_EMPTY'];
                language.emptyTable = translations['datatables.EMPTY_TABLE'];
                language.infoFiltered = translations['datatables.INFO_FILTERED'];
                language.zeroRecords = translations['datatables.ZERO_RECORDS'];
                language.paginate.first = translations['datatables.paginate.FIRST'];
                language.paginate.last = translations['datatables.paginate.LAST'];
                language.paginate.next = translations['datatables.paginate.NEXT'];
                language.paginate.previous = translations['datatables.paginate.LAST'];
                language.aria.sortAscending = translations['datatables.aria.SORTASCENDING'];
                language.aria.sortDescending = translations['datatables.aria.SORTDESCENDINT'];
            });

        }

    }

})
(angular);