(function () {
    'use strict';

    angular
        .module('app.dashboard')
        .directive('wordsDeclension', wordsDeclensionDirective);

    wordsDeclensionDirective.$inject = [];

    function wordsDeclensionDirective() {
        /* jshint eqeqeq: false,
         evil: true,
         -W018 */

        return {
            restrict: 'A',
            controller: ['$scope', '$translate', controller],
            link: link,
            scope: {
                wordsDeclension: '@',
                wdCount: '='
            }
        };

        function controller(scope, $translate) {
            scope.incline = function (count, text) {
                if (!(text.length > 0)) {
                    return;
                }

                return {
                    en: en,
                    ru: ru
                }[$translate.use()](count, text);

                function en(c, text) {
                    return c != 1 ? text[1] : text[0];
                }

                function ru(c, text) {
                    c = Math.abs(c) % 100;
                    if (c > 10 && c < 20) {
                        return text[2];
                    }

                    c = c % 10;
                    if (c > 1 && c < 5) {
                        return text[1];
                    }

                    if (c == 1) {
                        return text[0];
                    }

                    return text[2];
                }
            };
        }

        function link(scope, element, attrs) {
            var text = [];

            function incline() {
                element.html(scope.incline(scope.wdCount, text));
            }

            scope.$watch('wdCount', function () {
                incline();
            });

            attrs.$observe('wordsDeclension', function (value) {
                if (/\[.*\]/i.test(value)) {
                    text = eval(value);
                }
                incline();
            });
        }
    }

})();