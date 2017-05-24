<?php
/**
 * @package Make
 */

// Bail if this isn't being included inside of a MAKE_Menu_SetupInterface.
if ( ! isset( $this ) || ! $this instanceof MAKE_Menu_SetupInterface ) {
	return;
}

if ( !class_exists( 'Make_Walker_Nav_Menu_Edit' ) && class_exists( 'Walker_Nav_Menu_Edit' ) ):

class Make_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker_Nav_Menu::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker_Nav_Menu::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Start the element output.
     *
     * @see Walker_Nav_Menu::start_el()
     * @since 3.0.0
     *
     * @global int $_wp_nav_menu_max_depth
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     * @param int    $id     Not used.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    	parent::start_el( $output, $item, $depth, $args, $id );
        ob_start();
        $item_id = esc_attr( $item->ID );
        $item_container = "menu-item-$item_id";
        ?>

		<script type="text/javascript">
		(function($, itemId) {
			console.log('yi');
			// Fetch the item "move" setting block
			var itemMoveSetting = document.querySelector('#' + itemId + ' .field-move');
			// Fetch custom field template and create actual field node
			var template = document.getElementById('tmpl-make-menu-item-icons');
			var field = document.createElement('div');
			field.innerHTML = template.innerHTML;
			// Prepend the custom field
			itemMoveSetting.parentNode.insertBefore(field, itemMoveSetting);
		})(jQuery, '<?php echo $item_container; ?>');
		</script>

        <?php
        $output .= ob_get_clean();
    }
}

endif;