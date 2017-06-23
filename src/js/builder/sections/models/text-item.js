/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.models = oneApp.models || {};

	oneApp.models['text-item'] = Backbone.Model.extend({
		defaults: {
			id: '',
			'section-type': 'text-item',
			content: '',
		}
	});
})(window, Backbone, jQuery, _, oneApp);
