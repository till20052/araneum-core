(function () {
    'use strict';

    angular
        .module('app.panels')
        .directive('paneltool', paneltool);

    paneltool.$inject = ['$compile', '$timeout'];

    /**
     * PanelTool Directive
     *
     * @param $compile
     * @param $timeout
     * @returns {Object}
     */
    function paneltool($compile, $timeout) {
        return {
            link: link,
            restrict: 'E',
            scope: false
        };

        /**
         * Directive link
         *
         * @param scope
         * @param element
         * @param attrs
         */
        function link(scope, element, attrs) {
            var templates = {
                /* jshint multistr: true */
                collapse: '<a href="#" panel-collapse="" uib-tooltip="{{\'admin.dashboard.widget.COLLAPSE\' | translate}}" ng-click="{{panelId}} = !{{panelId}}"> \
                        <em ng-show="{{panelId}}" class="fa fa-plus"></em> \
                        <em ng-show="!{{panelId}}" class="fa fa-minus"></em> \
                      </a>',
                dismiss: '<a href="#" panel-dismiss="" uib-tooltip="{{\'admin.dashboard.widget.CLOSE\' | translate}}">\
                       <em class="fa fa-times"></em>\
                     </a>',
                refresh: '<a href="#" panel-refresh="" data-spinner="{{spinner}}" uib-tooltip="{{\'admin.dashboard.widget.REFRESH\' | translate}}">\
                       <em class="fa fa-refresh"></em>\
                     </a>'
            };

            var tools = scope.panelTools || attrs;

            $timeout(function () {
                element.html(getTemplate(element, tools)).show();
                $compile(element.contents())(scope);

                element.addClass('pull-right');
            });

            function getTemplate(elem, attrs) {
                var temp = '';
                attrs = attrs || {};
                if (attrs.toolCollapse)
                    temp += templates.collapse.replace(/{{panelId}}/g, (elem.parent().parent().attr('id')));
                if (attrs.toolDismiss)
                    temp += templates.dismiss;
                if (attrs.toolRefresh)
                    temp += templates.refresh.replace(/{{spinner}}/g, attrs.toolRefresh);
                return temp;
            }
        }// link
    }

})();
