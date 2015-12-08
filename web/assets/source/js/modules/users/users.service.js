(function(angular){
    'use strict';

    angular
        .module('app.users')
        .service('UsersService', UsersService);

    UsersService.$inject = ['HTTPEventListener', '$state'];
    function UsersService(HTTPEventListener, $state){
        HTTPEventListener.onResponse(isUserAuthorized);

        return {
            isUserAuthorized: isUserAuthorized
        };

        function isUserAuthorized(response){
            if(response.status == 401){
                $state.go('login');
            }
        }
    }
})(angular);