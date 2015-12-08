(function () {
    'use strict';

    angular
        .module('app.users')
        .controller('UserTableController', UserTableController);

    UserTableController.$inject = ['$compile', '$scope', '$http', 'DTOptionsBuilder'];

    /**
     * UserTableController constructor
     *
     * @param $compile
     * @param $scope
     * @param $http
     * @constructor
     */
    function UserTableController($compile, $scope, $http) {

        (function (vm) {
            init(function (response) {
                angular.forEach(response.grid.columns, function (tableHeader) {
                    this.push(tableHeader);
                }, vm.dt.columns);

                vm.dt.options.sAjaxSource = response.grid.source;
                vm.dt.initialized = true;
            });

            vm.dt = {
                initialized: false,
                options:
                {
                    processing: true,
                    serverSide: true,
                    sAjaxSource: null,
                    fnServerData:  function (source, data, callback, settings) {
                        settings.jqXHR = $.ajax({
                            dataType: 'json',
                            type: "GET",
                            url: source,
                            data: data,
                            success: function (response) {
                                angular.forEach(response.aaData, function (item, i) {
                                    this[i] = item
                                        .splice(0, item.length - 1)
                                        .concat([
                                            '<div widget="actions" />',
                                            '<div widget="checkbox" />'
                                        ]);
                                }, response.aaData);
                                callback(response);

                                $('td').each(function () {
                                    $(this).addClass('bb0 bl0');
                                });

                                $('div[widget]').each(function () {
                                    var ui = $(this);
                                    $(ui.parents('td').eq(0)).addClass('text-center p0 bb0 bl0');
                                    ui.replaceWith(
                                        $compile($('widget#users-' + ui.attr('widget') + ' > div').clone())($scope)
                                    );
                                });
                            }
                        });
                    },
                    sPaginationType: 'full_numbers'
                },
                columns: []
            };

            vm.onTableClickEvent = onTableClickEvent;
        })($scope);

        /**
         * Get datatable headers
         *
         * @param onSuccess
         * @param onError
         */
        function init(onSuccess) {
            $http
                .get('/user/manage/init')
                .success(onSuccess)
        }


        /**
         * On table click event handler
         *
         * @param e
         */
        function onTableClickEvent(e) {
            var tag = $(e.target);
            if (tag.attr('type') == 'checkbox') {
                if (tag.attr('rel') == 'select-all') {
                    $('tbody input[type="checkbox"]', $(tag.parents('table').eq(0)))
                        .prop('checked', tag.prop('checked'));
                }
                else if (!tag.prop('checked')) {
                    $('thead input[type="checkbox"]', $(tag.parents('table').eq(0)))
                        .prop('checked', false);
                }
            }
        }
    }

})();
