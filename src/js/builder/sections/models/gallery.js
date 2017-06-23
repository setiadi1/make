/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.models = oneApp.models || {};

	oneApp.models.gallery = oneApp.models.section.extend({
		defaults: function() {
			return {
				'section-type': 'gallery',
				'state': 'open',
				'gallery-items': [],
			}
		},

		parse: function(data) {
			var attributes = _(data).clone();
			attributes['gallery-items'] = _(attributes['gallery-items'])
				.map(function(item) {
					var itemModel = new oneApp.models['gallery-item'](item);
					itemModel.parent = this;
					return itemModel;
				}, this);

			return attributes;
		},

		toJSON: function() {
			var json = oneApp.models.section.prototype.toJSON.apply(this, arguments);
			json['gallery-items'] = _(json['gallery-items']).map(function(item) {
				return item.toJSON();
			});

			return json;
		}
	});
})(window, Backbone, jQuery, _, oneApp);
