(function() {
	angular
		.module('app.formBuilder')
		.factory('fromBuilderService', function() {
			return {
				templates: {
					text: '<div class="form-group">' +
					'<label></label>' +
					'<input type="text" class="form-control" id="usr">' +
					'</div>',
					datetime: '<div class="form-group">' +
					'<label></label>' +
					'<input type="date" class="form-control" id="usr">' +
					'</div>',
					hidden: '<div class="form-group">' +
					'<label></label>' +
					'<input type="hidden" class="form-control" id="usr">' +
					'</div>',
					choice: '<select class="form-control"></select>'
				},
				data: {},
				sortedFromData: undefined,
				fromTemplate: undefined,
				fromData: {},
				formOptions: {},
				fromArray: {},
				elementsTemplateArray: {},

				/**
				 * Set json form data from server
				 * @param data
				 */
				setData: function(data) {
					if ($.isEmptyObject(data)) {
						return false;
					}

					this.data = data;

					this.convertDataToArray();
				},

				/**
				 * Return form object from server
				 * @returns {formBuilder.data|{}}
				 */
				getData: function() {
					return this.data;
				},

				/**
				 * Get template by element name
				 * @param element element name
				 */
				getTemplateByElement: function(element) {
					var elementTemplate = undefined;
					
					switch (element.type) {
						case 'text':
							elementTemplate = $(this.templates[element.type]);
							elementTemplate = this.addOptionsToInputElement(element, elementTemplate);
							break;

						case 'hidden':
							elementTemplate = $(this.templates[element.type]);
							elementTemplate = this.addOptionsToInputElement(element, elementTemplate);
							break;

						case 'email':
							elementTemplate = $(this.templates[element.type]);
							elementTemplate = this.addOptionsToInputElement(element, elementTemplate);
							break;

						case 'datetime':
							elementTemplate = $(this.templates[element.type]);
							elementTemplate = this.addOptionsToInputElement(element, elementTemplate);
							break;

						case 'textarea':
							elementTemplate = $(this.templates[element.type]);
							break;

						case 'choice':
							elementTemplate = $(this.templates[element.type]);
							elementTemplate = this.addOptionsToSelectElement(element, elementTemplate);
							break;
					}

					return elementTemplate;
				},

				/**
				 * Get element template by name
				 * @param elementName element name
				 */
				getElementTemplateByName: function(elementName) {
					var elementTemplate = this.fromData[elementName];

					if (elementTemplate === undefined) {
						throw 'Element\'s template not found';
					}

					return elementTemplate;
				},

				/**
				 * Add attributes to input
				 * @param el object with data
				 * @param template base html template
				 * @returns {*} html template
				 */
				addOptionsToSelectElement: function(el, template) {
					$('select', template).attr($.extend(el.attrs, {
						name: el.name,
						ngModel: el.name,
						placeholder: '{{"'+el.attrs.placeholder+'" | translate}}'
					})).val(el.value);

					$('label', template).attr({
						'for': el.value,
						'translate': el.translateLabel
					});

					$('select', template).attr('name', el.name);
					$('select', template)
					               .append($('<option></option>').attr({
						               'selected': ''
					               }).text('{{"'+el.emptyValue+'" | translate}}'));


					for (var key in el.choices) {
						$('select', template).append($('<option />').val(el.choices[key].value).text(el.choices[key].label));
					}
					$('label', template).attr('translate', el.translateLabel)

					return template;
				},

				/**
				 * Add attributes to input
				 * @param el object with data
				 * @param template base html template
				 * @returns {*} html template
				 */
				addOptionsToInputElement: function(el, template) {
					$('input', template).attr($.extend(el.attrs, {
						name: el.name,
						ngModel: el.name,
						placeholder: '{{"'+el.attrs.placeholder+'" | translate}}'
					})).val(el.value);

					$('label', template).attr({
						'for': el.value,
						'translate': el.translateLabel
					});

					return template;
				},

				/**
				 * add elements to array
				 */
				generateElementsTemplateArray: function() {
					var elements = this.getFromArray(),
						mass = [];

					for (var elem in elements) {
						var el = this.getTemplateByElement(elements[elem]);
						mass.push(el[0].outerHTML);
					}

					this.elementsTemplateArray = mass;
				},

				/**
				 * Build form form array to string
				 * @returns {string} html template string
				 */
				buildForm: function() {
					if (this.elementsTemplateArray.length === undefined) {
						return '';
					}

					this.fromTemplate = this.elementsTemplateArray.join(' ');

					return this.fromTemplate;
				},

				/**
				 * Return form template in array by elements
				 * @returns {formBuilder.fromArray|{}}
				 */
				getFromArray: function() {
					return this.fromArray;
				},

				/**
				 * Return form template in string
				 * @returns object {*}
				 */
				getFromTemplate: function() {
					return this.elementsTemplateArray;
				},

				/**
				 * Sort data
				 * @returns {*}
				 */
				convertDataToArray: function() {
					var options = {},
						elements = {},
						formEl = this.data.children;

					// set from options
					options.name = this.data.vars.name;
					options.id = this.data.vars.id;
					options.attrs = this.data.vars.attr;
					options.action = this.data.vars.action;
					this.setFormOptions(options);
					for (var el in formEl) {
						var currEl = formEl[el]['vars'];
						if (angular.isArray(formEl[el]['children'])) {
							elements[currEl.full_name] = {
								name: currEl.full_name,
								name_create: currEl.name,
								type: currEl.block_prefixes[1],
								attrs: {'placeholder': currEl.attr.placeholder, 'required': currEl.required},
								value: currEl.value,
								choices: currEl.choices,
								multipart: currEl.multipart,
								label: currEl.label,
								translateLabel: currEl.attr.translateLabel,
								emptyValue: currEl.empty_value
							}
						}

						if (angular.isObject(formEl[el]['children'])) {
							for (var elem in formEl[el]['children']) {
								currEl = formEl[el]['children'][elem]['vars'];
								elements[currEl.full_name] = {
									name: currEl.full_name,
									name_create: currEl.name,
									type: currEl.block_prefixes[1],
									attrs: {'placeholder': currEl.attr.placeholder, 'required': currEl.required},
									value: currEl.value,
									choices: currEl.choices,
									label: currEl.label,
									translateLabel: currEl.attr.translateLabel,
									emptyValue: currEl.empty_value
								}
							}
						}

					}

					this.fromArray = elements;
					this.generateElementsTemplateArray();
				},

				/**
				 * Set from options
				 * @param options
				 */
				setFormOptions: function(options) {
					this.formOptions = options;
				},

				/**
				 * Get form options
				 * @returns {formBuilder.formOptions|{}}
				 */
				getFormOptions: function() {
					return this.formOptions;
				},

				/**
				 * Add Buttons to From
				 */
				getButtonsForForm: function(url, id) {
					var templateButtons = '<div class="col-lg-2">' +
						'<fieldset>' +
							'<div class="form-group">' +
								'<button type="submit" class="btn btn-default mr-sm" id="reset" translate="admin.general.RESET">' +
									'<em class="icon-refresh mr-sm"></em>' +
									'Reset' +
								'</button>' +
								'<button type="submit" class="btn btn-primary" id="search" translate="admin.general.SEARCH">' +
									'<em class="icon-magnifier mr-sm"></em>' +
									'Search' +
								'</button>' +
							'</div>' +
						'</fieldset>' +
						'</div>';
					templateButtons = $(templateButtons);
					$(templateButtons).find('#search').attr('ng-click', 'vm.search("' + url + '")');
					$(templateButtons).find('#reset').attr('ng-click', 'vm.reset($event,"' + url + '","' + id + '")');

					return templateButtons[0].outerHTML;
				}
			}
		});
})();