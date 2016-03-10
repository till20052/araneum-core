(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('AppStatusesChartController', AppStatusesChartController);

    AppStatusesChartController.$inject = ['Colors', '$scope', 'DashboardService'];

    function AppStatusesChartController(Colors, $scope, DashboardService) {
        /* jshint validthis: true */
        var vm = this;

        vm.data = {
            labels: [],
            series: []
        };
        vm.options = {
            height: 220,
            low: 0,
            high: 100,
            seriesBarDistance: 10
        };

        activate();

        /**
         * Activation
         */
        function activate() {
            DashboardService.onDataLoaded(function (response) {
                var data = response.data.statistics.daylyApplications;
                vm.data.labels = data.applications;
                angular.forEach(['success', 'problems', 'errors', 'disabled'], function (key) {
                    this.push(data[key].map(function (value) {
                        return parseInt(value) + 50;
                    }));
                }, vm.data.series);
            });
        }

    }


})();
