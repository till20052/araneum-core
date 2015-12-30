(function () {
    'use strict';

    angular
        .module('layout')
        .factory('layout', Layout);

    Layout.$inject = [];

    /**
     * Layout Service
     *
     * @returns {{
     *  isFixed: boolean,
     *  isCollapsed: boolean,
     *  isBoxed: boolean,
     *  isRTL: boolean,
     *  horizontal: boolean,
     *  isFloat: boolean,
     *  asideHover: boolean,
     *  theme: null
     * }}
     * @constructor
     */
    function Layout() {
        return {
            isFixed: true,
            isCollapsed: false,
            isBoxed: false,
            isRTL: false,
            horizontal: false,
            isFloat: false,
            asideHover: false,
            theme: null
        };
    }

})();