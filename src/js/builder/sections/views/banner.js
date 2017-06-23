/* global jQuery, _ */
var oneApp = oneApp || {};

(function (window, $, _, oneApp) {
	'use strict';

	oneApp.views = oneApp.views || {}

	oneApp.views.banner = oneApp.views.section.extend({
		itemViews: [],

		events: function() {
			return _.extend({}, oneApp.views.section.prototype.events, {
				'click .ttfmake-add-slide' : 'onSlideAdd',
				'model-item-change': 'onSlideChange',
				'view-ready': 'onViewReady',
				'item-sort': 'onSlideSort',
				'slide-remove': 'onSlideRemove',
				'overlay-open': 'onOverlayOpen',
			});
		},

		render: function () {
			oneApp.views.section.prototype.render.apply(this, arguments);

			var slides = this.model.get('banner-slides'),
					self = this;

			if (slides.length == 0) {
				$('.ttfmake-add-slide', this.$el).trigger('click', true);
				return this;
			}

			_(slides).each(function (slideModel) {
				var slideView = self.addSlide(slideModel);
			});

			return this;
		},

		onViewReady: function(e) {
			this.initializeSortables();

			_(this.itemViews).each(function(slideView) {
				slideView.$el.trigger('view-ready');
			});
		},

		onSlideChange: function() {
			this.model.trigger('change');
		},

		onSlideSort: function(e, ids) {
			e.stopPropagation();

			var slides = _(this.model.get('banner-slides'));
			var sortedSlides = _(ids).map(function(id) {
				return slides.find(function(slide) {
					return slide.id.toString() == id.toString()
				});
			});

			this.model.set('banner-slides', sortedSlides);
		},

		onSlideRemove: function(e, slideView) {
			var slides = this.model.get('banner-slides');
			this.model.set('banner-slides', _(slides).without(slideView.model));
		},

		addSlide: function(slideModel) {
			// Build the view
			var slideView = new oneApp.views['banner-slide']({
				model: slideModel
			});

			// Append view
			var html = slideView.render().el;
			$('.ttfmake-banner-slides-stage', this.$el).append(html);

			// Store view
			this.itemViews.push(slideView);

			return slideView;
		},

		onSlideAdd: function (e, pseudo) {
			e.preventDefault();

			var slideModelDefaults = ttfMakeSectionDefaults['banner-slide'] || {};
			var slideModelAttributes = _(slideModelDefaults).extend({
				id: new Date().getTime().toString()
			});
			var slideModel = new oneApp.models['banner-slide'](slideModelAttributes);
			slideModel.parent = this.model;
			var slideView = this.addSlide(slideModel);
			slideView.$el.trigger('view-ready');

			var slides = this.model.get('banner-slides');
			slides.push(slideModel);
			this.model.set('banner-slides', slides);
			this.model.trigger('change');

			if (!pseudo) {
				oneApp.builder.scrollToSectionView(slideView);
			}
		},

		getParentID: function() {
			var idAttr = this.$el.attr('id'),
				id = idAttr.replace('ttfmake-section-', '');

			return parseInt(id, 10);
		},

		initializeSortables: function() {
			var $selector = $('.ttfmake-banner-slides-stage', this.$el);
			var self = this;

			$selector.sortable({
				handle: '.ttfmake-sortable-handle',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSizeType: true,
				distance: 2,
				tolerance: 'pointer',
				start: function (event, ui) {
					// Set the height of the placeholder to that of the sorted item
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-banner-slides-stage');

					$('.sortable-placeholder', $stage).height($item.height());
				},
				stop: function (event, ui) {
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-banner-slides'),
						$orderInput = $('.ttfmake-banner-slide-order', $stage);

					var ids = $(this).sortable('toArray', {attribute: 'data-id'});
					self.$el.trigger('item-sort', [ids]);
				}
			});
		},

		onOverlayOpen: function(e, $overlay) {
			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update banner settings');
		},
	});
})(window, jQuery, _, oneApp);
