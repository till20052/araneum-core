(function () {
    angular
        .module('app.formBuilder')
        .factory('formDataService', ['$resource', function($resource) {
            return {
                data: undefined,

                /**
                 * get data for form from server
                 * @param url route with form data
                 * @returns {json} JSON
                 */
                getFromDataFromUrl: function (url) {
                    this.data = $resource(url);

                    return this.data;
                }
            }
        }]);
})();