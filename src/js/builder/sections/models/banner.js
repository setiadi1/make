/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.models = oneApp.models || {};

	oneApp.models.banner = oneApp.models.section.extend({
		defaults: function() {
			return {
				'section-type': 'banner',
				'state': 'open',
				'banner-slides': [],
			}
		},

		parse: function(data) {
			var attributes = _(data).clone();
			attributes['banner-slides'] = _(attributes['banner-slides'])
				.map(function(slide) {
					var slideModel = new oneApp.models['banner-slide'](slide);
					slideModel.parent = this;
					return slideModel;
				}, this );

			return attributes;
		},

		toJSON: function() {
			var json = oneApp.models.section.prototype.toJSON.apply(this, arguments);
			json['banner-slides'] = _(json['banner-slides']).map(function(slide) {
				return slide.toJSON();
			});

			return json;
		}
	});
})(window, Backbone, jQuery, _, oneApp);
