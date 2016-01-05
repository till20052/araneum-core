(function() {
    "use strict";

    angular
        .module('app.action-builder')
        .factory('topActionBuilderService', topActionBuilderService);

    function topActionBuilderService() {
        var actions = {
            groupTemplate: [
                '<div class="btn-group pull-right">',
                '</div>'
            ],
            actionTemplate: [
                '<button type=button class="btn btn-sm">',
                '<em></em>',
                '</button>'
            ],
            actionGroups: undefined,
            sortedByGroupButtons: undefined,
            actionsData: undefined,
            actionsTemplate: undefined,
            actionsMassTemplates: undefined,
            scope: undefined,

            setData: setData,
            parseData: parseData,
            getGroupTemplate: getGroupTemplate,
            getActionTemplate: getActionTemplate,
            build: buildActions,
            addOptions: addOptions,
            getActionsTemplate: getActionsTemplate
        };

        return actions;

        /**
         * Action data from server
         * @param actionsData
         * @param model link on object
         * @param scope 
         */
        function setData(actionsData, model, scope) {
            this.actionGroups = undefined;
            this.sortedByGroupButtons = undefined;
            this.actionsData = undefined;
            this.actionsTemplate = undefined;
            this.actionsMassTemplates = undefined;

            this.actionsData = actionsData;
            this.scope = scope;
            this.model = model;
            this.parseData();
        }


        /**
         * Parsed data from data server
         */
        function parseData() {
            this.sortedByGroupButtons = {};
            var i = 0;

            for (var group in this.actionsData) {
                var _group = this.actionsData[group];
                var groupMass = [];
                for (var button in _group) {
                    _group[button].id = i;
                    i++;
                    groupMass.push(_group[button]);
                }
                this.sortedByGroupButtons[group] = groupMass;
            }

            this.build();
        }

        /**
         * Get group template
         */
        function getGroupTemplate() {
            return this.groupTemplate.join(' ');
        }

        /**
         * Get action template
         * return {string}
         */
        function getActionTemplate() {
            return this.actionTemplate.join(' ');
        }

        /**
         * Build actions
         */
        function buildActions() {
            var templateMass = [],
                groupMass = this.sortedByGroupButtons,
                i = 0;

            for (var _group in groupMass) {
                var buttonLength = groupMass[_group].length,
                    group = $(this.getGroupTemplate());

                if (i > 0) {
                    group.addClass('mr');
                }
                i++;

                for (var j = 0; j < buttonLength; j++) {
                    var action = groupMass[_group][j];
                    group.append(this.addOptions(action));
                }

                templateMass.push(group);
            }

            this.actionsMassTemplates = templateMass;
        }

        /**
         * Add options to action
         * @param button
         * @returns {jQuery|HTMLElement}
         */
        function addOptions(button) {
            var actionTemplate = $(this.getActionTemplate()),
                actionConfig = {
                    id: button.id,
                    type: 'top',
                    config: {},
                };

            if (button.confirm !== undefined) {
                actionConfig.config = button.confirm;
            }

            if (button.callback) {
                actionConfig.config.callback = button.callback;
            };

            if (button.resource) {
                actionConfig.config.url = button.resource;
                actionConfig.config.type = 'resource';
            }

            if (button.form) {
                actionConfig.config.url = button.form;
                actionConfig.config.type = 'form';
            }

            $(actionTemplate)
                .addClass(button.display.btnClass)
                .attr({
                    'data-item': button.callback,
                    tooltip: button.display.label,
                    'ng-click': 'vm.actionClick($event)',
                    'data-conf': button.id + ',top',
                    'data-target': '#myModal'
                });

            $('em', actionTemplate).addClass(button.display.icon);
            this.scope.$broadcast('addActionConfig', actionConfig);

            return actionTemplate;
        }

        /**
         * Return actions template in html
         * @returns {string} html
         */
        function getActionsTemplate() {
            var actionMassTemplates = this.actionsMassTemplates,
                actionMassTemplatesLength = actionMassTemplates.length;

            if (this.actionsTemplate === undefined) {
                this.actionsTemplate = '';

                for (var i = 0; i < actionMassTemplatesLength; i++) {
                    this.actionsTemplate += actionMassTemplates[i].get(0).outerHTML;
                }
            }

            return this.actionsTemplate;
        }
    }
})();