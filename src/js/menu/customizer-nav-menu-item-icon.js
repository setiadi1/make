var NavMenuItemIconField = (function( $, api, sources ) {
	'use strict';

	var component = {
		api: null,
		hasSaved: false,
		sources: sources,
	};

	$.widget( 'custom.iconselectmenu', $.ui.selectmenu, {
		_renderItem: function( ul, item ) {
			var li = $( '<li>', {
				text: item.label
			} );

			if ( item.disabled ) {
				li.addClass( 'ui-state-disabled' );
			}

			var icon = $( '<i>', {
				class: 'fa ' + item.value
			} );

			icon.prependTo( li );
			return li.appendTo( ul );
		}
	} );

	component.init = function init( api ) {
		component.api = api;

		_.extend( component, api.Events );

		api.control.each( component.setup );
		api.control.bind( 'add', component.setup );
	};

	component.setup = function setup( control ) {
		var onceExpanded;

		if ( ! control.extended( component.api.Menus.MenuItemControl ) ) {
			return;
		}

		onceExpanded = function onceExpandedFn( expanded ) {
			if ( expanded ) {
				control.expanded.unbind( onceExpanded );
				component.render( control );
			}
		};

		control.deferred.embedded.done( function() {
			if ( component.hasSaved || control.expanded.get() ) {
				component.render( control );
			} else {
				control.expanded.bind( onceExpanded );
			}
		} );
	};

	component.render = function render( control ) {
		window.control = control;

		var $container = $( control.container );
		var $classesField = $('.field-css-classes', $container);

		$classesField.before( wp.template('make-menu-item-icons')( {icons: this.sources.fontawesome} ) );
		var $select = $( 'select', $container );
		$select.iconselectmenu().iconselectmenu( 'menuWidget' );
	}

	component.init( api );

})( jQuery, wp.customize, sources );