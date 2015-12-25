(function () {
    'use strict';

    angular.module('app.dashboard')
        .controller('RegisteredCustomersController', RegisteredCustomersController);

    RegisteredCustomersController.$inject = ['DashboardService', '$scope', '$translate'];

    /**
     * Registered Customer Controller
     *
     * @param DashboardService
     * @param $scope
     * @param $translate
     * @constructor
     */
    function RegisteredCustomersController(DashboardService, $scope, $translate) {
        /* jshint validthis: true */
        var vm = this;

        vm.error = '';
        vm.data = [];
        vm.count = 0;
        vm.options = {
            series: {
                lines: {
                    show: true,
                    fill: 0.8
                },
                points: {
                    show: true,
                    radius: 4
                }
            },
            grid: {
                borderColor: '#eee',
                borderWidth: 1,
                hoverable: true,
                backgroundColor: '#fcfcfc'
            },
            tooltip: true,
            tooltipOpts: {
                content: function (label, x, y) {
                    return x + ' : ' + y;
                }
            },
            xaxis: {
                tickColor: '#fcfcfc',
                mode: 'categories'
            },
            yaxis: {
                min: 0,
                tickDecimals: 0,
                tickColor: '#eee',
                position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                tickFormatter: function (v) {
                    return v + ' ' + vm.captions.customers;
                }
            },
            shadowSize: 0
        };

        vm.onLoading = true;
        vm.captions = {
            customers: 'customers'
        };

        DashboardService.onDataLoaded(function (response) {
            vm.onLoading = false;
            vm.count = response.data.statistics.registeredCustomers.count;
            vm.data = response.data.statistics.registeredCustomers.data;
            DashboardService.assignColorsByLabel(vm.data);
        }, function (error) {
            vm.onLoading = false;
            vm.error = error;
        });

        $translate('admin.dashboard.widget.CUSTOMERS').then(function (value) {
            vm.captions.customers = value;
        });

    }

})();