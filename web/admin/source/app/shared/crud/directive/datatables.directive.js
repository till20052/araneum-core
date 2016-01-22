/**
 * Created by artemm on 21.01.16.
 */
(function () {
    'use strict';

    angular
        .module('crud')
        .directive('crudDatatables', crudDatatables);

    crudDatatables.$inject = [];

    function crudDatatables() {

        return {
            restrict: 'E',
            link: link,
            scope: {

            }
        };

        function link() {

        }

    }

})();