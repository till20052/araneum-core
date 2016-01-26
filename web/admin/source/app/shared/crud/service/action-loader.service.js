(function () {
    'use strict';

    angular
        .module('crud')
        .service('CRUDActionLoader', CRUDActionLoader);

    CRUDActionLoader.$inject = ['CRUDLoader'];

    function CRUDActionLoader(CRUDLoader) {
        return {};
    }

})();