(function () {
    'use strict';

    angular
        .module('crud.form')
        .directive('form', form);

    form.$inject = ['FormFactory', '$compile'];

    function form(FormFactory, $compile) {
        return {
            link: link,
            restrict: 'E',
            scope: {
                form: '=manifest'
            }
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         */
        function link(scope, element) {
            if (!(scope.form instanceof Object))
                return;

            manifest.call({
                insert: function (form) {
                    element.replaceWith($compile(form)(scope));
                }
            }, scope.form);
        }

        /**
         *
         * @param {Object} form
         * @todo need to link loader
         */
        function manifest(form) {
            /* jshint validthis: true */
            var self = this,
                transformer;

            if (form.hasOwnProperty('transformer')) {
                transformer = form.transformer;
                delete form.transformer;
                form.transform = transform;
            }

            /**
             * Transform data by Form Transformer
             *
             * @param {Object} data
             */
            function transform(data) {
                delete form.transform;
                angular.extend(form, transformer.transform(data));
                self.insert(FormFactory.create(form));
            }
        }

    }

})();