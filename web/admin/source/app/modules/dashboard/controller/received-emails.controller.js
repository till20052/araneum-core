/**
 * Created by artemm on 30.11.15.
 */
(function (ng) {
    'use strict';

    angular.module('app.dashboard')
        .controller('ReceivedEmailsController', ReceivedEmailsController);

    ReceivedEmailsController.$inject = ['DashboardService', '$scope', '$translate'];

    /**
     * Received Emails Controller
     *
     * @param DashboardService
     * @param $scope
     * @param $translate
     * @constructor
     */
    function ReceivedEmailsController(DashboardService, $scope, $translate) {
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
                    return v + ' ' + vm.captions.emails;
                }
            },
            shadowSize: 0
        };

        vm.onLoading = true;
        vm.captions = {
            emails: 'emails'
        };

        DashboardService.onDataLoaded(function (response) {
            vm.onLoading = false;
            vm.count = response.data.statistics.receivedEmails.count;
            vm.data = response.data.statistics.receivedEmails.data;
            DashboardService.assignColorsByLabel(vm.data);
        }, function (error) {
            vm.onLoading = false;
            vm.error = error;
        });

        $translate('admin.dashboard.widget.EMAILS').then(function (value) {
            vm.captions.emails = value;
        });

    }

})();