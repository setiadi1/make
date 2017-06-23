/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.models = oneApp.models || {};

	oneApp.models['banner-slide'] = Backbone.Model.extend({
		defaults: {
			id: '',
			'section-type': 'banner-slide'
		}
	});
})(window, Backbone, jQuery, _, oneApp);
