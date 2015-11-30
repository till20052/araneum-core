(function () {
    angular
        .module('app.formBuilder')
        .controller('FormBuilderCtrl', ['$scope', '$state', 'formDataService', function($scope, $state, formDataService) {
            var formJsonUrl = $state.initialize,
                formData = formDataService.getFromDataFromUrl(formJsonUrl);
                $scope.filterFormData = formData.filter || {};
        }]);
})();