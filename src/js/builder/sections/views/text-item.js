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
				'click .edit-content-link': 'onContentEdit',
				'click .ttfmake-overlay-open': 'openConfigurationOverlay',
				'overlay-open': 'onOverlayOpen',
				'click .ttfmake-text-column-remove': 'onColumnRemove',
				'click .configure-button': 'toggleConfigureDropdown',
				'click .configure-options a': 'onOptionClick',
			});
		},

		initialize: function (options) {
			this.template = _.template(ttfMakeSectionTemplates['text-item'], oneApp.builder.templateSettings);
		},

		render: function () {
			var html = this.template(this.model);
			this.setElement(html);

			return this;
		},

		onOverlayOpen: function (e, $overlay) {
			e.stopPropagation();

			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update column');
		},

		toggleConfigureDropdown: function(evt) {
			var $cogLink;

			if (typeof evt !== 'undefined') {
				evt.preventDefault();
				evt.stopPropagation();
				$cogLink = $(evt.target);
			} else {
				$cogLink = this.$el.find('.configure-button');
			}

			var $configureOptions = this.$el.find('.configure-options');

			if ($configureOptions.is(':visible')) {
				$cogLink.removeClass('active');
			} else {
				$cogLink.addClass('active');
			}

			$configureOptions.toggle();
		},

		onOptionClick: function(evt) {
			this.toggleConfigureDropdown();
		},

		onColumnRemove: function(evt) {
			evt.preventDefault();

			if (!confirm('Are you sure you want to remove this column?')) {
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
