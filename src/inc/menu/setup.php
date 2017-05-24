<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Menu_Setup
 *
 * Set up the contents of the document head.
 *
 * @since 1.8.9.
 */
final class MAKE_Menu_Setup extends MAKE_Util_Modules implements MAKE_Menu_SetupInterface, MAKE_Util_HookInterface {
	/**
	 * An associative array of required modules.
	 *
	 * @since 1.8.9.
	 *
	 * @var array
	 */
	protected $dependencies = array(
		'scripts' => 'MAKE_Setup_ScriptsInterface',
	);

	/**
	 * Indicator of whether the hook routine has been run.
	 *
	 * @since 1.8.9.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Hook into WordPress.
	 *
	 * @since 1.8.9.
	 *
	 * @return void
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		// Register the icon field on menu items
		// register_meta( 'nav_menu_item', 'icon', array(
		// 	// 'sanitize_callback' => 'sanitize_my_meta_key',
		// 	// 'auth_callback' => 'authorize_my_meta_key',
		// 	'single' => true,
		// 	'show_in_rest' => true,
		// ) );

		// add_action( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		// add_action( 'admin_head-nav-menus.php', array( $this, 'admin_head' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'print_templates' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );

		// Hooking has occurred.
		self::$hooked = true;
	}

	public function edit_nav_menu_walker( $walker ) {
		// Load the custom walker class
		$file = dirname( __FILE__ ) . '/walker-nav-menu-edit-icons.php';
		if ( is_readable( $file ) ) {
			include_once $file;
		}

		if ( 'Walker_Nav_Menu_Edit' === $walker ) {
			$walker = 'Make_Walker_Nav_Menu_Edit';
		}

		return $walker;
	}

	public function admin_scripts( $view ) {
		if ( 'nav-menus.php' != $view ) {
			return;
		}

		// Overlay
		wp_register_script(
			'ttfmake-overlay',
			Make()->scripts()->get_js_directory_uri() . '/builder/core/views/overlay.js',
			array( 'backbone' ),
			TTFMAKE_VERSION,
			true
		);

		// Icon Picker
		wp_register_script(
			'make-icon-picker',
			$this->scripts()->get_js_directory_uri() . '/formatting/icon-picker/icon-picker.js',
			array( 'jquery',  ),
			TTFMAKE_VERSION
		);

		wp_localize_script(
			'make-icon-picker',
			'MakeIconPicker',
			array(
				'sources' => array(
					'fontawesome' => $this->scripts()->get_js_directory_uri() . '/formatting/icon-picker/fontawesome.json'
				)
			)
		);

		wp_enqueue_script(
			'make-menu-item-icons',
			Make()->scripts()->get_js_directory_uri() . '/menu/item-icon.js',
			array( 'ttfmake-overlay', 'make-icon-picker' ),
			TTFMAKE_VERSION,
			true
		);
	}

	public function admin_head() {
		?>
		<script type="text/html" id="tmpl-make-menu-item-icons">
			<p class="description description-wide">
				<label><?php echo __( 'Item Icon', 'make' ); ?></label>
				<input type="text" class="widefat ttfmake-menu-item-icon" />
			</p>
		</script>
		<?php
	}

	public function customize_controls_enqueue_scripts() {
		// Short-circuit if nav menus component is disabled.
		global $wp_customize;
		if ( ! isset( $wp_customize->nav_menus ) ) {
			return;
		}

		wp_enqueue_script(
			'make-customizer-nav-menu-item-icon',
			Make()->scripts()->get_js_directory_uri() . '/menu/customizer-nav-menu-item-icon.js',
			array( 'customize-controls', 'customize-nav-menus', 'jquery-ui-selectmenu' ),
			TTFMAKE_VERSION,
			true
		);

		$fontawesome_json = $this->scripts()->get_js_directory_uri() . '/formatting/icon-picker/fontawesome.json';
		$fontawesome = json_decode( file_get_contents( $fontawesome_json ) );

		wp_localize_script(
			'make-customizer-nav-menu-item-icon',
			'sources',
			array(
				'fontawesome' => $fontawesome
			)
		);
	}

	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-make-menu-item-icons">
			<p class="description description-thin">
				<label><?php echo __( 'Item Icon', 'make' ); ?></label>
				<select>
				<# for (var h in data.icons ) { #>
					<option value=""><?php echo __( 'None', 'make') ?></option>
					<optgroup label="{{ h }}">
					<# for (var i = 0; i < data.icons[h].length; i ++ ) { #>
						<option value="{{ data.icons[h][i].id }}">{{ data.icons[h][i].name }}</option>
					<# } #>
					</optgroup>
				<# } #>
				</select>
			</p>
		</script>
		<?php
	}

	/**
	 * Check if the hook routine has been run.
	 *
	 * @since 1.8.9.
	 *
	 * @return bool
	 */
	public function is_hooked() {
		return self::$hooked;
	}
}
