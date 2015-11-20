(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .controller('ChartDaylyApplicationStatusesController', ChartDaylyApplicationStatusesController);

    ChartDaylyApplicationStatusesController.$inject = ['Colors', '$scope', 'DashboardFactory'];

    function ChartDaylyApplicationStatusesController(Colors, $scope, DashboardFactory) {

        DashboardFactory.getStats().then(function(data){
            $scope.statistics = data.statistics;
            console.log($scope.statistics);
            activate();
        }, function(res){
            console.log(res);
        });

        ////////////////

        function activate() {

            /***Bar chart ***/
            $scope.barData = {
                labels:  $scope.statistics.daylyApplications.applications,
                datasets : [
                    {
                        label: 'Error',
                        fillColor : Colors.byName('danger'),
                        strokeColor : Colors.byName('danger'),
                        highlightFill: Colors.byName('danger'),
                        highlightStroke: Colors.byName('danger'),
                        data : $scope.statistics.daylyApplications.errors
                    },
                    {
                        label: 'Success',
                        fillColor : Colors.byName('success'),
                        strokeColor : Colors.byName('success'),
                        highlightFill : Colors.byName('success'),
                        highlightStroke : Colors.byName('success'),
                        data : $scope.statistics.daylyApplications.success
                    },
                    {
                        label: 'Warning',
                        fillColor : Colors.byName('warning'),
                        strokeColor : Colors.byName('warning'),
                        highlightFill : Colors.byName('warning'),
                        highlightStroke : Colors.byName('warning'),
                        data : $scope.statistics.daylyApplications.problems
                    },
                    {
                        label: 'Disabled',
                        fillColor : Colors.byName('gray'),
                        strokeColor : Colors.byName('gray'),
                        highlightFill : Colors.byName('gray'),
                        highlightStroke : Colors.byName('gray'),
                        data : $scope.statistics.daylyApplications.disabled
                    }
                ]
            };

            $scope.barOptions = {
                scaleBeginAtZero: true,
                scaleShowGridLines: true,
                scaleGridLineColor: 'rgba(0,0,0,.05)',
                scaleGridLineWidth: 1,
                barShowStroke: true,
                barStrokeWidth: 2,
                barValueSpacing: 5,
                barDatasetSpacing: 1
            };

            // Line chart
            // -----------------------------------
            $scope.lineData =[{
                "label": "Success",
                "color": "#27c24c",
                "data": $scope.statistics.daylyAverageStatuses.errors

                    /*[
                    ["00", 21],
                    ["01", 43],
                    ["02", 70],
                    ["03", 134],
                    ["04", 27],
                    ["05", 87],
                    ["06", 90],
                    ["07", 23],
                    ["08", 67],
                    ["09", 93],
                    ["10", 11],
                    ["11", 87],
                    ["12", 39],
                    ["13", 29],
                    ["14", 10],
                    ["15", 17],
                    ["16", 88],
                    ["17", 67],
                    ["18", 69],
                    ["19", 42],
                    ["20", 26],
                    ["21", 11],
                    ["22", 87],
                    ["23", 77]
                ]*/
            }, {
                "label": "Problem",
                "color": "#ff902b",
                "data": [
                    ["00", 13],
                    ["01", 16],
                    ["02", 77],
                    ["03", 89],
                    ["04", 94],
                    ["05", 92],
                    ["06", 32],
                    ["07", 30],
                    ["08", 66],
                    ["09", 88],
                    ["10", 90],
                    ["11", 45],
                    ["12", 23],
                    ["13", 11],
                    ["14", 2],
                    ["15", 50],
                    ["16", 33],
                    ["17", 45],
                    ["18", 46],
                    ["19", 67],
                    ["20", 22],
                    ["21", 11],
                    ["22", 10],
                    ["23", 5]
                ]
            }, {
                "label": "Error",
                "color": "#f05050",
                "data": [
                    ["00", 23],
                    ["01", 11],
                    ["02", 45],
                    ["03", 49],
                    ["04", 38],
                    ["05", 24],
                    ["06", 22],
                    ["07", 11],
                    ["08", 6],
                    ["09", 32],
                    ["10", 49],
                    ["11", 56],
                    ["12", 52],
                    ["13", 34],
                    ["14", 27],
                    ["15", 22],
                    ["16", 19],
                    ["17", 13],
                    ["18", 16],
                    ["19", 12],
                    ["20", 22],
                    ["21", 24],
                    ["22", 36],
                    ["23", 42]
                ]
            }, {
                "label": "Disabled",
                "color": "#dde6e9",
                "data": [
                    ["00", 0],
                    ["01", 0],
                    ["02", 0],
                    ["03", 0],
                    ["04", 1],
                    ["05", 1],
                    ["06", 1],
                    ["07", 1],
                    ["08", 0],
                    ["09", 0],
                    ["10", 0],
                    ["11", 0],
                    ["12", 0],
                    ["13", 0],
                    ["14", 0],
                    ["15", 0],
                    ["16", 0],
                    ["17", 0],
                    ["18", 0],
                    ["19", 0],
                    ["20", 0],
                    ["21", 0],
                    ["22", 0],
                    ["23", 0]
                ]
            }
            ];

            $scope.lineOptions = {
                series: {
                    lines: {
                        show: true,
                        fill: 0.01
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
                    content: function (label, x, y) { return x + ' : ' + y; }
                },
                xaxis: {
                    tickColor: '#eee',
                    mode: 'categories'
                },
                yaxis: {
                    position: ($scope.app.layout.isRTL ? 'right' : 'left'),
                    tickColor: '#eee'
                },
                shadowSize: 0
            };

        }

    }


})();
