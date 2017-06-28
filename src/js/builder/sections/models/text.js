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

		parse: function( data ) {
			var attributes = _( data ).clone();

			attributes.columns = _( attributes.columns ).map( function( column ) {
				column = new oneApp.models['text-item']( column );
				column.parent = this;
				return column;
			}, this );

			return attributes;
		},

		toJSON: function() {
			var json = oneApp.models.section.prototype.toJSON.apply(this, arguments);

			json.columns = _( json.columns ).map( function( column ) {
				return column.hasOwnProperty( 'attributes' ) ? column.toJSON() : column;
			} );

			return json;
		}
	});
})(window, Backbone, jQuery, _, oneApp);
