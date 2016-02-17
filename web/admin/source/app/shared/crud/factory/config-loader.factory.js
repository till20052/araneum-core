(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDConfigLoader', CRUDConfigLoader);

    CRUDConfigLoader.$inject = ['CRUDLoader'];

    /**
     * CRUD Config loader
     *
     * @param CRUDLoader
     * @constructor
     */
    function CRUDConfigLoader(CRUDLoader) {
        var Service = function () {
        };

        Service.prototype = new CRUDLoader();

        return new Service();
    }

})();