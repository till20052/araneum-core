(function () {
    'use strict';

    angular
        .module('crud.form')
        .directive('form', form);

    form.$inject = ['FormFactory'];

    function form(FormFactory) {
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
                scope: scope,
                insert: function (form) {
                    element.replaceWith(form);
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
                angular.extend(form, transformer.transform(data));
                delete form.transform;
                self.insert(
                    FormFactory.create(form)
                        .build(self.scope)
                );
            }
        }

        /**
         * Create controls
         *
         * @param {object} data
         * @returns {jQuery}
         */
        function controls(data) {
            var buttons = [],
                keys = Object.keys(data);
            return $('<div />')
                .addClass([bootstrap.col.offsetLeft, bootstrap.col.right].join(' '))
                .append(
                    angular.forEach(data, function (data, key) {
                        console.log(data);
                        var button = $('<button class="btn btn-default" />')
                            .click(data.click)
                            .html('{{ "' + data.label + '" | translate }}');

                        if (data.hasOwnProperty('class'))
                            button.removeClass('btn-default')
                                .addClass(data.class);

                        if (keys.indexOf(key) + 1 < keys.length)
                            button.addClass('mr');

                        if (data.hasOwnProperty('icon'))
                            button.prepend($('<em class="mr" />').addClass(data.icon));

                        this.push(button);
                    }, buttons) && buttons
                );
        }

    }

})();