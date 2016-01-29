(function () {
    'use strict';

    angular
        .module('crud')
        .controller('CRUDFormController', CRUDFormController);

    CRUDFormController.$inject = ['$scope', 'CRUDFormLoader'];

    function CRUDFormController($scope, CRUDFormLoader) {
        /* jshint validthis: true */
        var vm = this,
            config = $scope.config;

        vm.submit = submit;
        vm.cancel = cancel;

        activate();

        /**
         * Activation
         *
         * @private
         */
        function activate() {
            if (config instanceof Object) {
                CRUDFormLoader
                    .setUrl(config.source)
                    .load({
                        onSuccess: function () {
                            CRUDFormLoader.clearPromise();
                        }
                    });
            }
        }

        function submit() {
            // @todo необхідно зробити валідацію форми
            console.log($scope, vm);
            //config.onsubmit(config, {
            //    onSuccess: function(r){
            //        console.log(r);
            //    }
            //});
        }

        function cancel() {
            if(
                $scope.$parent.hasOwnProperty('ngDialog') &&
                $scope.$parent.ngDialog instanceof Object
            ){
                $scope.$parent.ngDialog.close();
            }
        }

    }

})();