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
            translateTable: function () {
                $translate([
                    'admin.datatables.PROCESSING',
                    'admin.datatables.SEARCH',
                    'admin.datatables.LENGTH_MENU',
                    'admin.datatables.INFO',
                    'admin.datatables.INFO_EMPTY',
                    'admin.datatables.INFO_FILTERED',
                    'admin.datatables.LOADING_RECORDS',
                    'admin.datatables.ZERO_RECORDS',
                    'admin.datatables.EMPTY_TABLE',
                    'admin.datatables.paginate.FIRST',
                    'admin.datatables.paginate.PREVIOUS',
                    'admin.datatables.paginate.NEXT',
                    'admin.datatables.paginate.LAST',
                    'admin.datatables.aria.SORTASCENDING',
                    'admin.datatables.aria.SORTDESCENDINT',
                    'admin.datatables.FIRSTNAME'

                ]).then(function (translations) {
                    service.language.processing = translations['admin.datatables.PROCESSING'];
                    service.language.search = translations['admin.datatables.SEARCH'];
                    service.language.lengthMenu = translations['admin.datatables.LENGTH_MENU'];
                    service.language.info = translations['admin.datatables.INFO'];
                    service.language.infoEmpty = translations['admin.datatables.INFO_EMPTY'];
                    service.language.emptyTable = translations['admin.datatables.EMPTY_TABLE'];
                    service.language.infoFiltered = translations['admin.datatables.INFO_FILTERED'];
                    service.language.zeroRecords = translations['admin.datatables.ZERO_RECORDS'];
                    service.language.paginate.first = translations['admin.datatables.paginate.FIRST'];
                    service.language.paginate.previous = translations['admin.datatables.paginate.PREVIOUS'];
                    service.language.paginate.next = translations['admin.datatables.paginate.NEXT'];
                    service.language.paginate.last = translations['admin.datatables.paginate.LAST'];
                    service.language.aria.sortAscending = translations['admin.datatables.aria.SORTASCENDING'];
                    service.language.aria.sortDescending = translations['admin.datatables.aria.SORTDESCENDINT'];
                });

                return service.language;
            }
        };

        $rootScope.$on('$translateChangeSuccess', function () {
            service.translateTable();
        });

        return service;
    }
})();