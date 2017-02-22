/* global Backbone, jQuery, _ */
var oneApp = oneApp || {};

(function (window, Backbone, $, _, oneApp) {
		'use strict';

		oneApp.views = oneApp.views || {}

		oneApp.views.item = Backbone.View.extend({
			events: {
				'view-ready': 'onViewReady',
				'click .ttfmake-media-uploader-add': 'onMediaAdd',
				'mediaSelected': 'onMediaSelected',
				'mediaRemoved': 'onMediaRemoved',
				'click .edit-content-link': 'onContentEdit',
				'click .ttfmake-overlay-open': 'openConfigurationOverlay',
				'overlay-close': 'onOverlayClose',
				'click .configure-button': 'toggleConfigureDropdown',
				'click .configure-options a': 'onOptionClick'
			},

			onViewReady: function(e) {
				// Trap this event to avoid stack overflow
				e.stopPropagation();
			},

			openConfigurationOverlay: function (e) {
				e.preventDefault();
				e.stopPropagation();

				var $target = $(e.target);
				var $overlay = $($target.attr('data-overlay'));
				oneApp.builder.settingsOverlay.open(this, $overlay);
			},

			onOverlayClose: function(e, changeset) {
				e.stopPropagation();

				this.model.set(changeset);

				if (this.model.hasChanged()) {
					this.$el.trigger('model-item-change');
				}
			},

			onMediaAdd: function(e) {
				e.preventDefault();
				e.stopPropagation();
				oneApp.builder.initUploader(this, e.target);
			},

			onMediaSelected: function(e, attachment) {
				e.stopPropagation();
				this.model.set('image-id', attachment.id);
				this.model.set('image-url', attachment.url);
				this.$el.trigger('model-item-change');
			},

			onMediaRemoved: function(e) {
				e.stopPropagation();
				this.model.unset('image-id');
				this.model.unset('image-url');
				this.$el.trigger('model-item-change');
			},

			onContentEdit: function(e) {
				e.preventDefault();

				var $target = $(e.target);
				var iframeID = ($target.attr('data-iframe')) ? $target.attr('data-iframe') : '';
				var textAreaID = $target.attr('data-textarea');
				var $overlay = oneApp.builder.tinymceOverlay.$el;

				oneApp.builder.setMakeContentFromTextArea(iframeID, textAreaID);
				oneApp.builder.tinymceOverlay.open(this);
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
			}
		});
})(window, Backbone, jQuery, _, oneApp);