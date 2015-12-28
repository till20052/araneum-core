(function () {
    'use strict';

    angular
        .module('app.users')
        .controller('UserSettingsController', UserSettingsController);

    UserSettingsController.$inject = ['User', '$rootScope'];

    function UserSettingsController(User, $rootScope) {
        /* jshint validthis: true */
        var vm = this;

        console.log(vm);

        //(function (vm) {
        //
        //    vm.layout = User.getSettings();
        //    if (vm.layout) {
        //        $rootScope.app.layout = vm.layout;
        //    }
        //
        //    vm.$watch('layout', function () {
        //        User.setSettings(vm.layout);
        //    }, true);
        //
        //})($scope);

    }

})();