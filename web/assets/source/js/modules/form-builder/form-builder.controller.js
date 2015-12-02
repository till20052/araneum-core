(function() {
    'use strict';

    angular
        .module('app.formBuilder')
        .controller('FormBuilderCtrl', FormBuilderController);

    FormBuilderController.$inject = ['$state', '$resource', 'formDataService'];

    function FormBuilderController($state, $resource, formDataService) {
        var vm = this;
        var formJsonUrl = $state.$current.initialize;
        vm.formObj = formDataService.getFromDataFromUrl(formJsonUrl);
        vm.gridData = {};
        vm.search = function(url) {
            if (url === undefined) {
                alert('no url');
            }

            $resource(url).$promise.then(function(response) {
                vm.gridData = response;
            });
        };
    }
})();
