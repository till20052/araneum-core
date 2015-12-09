(function (angular) {

    'use strict';

    angular
        .module('app.users')
        .service('UserAuth', ['$http', 'User', '$state', UserAuth]);

    /**
     * User auth service constructor
     *
     * @param $http
     * @param User
     * @param $state
     * @returns {{initLoginForm: initLoginForm, onStartChangeState: onStartChangeState, login: login, logout: logout}}
     * @constructor
     */
    function UserAuth($http, User, $state) {

        var _csrf_token;
        var targetState = {name: 'app.users'};

        return {
            initLoginForm: initLoginForm,
            onStartChangeState: onStartChangeState,
            login: login,
            logout: logout
        };

        /**
         * Change state listener
         *
         * @param event
         * @param toState
         */
        function onStartChangeState(event, toState) {
            if (!User.isAuthorized()) {
                if ($.inArray(toState.name, ['login', 'resetting', 'reset']) < 0) {
                    event.preventDefault();
                    $state.go('login');
                }
                targetState = typeof  toState.defaultState != 'undefined'
                    ? $state.get(toState.defaultState)
                    : toState;
            }
        }

        /**
         * Initialize login firm
         *
         * @param callback
         */
        function initLoginForm(callback) {
            $http
                .get('/login')
                .success(function (response) {
                    _csrf_token = response;
                    if (typeof callback == 'function') {
                        callback(response);
                    }
                })
                .error(function (response, statusCode) {
                    if (statusCode == 403 || statusCode == 401) {
                        User.data($.extend(response, {isAuthorized: true}));
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
                .post('/login_check', $.param({
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
                        User.data($.extend(response, {isAuthorized: true}));
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

})(angular);