(function() {
    'use strict';

    angular
        .module('app.formBuilder')
        .controller('FormBuilderController', FormBuilderController);

    FormBuilderController.$inject = ['$state', '$scope', '$compile', '$http', 'SweetAlert', 'ngDialog', 'toaster', 'DTOptionsBuilder', 'DTInstances', 'formDataService'];

    /**
     *
     * @param $state
     * @param $scope
     * @param $http
     * @param $compile
     * @param DTOptionsBuilder options for datatabe
     * @param DTInstances changing data in datatable
     * @param formDataService factory for store data form server
     * @constructor
     */
    function FormBuilderController($state, $scope, $compile, $http, ngSweetAlert, ngDialog, toaster, DTOptionsBuilder, DTInstances, formDataService) {
        var vm = this;

        var formJsonUrl = $state.$current.initialize;
        formDataService.setFromUrl(formJsonUrl);
        var promise = formDataService.getPromise();
        vm.onTableClickEvent = onTableClickEvent
        vm.confirm = confirm;
        vm.datatableItems = {};
        vm.checkBoxData = {};
        vm.actionConf = {
            'row': {},
            'top': {}
        };
        vm.actionClick = actionClick;
        vm.clickCheckBox = clickCheckBox;

        $scope.$on('addActionConfig', addActionConfig);

        vm.dt = {
            initialized: false,
            instance: {},
            options: DTOptionsBuilder
                .newOptions()
                .withOption('processing', true)
                .withOption('serverSide', true)
                .withOption('fnServerData', function(source, data, callback, settings) {
                    settings.jqXHR = $.ajax({
                        dataType: 'json',
                        type: "POST",
                        url: source,
                        data: data,
                        success: function(response) {
                            angular.forEach(response.aaData, function(item, i) {
                                vm.datatableItems[item[0]] = {
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
                            $('.dataTable td').each(function() {
                                $(this).addClass('bb0 bl0');
                            });

                            $('div[data-item="row"]').each(function() {
                                var ui = $(this),
                                    ngData = ui.attr('ng-model');

                                $(ui.parents('td').eq(0)).addClass('text-center p0');
                                ui.replaceWith(
                                    $compile($('<action-builder data-item="row" data-model="' + ngData + '"><action-builder/>').clone())($scope)
                                );
                            });

                            $('div[data-item="checkbox"]').each(function() {
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
                .withPaginationType('full_numbers'),
            columns: []
        };

        /**
         * Set url to datatable
         * @param url
         */
        vm.search = function(url) {
            var formData = $('form').serialize();
            vm.dt.options.sAjaxSource = url + '?' + formData;
        };

        /**
         * Reset datatable url
         * @param $event
         * @param url
         * @param id
         */
        vm.reset = function($event, url, id) {
            $($event.currentTarget).closest('.row').find('#' + id)[0].reset();
            vm.dt.options.sAjaxSource = url;
        };

        vm.errors = [];

        promise.then(function(response) {
            onInitSuccess(response);
        });

        /**
         * get data form server and add colums to datatable
         * @param response
         */
        function onInitSuccess(response) {
            vm.dt.options.sAjaxSource = response.grid.source;
            angular.forEach(response.grid.columns, function(f) {
                this.push(f);
            }, vm.dt.columns);
            vm.dt.initialized = true;
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
                        .prop('checked', tag.prop('checked'))
                        .addClass('selectedRow');
                        vm.checkBoxData = vm.datatableItems;
                } else if (!tag.prop('checked')) {
                    $('thead input[type="checkbox"]', $(tag.parents('table').eq(0)))
                        .prop('checked', false)
                        .removeClass('selectedRow');
                        vm.checkBoxData = []
                }
            }
        }


        /**
         * Call function on action click
         * @param $event
         * @param data grid object
         */
        function actionClick($event, data) {
            var conf = $($event.currentTarget).data('conf').split(','),
                actionType = conf[1],
                actionId = conf[0],
                actionData = [],
                actionConfig = vm.actionConf[actionType][actionId],
                type = actionConfig.type;

            if (actionType === 'top') {
                for (var obj in vm.checkBoxData) {
                    actionData.push(vm.checkBoxData[obj].id);
                }
            }

            if (actionType === 'row') {
                actionData.push(data.id);
            }

            if (type === 'resource') {
                resource(actionConfig, actionData);
                return;
            }

            if (type === 'form') {
                ngDialog.open({
                    template: 'fromId',
                    className: 'ngdialog-theme-default',
                    controller: ['$scope', function($scope) {
                        var id = '';

                        if (data !== undefined) {
                            id = data.id;
                        }

                        $scope.url = actionConfig.url + '/' + id;
                        $scope.id = id;

                        $scope.send = function($event, url) {
                            var formData = $('#locale').serializeArray(),
                                data = {},
                                len = formData.length;

                            $event.preventDefault();

                            for (var i = 0; i < len; i++) {
                                data[formData[i].name] = formData[i].value;
                            }

                            sendDataCreate(url, data, function(result) {
                                console.log(actionConfig)
                                callbackManager(actionConfig.callback, data);
                                $scope.closeThisDialog();
                                toaster.pop(
                                    'success',
                                    'Success',
                                    result.message
                                );
                            }, function(jqXHR, textStatus, errorThrown) {
                                toaster.pop(
                                    'error',
                                    'Error',
                                    jqXHR.data.message
                                );
                            });
                        }
                    }]
                });
            }
        }

        /**
         * Add or remove data form checkboxes
         * @param data
         * @returns {boolean}
         */
        function clickCheckBox($event, data) {
            if (vm.checkBoxData.hasOwnProperty(data.id)) {
                delete vm.checkBoxData[data.id];
                $($event.currentTarget).closest('tr').removeClass('selectedRow');

                return false;
            }

            $($event.currentTarget).closest('tr').addClass('selectedRow');
            vm.checkBoxData[data.id] = data;
        }

        /**
         * Add actions config
         * @param event
         * @param actionConfig
         */
        function addActionConfig(event, actionConfig) {
            vm.actionConf[actionConfig.type][actionConfig.id] = actionConfig.config;
        }

        /**
         * Send data on form confirm
         * @param url
         * @param data
         */
        function confirm(url, data) {
            $http({
                method: 'post',
                url: url,
                data: data
            });
        }

        /**
         * Called when click on simple action
         * @param config
         * @param data
         * @returns {boolean}
         */
        function resource(config, data) {
            if (data.length === 0) {
                return false;
            }

            if (config.title === undefined) {
                sendData(config.url, data, function(result) {
                    callbackManager(config.callback);
                    vm.checkBoxData = [];
                    $('input[type="checkbox"]', $('.panel.panel-default'))
                        .prop('checked', false);


                    toaster.pop(
                        'success',
                        'Success'
                    );

                }, function(error) {
                    toaster.pop('error', 'Error', error.data.message);
                });

                return;
            };

            ngSweetAlert.swal({
                    title: config.title,
                    //text: $translate('admin.pages.AREYOUSURETEXT'),
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: config.no.title,
                    confirmButtonColor: '#f05050',
                    confirmButtonText: config.yes.title,
                    closeOnConfirm: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        sendData(config.url, data, function(result) {
                            callbackManager(config.callback);
                            vm.checkBoxData = [];
                            $('input[type="checkbox"]', $('.panel.panel-default'))
                                .prop('checked', false);

                            toaster.pop(
                                'success',
                                'Success'
                            );
                        }, function(error) {
                            toaster.pop('error', 'Error', error.data.message);
                        });
                    }
                });
        }

        /**
         * Send data to server
         * @param url
         * @param data
         * @param success
         * @param error
         */
        function sendData(url, data, success, error) {
            $http({
                method: 'POST',
                url: url,
                data: {data: data}
            }).then(success, error);
        }

        /**
         * Send data to server
         * @param url
         * @param data
         * @param success
         * @param error
         */
        function sendDataCreate(url, data, success, error) {
            $http({
                method: 'POST',
                url: url,
                data: data
            }).then(success, error);
        }


        function callbackManager (callback, data) {
            try {
                vm[callback](data);
            } catch (err) {
                console.log(err.message);
            }
        }

        vm.deleteRow = function() {
            // DELETE ROW FROM DATATABLE
            console.log("delete");
            vm.datatableItems = [];
            $('#datatable').DataTable().rows('.selectedRow').remove().draw();
        }


        vm.create = function(data) {
            // DELETE ROW FROM DATATABLE
            console.log("create");
            $('#datatable').DataTable().add(data).draw();
        }

        vm.update = function(data) {
            // DELETE ROW FROM DATATABLE
            console.log("update");
        }
    }
})();