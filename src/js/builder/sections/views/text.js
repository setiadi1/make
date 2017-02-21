/* global jQuery, _ */
var oneApp = oneApp || {};

(function (window, $, _, oneApp) {
	'use strict';

	oneApp.views = oneApp.views || {}

	oneApp.views.text = oneApp.views.section.extend({
		itemViews: [],

		events: function() {
			return _.extend({}, oneApp.views.section.prototype.events, {
				'columns-number-change' : 'onColumnsNumberChange',
				'mouseup .ttfmake-text-column' : 'updateJSONOnSlide',
				'model-item-change': 'onTextItemChange',
				'columns-sort': 'onColumnsSort',
				'view-ready': 'onViewReady',
				'overlay-open': 'onOverlayOpen',
				'overlay-close': 'onOverlayClose',
				'click .ttfmake-text-columns-add-row': 'addRow',
				'column-remove': 'onColumnRemove',
				'click .ttfmake-text-columns-add-column-link': 'handleColumnAddLink'
			});
		},

		render: function() {
			oneApp.views.section.prototype.render.apply(this, arguments);

			var columns = this.model.get('columns');
			var self = this;

			if (typeof columns === 'undefined' || !columns.length) {
				this.addColumns(3);
			} else {
				_(columns).each(function(columnModel) {
					var columnView = self.addColumn(columnModel);
				});
			}

			this.$el.trigger('columns-ready');

			return this;
		},

		handleColumnAddLink: function(e) {
			e.preventDefault();
			e.stopPropagation();

			this.addColumns(1);

			this.$el.trigger('columns-ready');
			this.model.trigger('change');
		},

		onColumnRemove: function(e, columnView) {
			var columns = this.model.get('columns');
			this.model.set('columns', _(columns).without(columnView.model));

			this.$el.trigger('column-removed');
		},

		addColumn: function(columnModel) {
			var columnView = new oneApp.views['text-item']({
				model: columnModel
			});

			var html = columnView.render().el;

			if (!$('.ttfmp-column-size-container').length) {
				$('.ttfmake-text-columns-stage', this.$el).append(html);
			} else {
				$('.ttfmp-column-size-container', this.$el).before(html);
			}

			this.itemViews.push(columnView);

			var columns = parseInt($('.ttfmake-text-column', this.$el).length, 10);
			columnView.$el.addClass('ttfmake-text-column-position-'+columns);

			this.$el.trigger('column-added');

			return columnView;
		},

		addColumns: function(number) {
			var self = this;

			if (typeof number === 'undefined') {
				number = 1;
			}

			for (var i = 1; i <= number; i++) {
				var columnModelDefaults = ttfMakeSectionDefaults['text-item'] || {};
				var columnModelAttributes = _(columnModelDefaults).extend({
					id: new Date().getTime().toString(),
					parentID: this.model.id
				});

				var columnModel = new oneApp.models['text-item'](columnModelAttributes);
				var columnView = this.addColumn(columnModel);

				columnView.$el.trigger('view-ready');

				var columns = this.model.get('columns');
				columns.push(columnModel);

				this.model.set('columns', columns);
				this.model.trigger('change');
			}

			setTimeout(function() {
				self.initFrames();
			}, 100);
		},

		handleColumnsClasses: function() {
			var self = this;

			var columnsNumber = this.model.get('columns-number');
			var nthChild = parseInt(columnsNumber, 10) + 1;

			$(document).ready(function() {
				self.$el.find('.ttfmake-text-column').removeClass('ttfmake-text-column-row-start');

				self.$el.find('.ttfmake-text-column:nth-child('+columnsNumber+'n+'+nthChild+')').addClass('ttfmake-text-column-row-start');
			});
		},

		onViewReady: function(e) {
			e.stopPropagation();

			this.initializeColumnsSortables();
			this.initFrames();
		},

		initFrames: function(e) {
			var link = oneApp.builder.getFrameHeadLinks();

			$('iframe', this.$el).each(function() {
				var $this = $(this);

				var id = $this.attr('id').replace('ttfmake-iframe-', '');

				oneApp.builder.initFrame(id, link);
			});
		},

		onColumnsSort: function(e, ids) {
			e.stopPropagation();

			var columns = _(this.model.get('columns'));
			var sortedColumns = _(ids).map(function(id) {
				return columns.find(function(column) {
					return column.id.toString() === id.toString();
				});
			});

			this.model.set('columns', sortedColumns);
			this.$el.trigger('columns-sorted');
		},

		onColumnsNumberChange: function() {
			var columns = this.model.get('columns-number'),
					$stage = $('.ttfmake-text-columns-stage', this.$el);

			var numberOfColumnsToCreate = columns - this.model.get('columns').length;

			if (numberOfColumnsToCreate !== 0) {
				if (numberOfColumnsToCreate > 0) {
					this.addColumns(numberOfColumnsToCreate);
				}
			}

			$stage.removeClass(function(i, className) {
				return className.match(/ttfmake-text-columns-[0-9]/g || []).join(' ');
			});

			$stage.addClass('ttfmake-text-columns-' + columns);

			this.$el.trigger('columns-layout-updated');
		},

		onTextItemChange: function(evt) {
			this.model.trigger('change');
		},

		initializeColumnsSortables: function() {
			var $sortableSelector = $('.ttfmake-text-columns-stage', this.$el);
			var self = this;

			$sortableSelector.sortable({
				handle: '.ttfmake-sortable-handle',
				placeholder: {
					element: function(currentItem) {
						return $('<span class="sortable-placeholder"></span>')[0];
					},
					update: function(container, p) {
            return;
					}
				},
				items: '.ttfmake-text-column',
				forcePlaceholderSizeType: true,
				distance: 2,
				zIndex: 99999,
				tolerance: 'pointer',
				create: function() {
					self.$el.trigger('columns-sortable-init');
				},
				start: function(event, ui) {
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-text-columns-stage');

					/**
					 * Make Plus feature from here
					 */
					var addClass;

					$stage.removeClass('current-item-one-half current-item-two-thirds current-item-one-third current-item-one-fourth current-item-three-fourths');

					// If text item, potentially add class to stage
					if ($item.hasClass('ttfmake-text-column')) {
						if ($item.hasClass('ttfmake-column-width-two-thirds')) {
							addClass = 'current-item-two-thirds';
						} else if ($item.hasClass('ttfmake-column-width-one-third')) {
							addClass = 'current-item-one-third';
						} else if ($item.hasClass('ttfmake-column-width-one-fourth')) {
							addClass = 'current-item-one-fourth';
						} else if ($item.hasClass('ttfmake-column-width-three-fourths')) {
							addClass = 'current-item-three-fourths';
						} else if ($item.hasClass('ttfmake-column-width-one-half')) {
							addClass = 'current-item-one-half';
						}

						$stage.addClass(addClass);

						ui.placeholder.css('flex', $item.css('flex'));
         		ui.placeholder.css('margin-right', $item.css('margin-right'));
         		ui.placeholder.css('margin-left', $item.css('margin-left'));
					}
				},
				stop: function(event, ui) {
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.ttfmake-text-columns-stage');

					var i = 1;

					$('.ttfmake-text-column', $stage).each(function(index) {
						var columnIndex = parseInt(index, 10) + 1;
						$(this).removeClass('ttfmake-text-column-position-1 ttfmake-text-column-position-2 ttfmake-text-column-position-3 ttfmake-text-column-position-4 ttfmake-text-column-position-5 ttfmake-text-column-position-6')
							.addClass('ttfmake-text-column-position-' + columnIndex);
						i++;
					});

					var ids = $(this).sortable('toArray', {attribute: 'data-id'});
					self.$el.trigger('columns-sort', [ids]);
					oneApp.builder.initFrame(self.model.get('id') + '-' + $item.attr('data-id'));
				}
			});
		},

		onOverlayOpen: function(e, $overlay) {
			var $button = $('.ttfmake-overlay-close-update', $overlay);
			$button.text('Update columns settings');
		},

		onOverlayClose: function(e, changeset) {
			oneApp.views.section.prototype.onOverlayClose.apply(this, arguments);
			this.model.set(changeset);

			if ('columns-number' in changeset) {
				this.$el.trigger('columns-number-change');
			}
		}
	});
})(window, jQuery, _, oneApp);
