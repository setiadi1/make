/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.models = oneApp.models || {};

	oneApp.models.text = oneApp.models.section.extend({
		defaults: function() {
			return {
				'section-type': 'text',
				'state': 'open',
				'columns': []
			}
		},

		parse: function(data) {
			var attributes = _(data).clone();

			attributes['columns'] = _(attributes['columns']).values().map(function(column) {
				var columnModel = new oneApp.models['text-item'](column);
				columnModel.set('parentID', data.id);
				return columnModel;
			});

			return attributes;
		},

		toJSON: function() {
			var json = oneApp.models.section.prototype.toJSON.apply(this, arguments);
			var copyColumns = _(json['columns']).clone();

			_(json['columns']).each(function(column, index) {
				if (column.hasOwnProperty('attributes')) {
					copyColumns[index] = column.attributes;
				} else {
					copyColumns[index] = column;
				}
			});

			console.log(json);

			return json;
		},

		updateOrder: function(ids) {
			var ids = _(ids);
			var json = oneApp.models.section.prototype.toJSON.apply(this, arguments);
			var columns = _(json['columns']).clone();
			var orderedColumns = {
				1: {},
				2: {},
				3: {},
				4: {}
			};

			ids.each(function(id, index) {
				var intIndex = parseInt(index, 10)+1;
				var desiredColumn;

				_.each(columns, function(model) {
					if (parseInt(model.get('id'), 10) === parseInt(id, 10)) {
						desiredColumn = model;
					}
				});

				orderedColumns[intIndex] = desiredColumn;
			});

			this.set('columns', orderedColumns);
		}
	});
})(window, Backbone, jQuery, _, oneApp);
