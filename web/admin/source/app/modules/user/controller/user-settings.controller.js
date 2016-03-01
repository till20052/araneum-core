(function () {
    'use strict';

    angular
        .module('app.users')
        .controller('UserSettingsController', UserSettingsController);

    UserSettingsController.$inject = ['User', 'layout'];

    /**
     * Controller of User Settings
     *
     * @param User
     * @param layout
     * @constructor
     */
    function UserSettingsController(User, layout) {
        /* jshint validthis: true,
            eqeqeq: false */
        var vm = this;

        vm.layout = layout;
        vm.changeLayout = changeLayout;

        activate();

        /**
         * Activation
         */
        function activate(){
            User.init(function(service){
                for(var key in service.settings){
                    vm.layout[key] = service.settings[key];
                }
            });
            layout = User.settings;
        }

        /**
         * Change Layout Event invokes in case if click at any ui controls
         *
         * @param $event
         */
        function changeLayout($event){
            if($($event.target).prop('tagName') == 'INPUT'){
                User.setSettings(vm.layout);
            }
        }
    }

})();