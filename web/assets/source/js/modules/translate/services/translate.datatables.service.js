(function () {
    'use strict';

    angular
        .module('app.translate')
        .service('TranslateDatatablesService', TranslateDatatablesService);

    TranslateDatatablesService.$inject = ['$rootScope', '$translate'];

    function TranslateDatatablesService($rootScope, $translate) {

        var service = {
            language: {
                "decimal": "",
                "emptyTable": "",
                "info": "",
                "infoEmpty": "",
                "infoFiltered": "",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "",
                "loadingRecords": "",
                "processing": "",
                "search": "",
                "zeroRecords": "",
                "paginate": {
                    "first": "",
                    "last": "",
                    "next": "",
                    "previous": ""
                },
                "aria": {
                    "sortAscending": "",
                    "sortDescending": ""
                }
            },
            translateTable: function ($translate) {
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
                    service.language.processing = translations['datatables.PROCESSING'];
                    service.language.search = translations['datatables.SEARCH'];
                    service.language.lengthMenu = translations['datatables.LENGTH_MENU'];
                    service.language.info = translations['datatables.INFO'];
                    service.language.infoEmpty = translations['datatables.INFO_EMPTY'];
                    service.language.emptyTable = translations['datatables.EMPTY_TABLE'];
                    service.language.infoFiltered = translations['datatables.INFO_FILTERED'];
                    service.language.zeroRecords = translations['datatables.ZERO_RECORDS'];
                    service.language.paginate.first = translations['datatables.paginate.FIRST'];
                    service.language.paginate.last = translations['datatables.paginate.LAST'];
                    service.language.paginate.next = translations['datatables.paginate.NEXT'];
                    service.language.paginate.previous = translations['datatables.paginate.LAST'];
                    service.language.aria.sortAscending = translations['datatables.aria.SORTASCENDING'];
                    service.language.aria.sortDescending = translations['datatables.aria.SORTDESCENDINT'];
                });

                return service.language;
            }
        };

        $rootScope.$on('language', function () {
            service.translateTable($translate);
        });

        return service;
    }
})();