(function() {
    'use strict';

    angular
        .module('custom', [
            // request the the entire framework
            'araneum',
            // or just modules
            'app.core',
            'app.sidebar'
            /*...*/
        ]);
})();