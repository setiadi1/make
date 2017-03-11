/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
	'use strict';

	oneApp.views = oneApp.views || {}

	oneApp.views['text-item'] = oneApp.views.item.extend({
		el: '',
		elSelector: '',
		className: 'ttfmake-text-column',

		events: function() {
			return _.extend({}, oneApp.views.item.prototype.events, {
				'overlay-open': 'onOverlayOpen',
				'click .ttfmake-text-column-remove': 'onColumnRemove',
				'overlay-close': 'onOverlayClose'
			});
		},

		initialize: function (options) {
			this.template = _.template(ttfMakeSectionTemplates['text-item'], oneApp.builder.templateSettings);
		},

		render: function () {
			var self = this;

			var html = this.template(this.model);
			this.setElement(html);

			if ('' !== this.model.get('content')) {
				$('.edit-content-link', this.$el).addClass('item-has-content');
			}

			setTimeout(function() {
				self.updateIframeHeight();
			}, 1000);

			return this;
		},

		onOverlayOpen: function (e, $overlay) {
			e.stopPropagation();

			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update column');
		},

		onOverlayClose: function(e, changeset) {
			e.stopPropagation();

			this.model.set(changeset);

			if (this.model.hasChanged()) {
				this.$el.trigger('model-item-change');
			}

			this.updateIframeHeight();
		},

		updateIframeHeight: function() {
			var $iframe = this.$el.find('iframe');
			$iframe.css('height', 'auto');

			var iframeContentHeight = $iframe.contents().height();

			if (iframeContentHeight > 500) {
				iframeContentHeight = 500;
			}

			if (iframeContentHeight < 275) {
				iframeContentHeight = 275;
			}

			$iframe.height(iframeContentHeight);
		},

		onColumnRemove: function(evt) {
			evt.preventDefault();

			if (!confirm('Are you sure you want to trash this column permanently?')) {
				return;
			}

			var $stage = this.$el.parents('.ttfmake-text-columns-stage');

			this.$el.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, oneApp.builder.options.closeSpeed, function() {
				this.$el.trigger('column-remove', this);
				this.remove();
			}.bind(this));
		},
	});
})(window, Backbone, jQuery, _, oneApp);
