(function () {
    'use strict';

    angular
        .module('crud.form')
        .directive('form', form);

    form.$inject = ['Form', '$compile'];

    function form(Form, $compile) {
        return {
            link: link,
            restrict: 'E',
            scope: {
                manifest: '='
            }
        };

        /**
         * Directive link
         *
         * @param {{
         *  manifest: {
         *      events: Object
         *  }
         * }} scope
         * @param element
         */
        function link(scope, element) {
            if (!(scope.manifest instanceof Object))
                return; //throw console.error('[ERROR]: Incoming Manifest doesn\'t instance of Object');

            if (!scope.manifest.hasOwnProperty('events'))
                scope.manifest.events = {};

            [{
                afterBuild: compile
            }].forEach(function (eventMap) {
                var name = Object.keys(eventMap)[0],
                    event = eventMap[name];

                if (this.hasOwnProperty(name))
                    return (this[name] = [event, this[name]]);

                this[name] = event;
            }, scope.manifest.events);

            scope.form = builder(scope.manifest);

            /**
             * Compile form
             *
             * @param {jQuery} form
             */
            function compile(form) {
                element.replaceWith($compile(form)(scope));
            }
        }

        /**
         * @param manifest
         */
        function builder(manifest) {
            return angular.extend(manifest, initialize(manifest));

            /**
             * Initialize form builder
             *
             * @param manifest
             * @returns {{}}
             */
            function initialize(manifest) {
                var $this = angular.extend({}, new FormHandler(manifest));

                if (manifest.hasOwnProperty('events') && manifest.events instanceof Object)
                    angular.extend($this, new EventsRegister(manifest.events));

                if (manifest.hasOwnProperty('transformer'))
                    angular.extend($this, new FormTransformer(manifest.transformer));

                return $this;
            }

            /**
             * Handler of Form
             *
             * @returns {*}
             * @constructor
             */
            function FormHandler(form) {
                /* jshint validthis: true */
                var $form = form,
                    $data = {},
                    $this = angular.extend(this, {
                        data: data,
                        getChildren: getChildren,
                        getChild: getChild,
                        getView: getView,
                        build: build
                    });

                return $this;

                /**
                 * Set|Get data value of form
                 *
                 * @param key
                 * @param val
                 * @returns {*}
                 */
                function data(key, val) {
                    if (key === undefined)
                        return $data;

                    if (key instanceof String) {
                        if (val !== undefined) {
                            $data[key] = val;

                            return $this;
                        }

                        if ($data.hasOwnProperty(key))
                            return $data[key];
                    }

                    if (key instanceof Object)
                        $data = key;

                    return $this;
                }

                /**
                 * Get children of form
                 *
                 * @returns {Array<Object>}
                 */
                function getChildren() {
                    return $form.children;
                }

                /**
                 * Get child of form by index
                 *
                 * @param {Number} index
                 * @returns {Object}
                 */
                function getChild(index) {
                    return $form.children[index] !== undefined ? $form.children[index] : undefined;
                }

                /**
                 * Get view of form
                 *
                 * @returns {*}
                 */
                function getView() {
                    return $form.view;
                }

                /**
                 * Build form
                 */
                function build(form) {
                    delete this.build;
                    this.event('afterBuild')
                        .invoke(new Form((angular.extend($form, form))));
                }
            }

            /**
             * Event Register
             *
             * @param {Object<String, Function|Array<Function>>} events
             * @returns {{event: get}}
             * @constructor
             */
            function EventsRegister(events) {
                var $events = {},
                    $this = angular.extend(this, {
                        event: get
                    });

                delete manifest.events;
                Object.keys(events)
                    .forEach(function (name) {
                        if (!(events[name] instanceof Array))
                            events[name] = [events[name]];
                        events[name].forEach(function (event) {
                            set(name, event);
                        });
                    });

                return $this;

                /**
                 * Append event to array of events by name
                 *
                 * @param {String} name
                 * @param {Function} event
                 * @returns {EventsRegister}
                 */
                function set(name, event) {
                    if (!$events.hasOwnProperty(name))
                        $events[name] = create();

                    $events[name].push(event);

                    return $this;
                }

                /**
                 * Get array of events by name
                 *
                 * @param {String} name
                 * @returns {ArrayOfEvents|undefined}
                 */
                function get(name) {
                    return $events.hasOwnProperty(name) ? $events[name] : undefined;
                }

                /**
                 * Create array of events
                 *
                 * @returns {ArrayOfEvents}
                 */
                function create() {
                    function ArrayOfEvents() {
                        /* jshint validthis: true */
                        var $this = angular.extend(this, {
                            invoke: invoke
                        });

                        return $this;

                        /**
                         * Invoke each event which was registered in this array of events
                         */
                        function invoke() {
                            $this.forEach(function (event) {
                                event.apply(undefined, this);
                            }, arguments);
                        }
                    }

                    ArrayOfEvents.prototype = Object.create(Array.prototype);

                    return new ArrayOfEvents();
                }
            }

            /**
             * Handler of Form Transformer
             *
             * @param {SymfonyFormTransformer|*} transformer
             * @constructor
             */
            function FormTransformer(transformer) {
                /* jshint validthis: true */
                var $this = angular.extend(this, {
                    transform: transform
                });

                delete manifest.transformer;

                return $this;

                /**
                 * Transform incoming data by Form Transformer
                 *
                 * @param {Object} data
                 */
                function transform(data) {
                    delete manifest.transform;
                    this.build(transformer.transform(data));
                    return manifest;
                }
            }
        }

    }

})();