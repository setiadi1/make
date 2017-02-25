/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.views = oneApp.views || {}

	oneApp.views['banner-slide'] = oneApp.views.item.extend({
		events: function() {
			return _.extend({}, oneApp.views.item.prototype.events, {
				'click .ttfmake-banner-slide-remove': 'onSlideRemove',
				'overlay-open': 'onOverlayOpen',
			});
		},

		initialize: function (options) {
			this.template = _.template(ttfMakeSectionTemplates['banner-slide'], oneApp.builder.templateSettings);
		},

		render: function () {
			var html = this.template(this.model)
			this.setElement(html);

			return this;
		},

		onSlideRemove: function (evt) {
			evt.preventDefault();

			if (!confirm('Are you sure you want to trash this banner slide permanently?')) {
				return;
			}

			var $stage = this.$el.parents('.ttfmake-banner-slides');

			// Fade and slide out the section, then cleanup view
			this.$el.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, oneApp.builder.options.closeSpeed, function() {
				this.$el.trigger('slide-remove', this);
				this.remove();
			}.bind(this));
		},

		onOverlayOpen: function (e, $overlay) {
			e.stopPropagation();

			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update slide');
		},
	});
})(window, Backbone, jQuery, _, oneApp);