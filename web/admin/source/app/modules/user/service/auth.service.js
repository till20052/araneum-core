(function () {
    'use strict';

    angular
        .module('app.users')
        .service('UserAuth', ['$http', 'User', '$state', Auth]);

    /**
     * User auth service constructor
     *
     * @param $http
     * @param User
     * @param $state
     * @returns {{
     *  initLoginForm: initLoginForm,
     *  login: login,
     *  logout: logout
     * }}
     * @constructor
     */
    function Auth($http, User, $state) {

        var _csrf_token;
        var targetState = {name: 'app.users'};

        return {
            initLoginForm: initLoginForm,
            login: login,
            logout: logout
        };

        /**
         * Initialize login firm
         *
         * @param callback
         */
        function initLoginForm(callback) {
            $http
                .get('/manage/login')
                .success(function (response) {
                    _csrf_token = response;
                    if (typeof callback == 'function') {
                        callback(response);
                    }
                })
                .error(function (response, statusCode) {
                    if (statusCode == 403 || statusCode == 401) {
                        User.setData($.extend(response, {authorized: true}));
                        $state.go(targetState.name);
                    }
                });
        }

        /**
         * Check user login
         *
         * @param data
         */
        function login(data) {
            $http
                .post('/manage/login_check', $.param({
                    _username: data.username,
                    _password: data.password,
                    _csrf_token: _csrf_token
                }),
                {
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                .success(function (response) {
                    var event = createEvent(response);

                    if (typeof data.onSuccess == 'function') {
                        data.onSuccess(event);
                    }

                    if (!event.isPropagationStopped()) {
                        User.setData(angular.extend(response, {authorized: true}));
                        $state.go(targetState.name);
                    }
                })
                .error(function (response) {
                    var event = createEvent(response);

                    if (typeof data.onError == 'function') {
                        data.onError(event);
                    }

                    if (!event.isPropagationStopped()) {
                        _csrf_token = response._csrf_token;
                    }
                });
        }

        /**
         * Logout
         *
         * @param data
         */
        function logout(data) {
            $http
                .get('/logout')
                .success(function (response) {
                    var event = createEvent(response);

                    if (
                        typeof data == 'object'
                        && typeof data.onSuccess == 'function'
                    ) {
                        data.onSuccess(event);
                    }

                    if (!event.isPropagationStopped()) {
                        User.setAsNotAuthorized();
                        $state.go('login');
                    }
                })
                .error(function (response) {
                    var event = createEvent(response);

                    if (
                        typeof data == 'object'
                        && typeof data.onError == 'function'
                    ) {
                        data.onError(event);
                    }
                });
        }

        /**
         * Create event
         *
         * @param response
         * @returns {{response: *, isPropagationStopped: isPropagationStopped, stopPropagation: stopPropagation}}
         */
        function createEvent(response) {
            var isEventPropagationStopped = false;

            return {
                response: response,
                isPropagationStopped: isPropagationStopped,
                stopPropagation: stopPropagation
            };

            function stopPropagation() {
                isEventPropagationStopped = true;
            }

            function isPropagationStopped() {
                return isEventPropagationStopped;
            }
        }

    }

})();