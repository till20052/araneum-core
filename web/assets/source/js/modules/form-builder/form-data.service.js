(function() {
    angular
        .module('app.formBuilder')
        .factory('formDataService', ['$resource', function($resource) {
            return {
                data: undefined,

                /**
                 * Get data for form from server
                 * @param {string} url route with form data
                 * @returns {json} JSON
                 */
                getFromDataFromUrl: function(url) {
                    this.data = $resource(url, {}, {
                        method: 'GET'
                    }).get();

                    return this.data;
                }
            };
        }]);
})();
