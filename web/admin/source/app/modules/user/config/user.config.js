(function (angular) {
    'use strict';

    angular
        .module('app.users')
        .config(['$httpProvider', config]);

    function config($httpProvider) {
        $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    }

})(angular);