(function() {
    'use strict';

    angular
        .module('app.formBuilder')
        .controller('FormBuilderController', FormBuilderController);

    FormBuilderController.$inject = ['$state', '$scope', '$http', '$filter', 'SweetAlert', 'ngDialog', 'toaster', 'formDataService'];

    /**
     *
     * @param $state
     * @param $scope
     * @param $http
     * @param $filter
     * @param ngSweetAlert
     * @param ngDialog
     * @param toaster
     * @param formDataService
     * @constructor
     */
    function FormBuilderController($state, $scope, $http, $filter, ngSweetAlert, ngDialog, toaster, formDataService) {
        var vm = this;

        var formJsonUrl = $state.$current.initialize;
        formDataService.setFromUrl(formJsonUrl);
        var promise = formDataService.getPromise();
        vm.onTableClickEvent = onTableClickEvent;
        vm.confirm = confirm;
        vm.datatableItems = {};
        vm.checkBoxData = {};
        vm.actionConf = {
            'row': {},
            'top': {}
        };
        vm.actionClick = actionClick;
        vm.clickCheckBox = clickCheckBox;
        vm.resetConfig = {};
        vm.dt = {};
        $scope.urlData = formJsonUrl;

        $scope.$on('addActionConfig', addActionConfig);

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
            var form = $($event.currentTarget).closest('.row').find('#' + id).get(0);

            if (form === undefined) {
                form =  $($event.currentTarget).closest('.panel.panel-default').find('#' + id).get(0);
            }

            form.reset();
            vm.dt.options.sAjaxSource = url;
        };

        vm.errors = [];

        promise.then(function(response) {
            vm.resetConfig = {
                url: response.grid.source,
                idForm: response.filter.vars.id
            }
        });

        /**
         * Click Table Event
         * @param e
         */
        function onTableClickEvent(e) {
            var tag = $(e.target);
            if (tag.attr('type') == 'checkbox') {
                if (tag.attr('rel') == 'select-all') {
                    if (tag.prop('checked')) {
                        $('tbody input[type="checkbox"]', $(tag.parents('table').eq(0)))
                            .prop('checked', tag.prop('checked'))
                            .addClass('selectedRow');

                        $.extend(vm.checkBoxData, vm.datatableItems);

                        return;
                    }

                    if (!tag.prop('checked')) {
                        $('thead input[type="checkbox"]', $(tag.parents('table').eq(0)))
                            .prop('checked', false);
                        $('tbody input[type="checkbox"]', $(tag.parents('table').eq(0)))
                            .prop('checked', tag.prop('checked'))
                            .removeClass('selectedRow');
                        vm.checkBoxData = []
                    }
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
                actionConfig = vm.actionConf[actionType][actionId];

            var type = actionConfig.type;

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
                        $scope.actionName = actionConfig.actionName;

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

                            data['id'] = id;

                            sendDataCreate(url, data, function(result) {
                                callbackManager(actionConfig.callback, data, result.id);
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

            if (!data) {
                return false;
            }

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
            }

            ngSweetAlert.swal({
                    title: $filter('translate')(config.title),
                    //text: $translate('admin.pages.AREYOUSURETEXT'),
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: $filter('translate')(config.no.title),
                    confirmButtonColor: '#f05050',
                    confirmButtonText: $filter('translate')(config.yes.title),
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
                data: {
                    data: data
                }
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


        function callbackManager(callback, data, id) {
            try {
                vm[callback](data, id);
            } catch (err) {
                console.log(err.message);
            }
        }

        vm.deleteRow = function() {
            vm.datatableItems = [];
            $('#datatable').DataTable().rows('.selectedRow').remove().draw();
        };


        vm.create = function(data, id) {
            var values = [id];
            vm.datatableItems[id] = data;

            for (var key in data) {
                values.push(data[key]);
            }

            values.push('<div data-item="row" ng-model="vm.datatableItems[' + id + ']" />');
            values.push('<div data-item="checkbox" ng-model="vm.datatableItems[' + id + ']" />');

            $('#datatable').DataTable().draw();
        };

        vm.update = function(data) {
            $('#datatable').DataTable().draw();
        };

        vm.editRow = function (data) {
            $('#datatable').DataTable().draw();
        };
    }
})();