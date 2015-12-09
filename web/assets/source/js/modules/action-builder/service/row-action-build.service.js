(function () {
	"use strict";

	angular
		.module('app.action-builder')
		.factory('rowActionBuilderService', rowActionBuilderService);

	rowActionBuilderService.$inject = ['topActionBuilderService'];

	function rowActionBuilderService(topActionBuilderService) {

		var factory = {};
		$.extend(factory,topActionBuilderService);

		factory.actionTemplate = [
			'<li>',
			'<a href="#">',
			'<em></em>',
			'</a>',
			'</li>'
		];

		factory.dividerTemplate = [
			'<li class="divider">',
			'</li>'
		];

		factory.wrapperTemplate = [
			'<div dropdown="dropdown" class="btn-group">',
			'<button type="button" dropdown-toggle="" class="btn btn-xs dropdown-toggle btn-default" tooltip="Actions">',
			'<em class="icon-settings"></em>',
			'</button>',
			'<ul role="menu" class="dropdown-menu dropdown-menu-right">',
			'</ul>',
			'</div>'
		];

		factory.getDividerTemplate = getDividerTemplate;
		factory.build = buildActions;
		factory.getActionsTemplate = getActionsTemplate;
		factory.getWrapperTemplate = getWrapperTemplate;
		factory.addOptions = addOptions;

		function buildActions() {
			var templateMass = [],
				groupMass = this.sortedByGroupButtons,
				divider = $(this.getDividerTemplate());

			for ( var _group in groupMass ) {
				var buttonLength = groupMass[_group].length;

				for ( var j = 0; j < buttonLength; j++ ) {
					var action = groupMass[_group][j];
					templateMass.push(this.addOptions(action));
				}

				templateMass.push(divider);
			}
			
			this.actionsMassTemplates = templateMass;
		}

		function getActionsTemplate() {
			var actionMassTemplates = this.actionsMassTemplates,
				actionMassTemplatesLength = actionMassTemplates.length,
				wrapper = $(this.getWrapperTemplate());

			if ( this.actionsTemplate === undefined ) {
				this.actionsTemplate = '';

				for ( var i = 0; i < actionMassTemplatesLength; i++ ) {
					this.actionsTemplate += actionMassTemplates[i].get(0).outerHTML;
				}
			}
			
			$('ul', wrapper ).append(this.actionsTemplate);

			return this.actionsTemplate = wrapper.get(0).outerHTML;
		}

		function addOptions(button) {
			var actionTemplate = $(this.getActionTemplate()),
				actionConfig = {
					id: button.id,
					config: button.confirm,
					type: 'row'
				};

			if ( button.resource ) {
				actionConfig.config.url = button.resource;
				actionConfig.config.type = 'resource';
			}

			if ( button.form ) {
				actionConfig.config.url = button.form;
				actionConfig.config.type = 'form';
			}

			$('em', actionTemplate ).addClass(button.display.icon);
			$('a', actionTemplate ).append(button.display.label);
			$('a', actionTemplate ).attr('ng-click', 'vm.actionClick($event,' + this.model  +')');
			$('a', actionTemplate ).attr('data-conf', button.id + ',row');
			$(actionTemplate ).attr('data-target', '#myModal');

			this.scope.$broadcast('addActionConfig', actionConfig);

			return actionTemplate;
		}

		function getDividerTemplate() {
			return this.dividerTemplate.join(' ');
		}

		function getWrapperTemplate() {
			return this.wrapperTemplate.join(' ');
		}

		return factory;
	}
})();