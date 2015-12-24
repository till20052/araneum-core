/**
 * Created by artemm on 30.11.15.
 */
(function (ng) {
    'use strict';

    ng.module('app.dashboard')
        .controller('ReceivedEmailsController', ReceivedEmailsController);

    ReceivedEmailsController.$inject = ['$scope', 'DashboardService', '$translate'];

    /**
     * Received Emails Controller
     *
     * @param $scope
     * @param DashboardService
     * @param $translate
     * @constructor
     */
    function ReceivedEmailsController($scope, DashboardService, $translate) {

        /**
         * Constructor
         */
        (function (vm) {

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
                    tickColor: '#eee',
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickFormatter: function (v) {
                        return v + ' ' + vm.captions.emails;
                    }
                },
                shadowSize: 0
            };

            $scope.onLoading = true;
            vm.captions = {
                emails: 'emails'
            };

            DashboardService.onDataLoaded(function (response) {
                $scope.onLoading = false;
                vm.count = response.data.statistics.receivedEmails.count;
                vm.data = response.data.statistics.receivedEmails.data;
                DashboardService.assignColorsByLabel(vm.data);
            }, function (error) {
                $scope.onLoading = false;
                vm.error = error;
            });

            $translate('admin.dashboard.widget.EMAILS').then(function (value) {
                vm.captions.emails = value;
            });

        })($scope);

    }

})(angular);