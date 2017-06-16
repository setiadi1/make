<?php
/**
 * @package Make
 */

if ( ! class_exists( 'MAKE_Builder_Sections_Gallery_Definition' ) ) :
/**
 * Section definition for Columns
 *
 * Class MAKE_Builder_Sections_Gallery_Definition
 */
class MAKE_Builder_Sections_Gallery_Definition {
	/**
	 * The one instance of MAKE_Builder_Sections_Gallery_Definition.
	 *
	 * @var   MAKE_Builder_Sections_Gallery_Definition
	 */
	private static $instance;

	/**
	 * Register the text section.
	 *
	 * Note that in 1.4.0, the "text" section was renamed to "columns". In order to provide good back compatibility,
	 * only the section label is changed to "Columns". All other internal references for this section will remain as
	 * "text".
	 *
	 * @return void
	 */
	public static function register() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_filter( 'make_section_choices', array( $this, 'section_choices' ), 10, 3 );
		add_filter( 'make_sections_settings', array( $this, 'section_settings' ) );
		add_filter( 'make_section_defaults', array( $this, 'section_defaults' ) );
		add_filter( 'make_get_section_json', array ( $this, 'get_section_json' ), 10, 1 );
		add_filter( 'make_builder_js_dependencies', array( $this, 'add_js_dependencies' ) );

		ttfmake_add_section(
			'gallery',
			__( 'Gallery', 'make' ),
			Make()->scripts()->get_css_directory_uri() . '/builder/sections/images/gallery.png',
			__( 'Display your images in various grid combinations.', 'make' ),
			array( $this, 'save' ),
			array(
				'gallery' => 'sections/gallery/builder-template',
				'gallery-item' => 'sections/gallery/builder-template-item'
			),
			'sections/gallery/frontend-template',
			400,
			get_template_directory() . '/inc/builder/'
		);
	}

	public function get_settings() {
		return array(
			100 => array(
				'type'    => 'divider',
				'label'   => __( 'General', 'make' ),
				'name'    => 'divider-general',
				'class'   => 'ttfmake-configuration-divider open',
			),
			200 => array(
				'type'  => 'section_title',
				'name'  => 'title',
				'label' => __( 'Enter section title', 'make' ),
				'class' => 'ttfmake-configuration-title ttfmake-section-header-title-input',
				'default' => ttfmake_get_section_default( 'title', 'gallery' )
			),
			300 => array(
				'type'    => 'checkbox',
				'label'   => __( 'Full width', 'make' ),
				'name'    => 'full-width',
				'default' => ttfmake_get_section_default( 'full-width', 'gallery' )
			),
			400 => array(
				'type'    => 'select',
				'name'    => 'columns',
				'label'   => __( 'Columns', 'make' ),
				'class'   => 'ttfmake-gallery-columns',
				'default' => ttfmake_get_section_default( 'columns', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'columns', 'gallery' ),
			),
			500 => array(
				'type'    => 'select',
				'name'    => 'aspect',
				'label'   => __( 'Aspect ratio', 'make' ),
				'default' => ttfmake_get_section_default( 'aspect', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'aspect', 'gallery' ),
			),
			600 => array(
				'type'    => 'select',
				'name'    => 'captions',
				'label'   => __( 'Caption style', 'make' ),
				'default' => ttfmake_get_section_default( 'captions', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'captions', 'gallery' ),
			),
			700 => array(
				'type'    => 'select',
				'name'    => 'caption-color',
				'label'   => __( 'Caption color', 'make' ),
				'default' => ttfmake_get_section_default( 'caption-color', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'caption-color', 'gallery' ),
			),
			800 => array(
				'type'  => 'divider',
				'label' => __( 'Background', 'make' ),
				'name'  => 'divider-background',
				'class' => 'ttfmake-configuration-divider',
			),
			900 => array(
				'type'  => 'image',
				'name'  => 'background-image',
				'label' => __( 'Background image', 'make' ),
				'class' => 'ttfmake-configuration-media',
				'default' => ttfmake_get_section_default( 'background-image', 'gallery' )
			),
			1000 => array(
				'type'  => 'select',
				'name'  => 'background-position',
				'label' => __( 'Position', 'make' ),
				'class' => 'ttfmake-configuration-media-related',
				'default' => ttfmake_get_section_default( 'background-position', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'background-position', 'gallery' ),
			),
			1100 => array(
				'type'    => 'select',
				'name'    => 'background-style',
				'label'   => __( 'Display', 'make' ),
				'class'   => 'ttfmake-configuration-media-related',
				'default' => ttfmake_get_section_default( 'background-style', 'gallery' ),
				'options' => ttfmake_get_section_choices( 'background-style', 'gallery' ),
			),
			1200 => array(
				'type'    => 'checkbox',
				'label'   => __( 'Darken', 'make' ),
				'name'    => 'darken',
				'default' => ttfmake_get_section_default( 'darken', 'gallery' ),
			),
			1300 => array(
				'type'    => 'color',
				'label'   => __( 'Background color', 'make' ),
				'name'    => 'background-color',
				'class'   => 'ttfmake-gallery-background-color ttfmake-configuration-color-picker',
				'default' => ttfmake_get_section_default( 'background-color', 'gallery' )
			),
		);
	}

	public function get_item_settings() {
		/**
		 * Filter the definitions of the Gallery item configuration inputs.
		 *
		 * @since 1.4.0.
		 *
		 * @param array    $inputs    The input definition array.
		 */
		$inputs = apply_filters( 'make_gallery_item_configuration', array(
			100 => array(
				'type'    => 'section_title',
				'name'    => 'title',
				'label'   => __( 'Enter item title', 'make' ),
				'default' => ttfmake_get_section_default( 'title', 'gallery-item' ),
				'class'   => 'ttfmake-configuration-title',
			),
			200 => array(
				'type'    => 'text',
				'name'    => 'link',
				'label'   => __( 'Item link URL', 'make' ),
				'default' => ttfmake_get_section_default( 'link', 'gallery-item' ),
			),
			300 => array(
				'type'    => 'checkbox',
				'name'    => 'open-new-tab',
				'label'   => __( 'Open link in a new tab', 'make' ),
				'default' => ttfmake_get_section_default( 'open-new-tab', 'gallery-item' ),
			),
		) );

		// Sort the config in case 3rd party code added another input
		ksort( $inputs, SORT_NUMERIC );

		return $inputs;
	}

	/**
	 * Define settings for this section
	 *
	 * @since 1.8.10.
	 *
	 * @hooked filter make_sections_settings
	 *
	 * @param array $settings   The existing array of section settings.
	 *
	 * @return array             The modified array of section settings.
	 */
	public function section_settings( $settings ) {
		$settings['gallery'] = $this->get_settings();
		$settings['gallery-item'] = $this->get_item_settings();

		return $settings;
	}

	/**
	 * Add new section choices.
	 *
	 * @since 1.8.8.
	 *
	 * @hooked filter make_section_choices
	 *
	 * @param array  $choices         The existing choices.
	 * @param string $key             The key for the section setting.
	 * @param string $section_type    The section type.
	 *
	 * @return array                  The choices for the particular section_type / key combo.
	 */
	public function section_choices( $choices, $key, $section_type ) {
		if ( count( $choices ) > 1 || ! in_array( $section_type, array( 'gallery' ) ) ) {
			return $choices;
		}

		$choice_id = "$section_type-$key";

		switch ( $choice_id ) {
			case 'gallery-columns':
				$choices = array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				);
				break;

			case 'gallery-aspect' :
				$choices = array(
					'square' => __( 'Square', 'make' ),
					'landscape' => __( 'Landscape', 'make' ),
					'portrait' => __( 'Portrait', 'make' ),
					'none' => __( 'None', 'make' ),
				);
				break;

			case 'gallery-captions':
				$choices = array(
					'reveal' => __( 'Reveal', 'make' ),
					'overlay' => __( 'Overlay', 'make' ),
					'none' => __( 'None', 'make' ),
				);
				break;

			case 'gallery-caption-color' :
				$choices = array(
					'light' => __( 'Light', 'make' ),
					'dark' => __( 'Dark', 'make' ),
				);
				break;

			case 'gallery-background-position' :
				$choices = array(
					'center-top'  => __( 'Top', 'make' ),
					'center-center' => __( 'Center', 'make' ),
					'center-bottom' => __( 'Bottom', 'make' ),
					'left-center'  => __( 'Left', 'make' ),
					'right-center' => __( 'Right', 'make' )
				);
				break;

			case 'gallery-background-style' :
				$choices = array(
					'tile'  => __( 'Tile', 'make' ),
					'cover' => __( 'Cover', 'make' ),
					'contain' => __( 'Contain', 'make' ),
				);
				break;
		}

		return $choices;
	}

	/**
	 * Get default values for gallery section
	 *
	 * @since 1.8
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'title' => '',
			'columns' => 3,
			'aspect' => 'square',
			'captions' => 'reveal',
			'caption-color' => 'light',
			'background-image' => '',
			'background-position' => 'center-center',
			'darken' => 0,
			'background-style' => 'cover',
			'background-color' => '',
			'full-width' => 0
		);
	}

	/**
	 * Get default values for gallery item
	 *
	 * @since 1.8
	 *
	 * @return array
	 */
	public function get_item_defaults() {
		return array(
			'title' => '',
			'link' => '',
			'description' => '',
			'background-image' => '',
			'open-new-tab' => 0
		);
	}

	/**
	 * Extract the setting defaults and add them to Make's section defaults system.
	 *
	 * @since 1.6.0.
	 *
	 * @hooked filter make_section_defaults
	 *
	 * @param array $defaults    The existing array of section defaults.
	 *
	 * @return array             The modified array of section defaults.
	 */
	public function section_defaults( $defaults ) {
		$defaults['gallery'] = $this->get_defaults();
		$defaults['gallery-item'] = $this->get_item_defaults();

		return $defaults;
	}

	/**
	 * Filter the json representation of this section.
	 *
	 * @since 1.8.0.
	 *
	 * @hooked filter make_get_section_json
	 *
	 * @param array $defaults    The array of data for this section.
	 *
	 * @return array             The modified array to be jsonified.
	 */
	public function get_section_json( $data ) {
		if ( $data['section-type'] == 'gallery' ) {
			$data = wp_parse_args( $data, $this->get_defaults() );
			$image = ttfmake_get_image_src( $data['background-image'], 'large' );

			if ( isset( $image[0] ) ) {
				$data['background-image-url'] = $image[0];
			}

			if ( isset( $data['gallery-items'] ) && is_array( $data['gallery-items'] ) ) {
				foreach ( $data['gallery-items'] as $s => $item ) {
					$item = wp_parse_args( $item, $this->get_item_defaults() );

					// Handle legacy data layout
					$id = isset( $item['id'] ) ? $item['id']: $s;
					$data['gallery-items'][$s]['id'] = $id;

					if ( isset( $data['gallery-items'][$s]['image-id'] ) && '' !== $data['gallery-items'][$s]['image-id'] ) {
						$data['gallery-items'][$s]['background-image'] = $data['gallery-items'][$s]['image-id'];
					}

					$item_image = ttfmake_get_image_src( $data['gallery-items'][$s]['background-image'], 'large' );

					if( isset( $item_image[0] ) ) {
						$data['gallery-items'][$s]['background-image-url'] = $item_image[0];
					}
				}

				if ( isset( $data['gallery-item-order'] ) ) {
					$ordered_items = array();

					foreach ( $data['gallery-item-order'] as $item_id ) {
						array_push( $ordered_items, $data['gallery-items'][$item_id] );
					}

					$data['gallery-items'] = $ordered_items;
					unset( $data['gallery-item-order'] );
				}
			}
		}

		return $data;
	}

	/**
	 * Save the data for the section.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save( $data ) {
		$data = wp_parse_args( $data, $this->get_defaults() );
		$clean_data = array();

		if ( isset( $data['columns'] ) ) {
			$clean_data['columns'] = ttfmake_sanitize_section_choice( $data['columns'], 'columns', $data['section-type'] );
		}

		if ( isset( $data['caption-color'] ) ) {
			$clean_data['caption-color'] = ttfmake_sanitize_section_choice( $data['caption-color'], 'caption-color', $data['section-type'] );
		}

		if ( isset( $data['captions'] ) ) {
			$clean_data['captions'] = ttfmake_sanitize_section_choice( $data['captions'], 'captions', $data['section-type'] );
		}

		if ( isset( $data['aspect'] ) ) {
			$clean_data['aspect'] = ttfmake_sanitize_section_choice( $data['aspect'], 'aspect', $data['section-type'] );
		}

		if ( isset( $data['background-image'] ) ) {
			$clean_data['background-image'] = ttfmake_sanitize_image_id( $data['background-image'] );
		}

		if ( isset( $data['title'] ) ) {
			$clean_data['title'] = $clean_data['label'] = apply_filters( 'title_save_pre', $data['title'] );
		}

		if ( isset( $data['darken'] ) && (int) $data['darken'] == 1 ) {
			$clean_data['darken'] = 1;
		} else {
			$clean_data['darken'] = 0;
		}

		if ( isset( $data['background-color'] ) ) {
			$clean_data['background-color'] = maybe_hash_hex_color( $data['background-color'] );
		}

		if ( isset( $data['background-style'] ) ) {
			$clean_data['background-style'] = ttfmake_sanitize_section_choice( $data['background-style'], 'background-style', $data['section-type'] );
		}

		if ( isset( $data['background-position'] ) ) {
			$clean_data['background-position'] = ttfmake_sanitize_section_choice( $data['background-position'], 'background-position', $data['section-type'] );
		}

		if ( isset( $data['full-width'] ) && $data['full-width'] == 1 ) {
			$clean_data['full-width'] = 1;
		} else {
			$clean_data['full-width'] = 0;
		}

		if ( isset( $data['gallery-items'] ) && is_array( $data['gallery-items'] ) ) {
			$clean_data['gallery-items'] = array();

			foreach ( $data['gallery-items'] as $i => $item ) {
				$item = wp_parse_args( $item, $this->get_item_defaults() );

				// Handle legacy data layout
				$id = isset( $item['id'] ) ? $item['id']: $i;

				$clean_item_data = array( 'id' => $id );

				if ( isset( $item['title'] ) ) {
					$clean_item_data['title'] = apply_filters( 'title_save_pre', $item['title'] );
				}

				if ( isset( $item['link'] ) ) {
					$clean_item_data['link'] = esc_url_raw( $item['link'] );
				}

				if ( isset( $item['description'] ) ) {
					$clean_item_data['description'] = sanitize_post_field( 'post_content', $item['description'], ( get_post() ) ? get_the_ID() : 0, 'db' );
				}

				if ( isset( $item['background-image'] ) ) {
					$clean_item_data['background-image'] = ttfmake_sanitize_image_id( $item['background-image'] );
				}

				if ( isset( $item['open-new-tab'] ) && $item['open-new-tab'] == 1 ) {
					$clean_item_data['open-new-tab'] = 1;
				} else {
					$clean_item_data['open-new-tab'] = 0;
				}

				array_push( $clean_data['gallery-items'], $clean_item_data );
			}
		}

		return $clean_data;
	}

	/**
	 * Add JS dependencies for the section
	 *
	 * @return array
	 */
	public function add_js_dependencies( $deps ) {
		if ( ! is_array( $deps ) ) {
			$deps = array();
		}

		wp_register_script(
			'builder-models-gallery',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/models/gallery.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-models-gallery-item',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/models/gallery-item.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-views-gallery-item',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/views/gallery-item.js',
			array( 'builder-views-item' ),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-views-gallery',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/views/gallery.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		return array_merge( $deps, array(
			'builder-models-gallery',
			'builder-models-gallery-item',
			'builder-views-gallery-item',
			'builder-views-gallery'
		) );
	}
}
endif;
