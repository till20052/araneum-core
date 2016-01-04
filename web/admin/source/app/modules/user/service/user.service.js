(function () {
    'use strict';

    angular
        .module('app.users')
        .factory('User', User);

    User.$inject = ['$http'];

    /**
     * User Service
     *
     * @param $http
     * @returns {{
     *  name: string,
     *  email: string,
     *  picture: string,
     *  settings: {},
     *  authorized: boolean,
     *  setSettings: setSettings,
     *  getSettings: getSettings,
     *  setAsNotAuthorized: setAsNotAuthorized,
     *  isAuthorized: isAuthorized,
     *  setData: setData
     * }}
     * @constructor
     */
    function User($http) {
        /* jshint eqeqeq: false */

        var dataSource = {
            resource: {
                method: 'GET',
                url: '/user/profile/get_authorized_user_data'
            },
            pending: false
        };
        var service = {
            name: '',
            email: '',
            picture: '/admin/build/img/user/no-image.jpg',
            settings: {},
            authorized: false,
            init: init,
            setSettings: setSettings,
            getSettings: getSettings,
            setAsNotAuthorized: setAsNotAuthorized,
            isAuthorized: isAuthorized,
            setData: setData
        };

        return service;

        function init(callback) {
            if (dataSource.pending) {
                return;
            }
            dataSource.pending = true;
            $http(dataSource.resource)
                .then(function (response) {
                    dataSource.pending = false;
                    setData(response.data);
                    if (typeof callback == 'function') {
                        callback(service);
                    }
                });
        }

        function getSettings() {
            return service.settings;
        }

        function setSettings(settings, onSuccess, onError) {
            $http
                .post('/user/profile/settings', settings)
                .success(function (response) {
                    service.settings = settings;
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

        function setAsNotAuthorized() {
            service.authorized = false;
        }

        function isAuthorized() {
            return service.authorized;
        }

        function setData(data) {
            (['name', 'email', 'settings', 'authorized']).forEach(function (field) {
                if (typeof data[field] == 'undefined') {
                    return;
                }
                service[field] = data[field];
            });
        }

    }

})();