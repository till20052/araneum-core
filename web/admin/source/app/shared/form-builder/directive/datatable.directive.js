(function () {
    "use strict";

    angular
        .module('app.formBuilder')
        .directive('datatableDir', dataTableDir);

    dataTableDir.$inject = ['RouteHelpers'];

    function dataTableDir(RouteHelpers) {
        var directive = {
            restrict: 'AE',
            scope: true,
            controller: ['$scope' , '$compile', 'DTOptionsBuilder', 'formDataService', '$translate', 'TranslateDatatablesService', function ($scope, $compile, DTOptionsBuilder, formDataService, $translate, translate) {
                var promise = formDataService.getPromise();

                $scope.vm.dt = {
                    initialized: false,
                    instance: {},
                    options: DTOptionsBuilder
                        .newOptions()
                        .withOption('processing', true)
                        .withOption('serverSide', true)
                        .withOption('fnServerData', function (source, data, callback, settings) {
                            settings.jqXHR = $.ajax({
                                dataType: 'json',
                                type: "POST",
                                url: source,
                                data: data,
                                success: function (response) {
                                    angular.forEach(response.aaData, function (item, i) {
                                        $scope.vm.datatableItems[item[0]] = {
                                            id: item[0],
                                            name: item[1],
                                            locale: item[2],
                                            enabled: item[3],
                                            orientation: item[4],
                                            encoding: item[5]
                                        };

                                        this[i] = item
                                            .splice(0, item.length - 1)
                                            .concat([
                                                '<div data-item="row" ng-model="vm.datatableItems[' + item[0] + ']" />',
                                                '<div data-item="checkbox" ng-model="vm.datatableItems[' + item[0] + ']" />'
                                            ]);

                                    }, response.aaData);
                                    callback(response);
                                    $('.dataTable td').each(function () {
                                        $(this).addClass('bb0 bl0');
                                    });

                                    $('div[data-item="row"]').each(function () {
                                        var ui = $(this),
                                            ngData = ui.attr('ng-model');

                                        $(ui.parents('td').eq(0)).addClass('text-center p0');
                                        ui.replaceWith(
                                            $compile($('<action-builder data-item="row" data-model="' + ngData + '"><action-builder/>').clone())($scope)
                                        );
                                    });

                                    $('div[data-item="checkbox"]').each(function () {
                                        var ui = $(this),
                                            ngData = ui.attr('ng-model');

                                        $(ui.parents('td').eq(0)).addClass('text-center p0');
                                        ui.replaceWith(
                                            $compile($('<div class="checkbox c-checkbox needsclick m0">' +
                                                '<label class="needsclick">' +
                                                '<input type="checkbox" value="" class="needsclick" ng-click="vm.clickCheckBox($event, ' + ngData + ')" />' +
                                                '<span class="fa fa-check mr0"></span>' +
                                                '</label>' +
                                                '</div>').clone())($scope)
                                        );
                                    });
                                }
                            });
                        })
                        .withOption('language', translate.translateTable())
                        .withPaginationType('full_numbers'),
                    columns: []
                };
                promise.then(function (response) {
                    onInitSuccess(response);
                });

                /**
                 * get data form server and add colums to datatable
                 * @param response
                 */
                function onInitSuccess(response) {
                    var massTranslate = [];
                    $scope.vm.dt.options.sAjaxSource = response.grid.source;
                    $translate(response.grid.columns).then(function (data) {
                        for (var key in data) {
                            var translatedWord = key;
                            massTranslate.push(translatedWord);
                        }
                        $scope.vm.dt.columns = massTranslate;
                        $scope.vm.dt.initialized = true;
                    });
                }
            }],
            templateUrl: RouteHelpers.basepath('widget/datatable.html')
        };

        return directive;
    }
})();
