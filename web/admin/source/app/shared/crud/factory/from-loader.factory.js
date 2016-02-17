(function () {
    'use strict';

    angular
        .module('crud')
        .factory('CRUDFormLoader', CRUDFormLoader);

    CRUDFormLoader.$inject = ['CRUDLoader'];

    /**
     * CRUD Form loader
     *
     * @param CRUDLoader
     * @constructor
     */
    function CRUDFormLoader(CRUDLoader) {
        var Service = function () {
        };

        Service.prototype = new CRUDLoader();

        return new Service();
    }

})();