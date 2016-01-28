(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = ['$scope', 'CRUDFormLoader'];

    function CRUDFormController($scope, CRUDFormLoader) {
        /* jshint validthis: true */
        var vm = this;

        vm.method = 'POST';
        vm.action = null;

        activate();

        /**
         * Activation
         */
        function activate() {
            if ($scope.hasOwnProperty('source')) {
                CRUDFormLoader
                    .setUrl($scope.source)
                    .load({
                        onSuccess: function () {
                            CRUDFormLoader.clearPromise();
                        }
                    });
            }
        }

    }

})();