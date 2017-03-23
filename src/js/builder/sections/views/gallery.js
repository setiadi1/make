/* global jQuery, _ */
var oneApp = oneApp || {};

(function (window, $, _, oneApp) {
	'use strict';

	oneApp.views = oneApp.views || {}

	oneApp.views.gallery = oneApp.views.section.extend({
		itemViews: [],

		events: function() {
			return _.extend({}, oneApp.views.section.prototype.events, {
				'view-ready': 'onViewReady',
				'click .ttfmake-gallery-add-item-link' : 'onItemAdd',
				'model-item-change': 'onItemChange',
				'item-sort': 'onItemSort',
				'item-remove': 'onItemRemove',
				'overlay-open': 'onOverlayOpen',
				'overlay-close': 'onOverlayClose',
			});
		},

		render: function () {
			oneApp.views.section.prototype.render.apply(this, arguments);

			var items = this.model.get('gallery-items'),
					self = this;

			if (items.length == 0) {
				var $addButton = $('.ttfmake-gallery-add-item-link', this.$el);
				$addButton.trigger('click', true);
				$addButton.trigger('click', true);
				$addButton.trigger('click', true);
				return this;
			}

			_(items).each(function (itemModel) {
				var itemView = self.addItem(itemModel);
			});

			return this;
		},

		onViewReady: function(e) {
			e.stopPropagation();
			this.initializeSortables();
		},

		addItem: function(itemModel) {
			// Create view
			var itemView = new oneApp.views['gallery-item']({
				model: itemModel
			});

			var html = itemView.render().el;
			$('.ttfmake-gallery-items-stage', this.$el).append(html);

			// Store view
			this.itemViews.push(itemView);

			return itemView;
		},

		onItemAdd : function (e, pseudo) {
			e.preventDefault();

			var itemModelDefaults = ttfMakeSectionDefaults['gallery-item'] || {};
			var itemModelAttributes = _(itemModelDefaults).extend({
				id: new Date().getTime().toString(),
				parentID: this.model.id
			});
			var itemModel = new oneApp.models['gallery-item'](itemModelAttributes);
			var itemView = this.addItem(itemModel);
			itemView.$el.trigger('view-ready');

			var items = this.model.get('gallery-items');
			items.push(itemModel);
			this.model.set('gallery-items', items);
			this.model.trigger('change');

			if (!pseudo) {
				oneApp.builder.scrollToSectionView(itemView);
			}
		},

		onItemSort: function(e, ids) {
			e.stopPropagation();

			var items = _(this.model.get('gallery-items'));
			var sortedItems = _(ids).map(function(id) {
				return items.find(function(item) {
					return item.id.toString() == id.toString()
				});
			});

			this.model.set('gallery-items', sortedItems);
		},

		onItemChange: function() {
			this.model.trigger('change');
		},

		onItemRemove: function(e, itemView) {
			var items = this.model.get('gallery-items');
			this.model.set('gallery-items', _(items).without(itemView.model));
		},

		initializeSortables: function() {
			var $selector = $('.ttfmake-gallery-items-stage', this.$el);
			var self = this;

			$selector.sortable({
				handle: '.ttfmake-sortable-handle',
				placeholder: 'sortable-placeholder',
				distance: 2,
				tolerance: 'pointer',
				start: function (event, ui) {
					// Set the height of the placeholder to that of the sorted item
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-gallery-items-stage');

					$('.sortable-placeholder', $stage)
						.height(parseInt($item.height(), 10) - 2); // -2 to account for placeholder border
				},
				stop: function (event, ui) {
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-gallery-items'),
						$orderInput = $('.ttfmake-gallery-item-order', $stage);

					var ids = $(this).sortable('toArray', {attribute: 'data-id'});
					self.$el.trigger('item-sort', [ids]);
				}
			});
		},

		handleColumns: function() {
			var columns = this.model.get('columns');
			var $stage = $('.ttfmake-gallery-items-stage', this.$el);

			$stage.removeClass('ttfmake-gallery-columns-1 ttfmake-gallery-columns-2 ttfmake-gallery-columns-3 ttfmake-gallery-columns-4');
			$stage.addClass('ttfmake-gallery-columns-' + parseInt(columns, 10));
		},

		onOverlayOpen: function(e, $overlay) {
			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update gallery settings');
		},

		onOverlayClose: function(e, changeset) {
			oneApp.views.section.prototype.onOverlayClose.apply(this, arguments);
			this.model.set(changeset);

			if ('columns' in changeset) {
				this.handleColumns();
			}
		}
	});
})(window, jQuery, _, oneApp);
