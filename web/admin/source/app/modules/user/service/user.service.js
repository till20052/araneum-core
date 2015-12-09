(function (angular) {

    'use strict';

    angular
        .module('app.users')
        .service('User', ['$sessionStorage', '$http', '$rootScope', User]);

    function User($sessionStorage, $http, $rootScope) {

        return (function () {

            if (typeof $sessionStorage.user != 'object') {
                $sessionStorage.user = {
                    name: '',
                    email: '',
                    picture: '/admin/build/img/user/no-image.jpg',
                    isAuthorized: false
                };
            }

            $http
                .get('/user/profile/get_authorized_user_data')
                .success(function (response) {
                    angular.forEach(response, function (value, key) {
                        if (value == null) {
                            return;
                        }
                        this[key] = value;
                    }, $sessionStorage.user);
                });

            return {
                set: set,
                get: get,
                getUser: getUser,
                getName: getName,
                getEmail: getEmail,
                setSettings: setSettings,
                getSettings: getSettings,
                setAsAuthorized: setAsAuthorized,
                setAsNotAuthorized: setAsNotAuthorized,
                isAuthorized: isAuthorized,
                data: data
            };

        })();

        function set(key, value) {
            $sessionStorage.user[key] = value;
            return this;
        }

        function get(key) {
            return $sessionStorage.user[key] || null;
        }

        function getName() {
            return get('name');
        }

        function getEmail() {
            return get('email');
        }

        function setSettings(settings, onSuccess, onError) {
            $http
                .post('/user/profile/settings', settings)
                .success(function (response) {
                    set('settings', settings);
                    if (typeof onSuccess == 'function') {
                        onSuccess(response);
                    }
                })
                .error(function (response) {
                    if (typeof onError == 'function') {
                        onError(response);
                    }
                });
        }

        function getSettings() {
            return get('settings');
        }

        function setAsAuthorized() {
            return set('isAuthorized', true);
        }

        function setAsNotAuthorized() {
            return set('isAuthorized', false);
        }

        function isAuthorized() {
            return get('isAuthorized');
        }

        function data(data) {
            angular.forEach(data, function (value, key) {
                set(key, value);
            });
        }

        function getUser() {
            return $sessionStorage.user;
        }

    }

})(angular);