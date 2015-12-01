(function () {
    'use strict';

    angular
        .module('app.formBuilder')
        .controller('FormBuilderCtrl', FormBuilderController);

    FormBuilderController.$inject = ['$state', 'formDataService'];

    function FormBuilderController($state, formDataService) {
        var vm = this,
            formJsonUrl = $state.initialize,
            formData = formDataService.getFromDataFromUrl(formJsonUrl);

        vm.filterFormData = formData.filter || {};
    }

})();