<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Enhancements_Builder
 *
 * Various Builder enhancements exclusive to Make theme
 *
 * @since 1.7.0.
 */
final class MAKE_Enhancements_Builder extends MAKE_Util_Modules implements  MAKE_Enhancements_BuilderInterface, MAKE_Util_HookInterface {

	/**
	 * Indicator of whether the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Hook into WordPress.
	 *
	 * @since 1.7.0.
	 *
	 * @return void
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}

		add_filter( 'make_builder_settings', array( $this, 'builder_settings' ), 100 );

		add_filter( 'make_section_settings', array( $this, 'full_width_settings' ), 20, 2 );
		// Add entry to section defaults
		add_filter( 'make_sections_defaults', array( $this, 'full_width_defaults' ), 20 );
		// Hook up save routine
		add_filter( 'make_prepare_data_section', array( $this, 'full_width_save_data' ), 20, 2 );

		// Hooking has occurred.
		self::$hooked = true;
	}

	/**
	 * Check if the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @return bool
	 */
	public function is_hooked() {
		return self::$hooked;
	}

	/**
	 * TODO
	 */
	public function builder_settings( $settings ) {
		$settings['updateColumnsLabel'] = __( 'Update content settings', 'make' );

		return $settings;
	}

	/**
	 * Add a Full Width setting checkbox
	 *
	 * @since 1.6.0.
	 *
	 * @hooked filter make_section_settings
	 *
	 * @param array $args    The section args.
	 *
	 * @return array         The modified section args.
	 */
	public function full_width_settings( $settings, $section_type ) {
		if ( ! in_array( $section_type, array(
			'text', 'banner', 'gallery', 'panels', 'postlist', 'productgrid', 'downloads'
			) ) ) {
			return $settings;
		}

		$index = array_search( 'divider-background', wp_list_pluck( $settings, 'name' ) );

		$settings[$index - 25] = array(
			'type'    => 'checkbox',
			'label'   => __( 'Full width', 'make' ),
			'name'    => 'full-width',
			'default' => ttfmake_get_section_default( 'full-width', 'text' ),
		);

		return $settings;
	}

	/**
	 * TODO
	 */
	public function full_width_defaults( $defaults ) {
		foreach ( $defaults as $section_id => $section_defaults ) {
			if ( ! in_array( $section_defaults['section-type'], array(
				'text', 'banner', 'gallery', 'panels', 'postlist', 'productgrid', 'downloads'
				) ) ) {
				continue;
			}

			$defaults[$section_id]['full-width'] = 0;
		}

		return $defaults;
	}

	/**
	 * TODO
	 */
	public function full_width_save_data( $clean_data, $original_data ) {
		if ( isset( $original_data['full-width'] ) && $original_data['full-width'] == 1 ) {
			$clean_data['full-width'] = 1;
		} else {
			$clean_data['full-width'] = 0;
		}

		return $clean_data;
	}
}