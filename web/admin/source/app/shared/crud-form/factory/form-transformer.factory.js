(function () {
    'use strict';

    angular
        .module('crud.form')
        .factory('FormTransformer', FormTransformerFactory);

    /**
     * Form Transformer Factory
     *
     * @returns {Function}
     * @constructor
     */
    function FormTransformerFactory() {
        return function (transformer) {
            return build({
                SymfonyFormTransformer: SymfonyFormTransformer
            }[getName(transformer)]);
        };

        /**
         * Get transformer name
         *
         * @param value
         * @returns {string}
         */
        function getName(value) {
            return value.split('-')
                .map(function (word) {
                    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                })
                .join('');
        }

        /**
         * Create new Form Transformer Instance
         *
         * @param {Function} FormTransformer
         * @returns {AbstractFormTransformer|SymfonyFormTransformer}
         */
        function build(FormTransformer) {
            FormTransformer.prototype = new AbstractFormTransformer();
            return new FormTransformer();
        }
    }

    /**
     * Abstraction of Form Transformer
     *
     * @returns {{
     *  addRule: addRule,
     *  transform: transform
     * }}
     * @constructor
     */
    function AbstractFormTransformer() {
        var rules = [];

        return {
            addRule: addRule,
            transform: transform
        };

        /**
         * Add transformation rule
         *
         * @param {Function} rule
         */
        function addRule(rule) {
            rules.push(rule);
        }

        /**
         * Transform form data
         *
         * @param {Object} data
         * @returns {Object}
         */
        function transform(data) {
            var mf = {};
            rules = rules.forEach(function (rule) {
                    mf = angular.extend(mf, rule(data));
                }) || [];
            return mf;
        }
    }

    /**
     * Symfony From Transformer
     *
     * @constructor
     * @extends {AbstractFormTransformer}
     */
    function SymfonyFormTransformer() {
        /* jshint validthis: true */
        var self = this;

        self.addRule(function (value) {
            if (!value.hasOwnProperty('vars'))
                return;
            return form(value.vars);
        });

        self.addRule(function (data) {
            if (!data.hasOwnProperty('children'))
                return;
            return {
                children: Object.keys(data.children)
                    .map(function (key) {
                        if (!data.children[key].hasOwnProperty('vars'))
                            return;
                        return child(data.children[key].vars);
                    })
                    .filter(function (child) {
                        return child !== undefined;
                    })
            };
        });

        /**
         * Get form
         *
         * @param {Object} data
         * @returns {{
         *  name: String,
         *  action: String,
         *  method: String
         * }}
         */
        function form(data) {
            /* jshint -W106 */
            return {
                name: data.full_name,
                action: data.action,
                method: data.method
            };
        }

        /**
         * Get child
         *
         * @param {Object|{
         *  block_prefixes: Array<String>,
         *  full_name: String
         * }} data
         * @returns {{
         *  id: String,
         *  type: String,
         *  name: String,
         *  label: String,
         *  placeholder: String,
         *  options: Array<Object>
         * }}
         */
        function child(data) {
            /* jshint -W106 */
            return (function () {
                /* jshint validthis: true */
                var self = this;
                Object
                    .keys(arguments)
                    .forEach(function (i) {
                        if (this[i][Object.keys(this[i])[0]] === undefined)
                            return;
                        angular.extend(self, this[i]);
                    }, arguments);
                return self;
            }).apply({
                id: data.name,
                type: type(data.block_prefixes[1]),
                name: data.full_name
            }, [
                {label: label(data, 'label', 'translateLabel')},
                {placeholder: label(data, 'placeholder')},
                {options: options(data)}
            ]);

            /**
             * Get child type
             *
             * @param {String} value
             * @returns {String}
             */
            function type(value) {
                /* jshint eqeqeq: false */
                if (value == 'choice')
                    return 'select';
                return value;
            }

            /**
             * Get label or placeholder value of child
             *
             * @param {{
             *  label: String,
             *  attr: {
             *      translateLabel: String
             *  }
             * }} data
             * @param {String} field
             * @param {String=} attr
             * @returns {String|*}
             */
            function label(data, field, attr) {
                field = attr !== undefined ? attr : field;
                if (data.attr.hasOwnProperty(field))
                    return '{{ "' + data.attr[field] + '" | translate }}';
                return data[field];
            }

            /**
             * Get options
             *
             * @param {{
             *  choices: Array<Object>,
             *  placeholder: String
             * }} data
             * @returns {Array|undefined}
             */
            function options(data) {
                if (!data.hasOwnProperty('choices') || !(data.choices instanceof Array))
                    return;
                return [{
                    id: undefined,
                    text: data.placeholder
                }].concat(data.choices.map(function (choice) {
                    return {
                        id: choice.data,
                        text: choice.label
                    };
                }));
            }
        }
    }

})();