<?php
/**
 * @package Make
 */

if ( ! class_exists( 'MAKE_Builder_Sections_Columns_Definition' ) ) :
/**
 * Section definition for Columns
 *
 * Class MAKE_Builder_Sections_Columns_Definition
 */
class MAKE_Builder_Sections_Columns_Definition {
	/**
	 * The one instance of MAKE_Builder_Sections_Columns_Definition.
	 *
	 * @var   MAKE_Builder_Sections_Columns_Definition
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
		ttfmake_add_section(
			'text',
			__( 'Columns', 'make' ),
			Make()->scripts()->get_css_directory_uri() . '/builder/sections/images/text.png',
			__( 'Create rearrangeable columns of content and images.', 'make' ),
			array( $this, 'save' ),
			array (
				'text' => 'sections/columns/builder-template',
				'text-item' => 'sections/columns/builder-template-column'
			),
			'sections/columns/frontend-template',
			100,
			get_template_directory() . '/inc/builder/',
			$this->get_settings()
		);

		add_filter( 'make_section_defaults', array( $this, 'section_defaults' ) );
		add_filter( 'make_get_section_json', array ( $this, 'get_section_json' ), 10, 1 );
		add_filter( 'make_builder_js_dependencies', array( $this, 'add_js_dependencies' ) );
	}

	public function get_settings() {
		return array(
			100 => array(
				'type'  => 'section_title',
				'name'  => 'title',
				'label' => __( 'Enter section title', 'make' ),
				'class' => 'ttfmake-configuration-title ttfmake-section-header-title-input',
				'default' => ttfmake_get_section_default( 'title', 'text' ),
			),
			200 => array(
				'type'    => 'checkbox',
				'label'   => __( 'Full width', 'make' ),
				'name'    => 'full-width',
				'default' => ttfmake_get_section_default( 'full-width', 'text' ),
			),
			300 => array(
				'type'    => 'select',
				'name'    => 'columns-number',
				'class'   => 'ttfmake-text-columns',
				'label'   => __( 'Columns', 'make' ),
				'default' => ttfmake_get_section_default( 'columns-number', 'text' ),
				'options' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6
				),
			),
			400 => array(
				'type'  => 'image',
				'name'  => 'background-image',
				'label' => __( 'Background image', 'make' ),
				'class' => 'ttfmake-configuration-media',
				'default' => ttfmake_get_section_default( 'background-image', 'text' ),
			),
			500 => array(
				'type'    => 'checkbox',
				'label'   => __( 'Darken background to improve readability', 'make' ),
				'name'    => 'darken',
				'default' => ttfmake_get_section_default( 'darken', 'text' ),
			),
			600 => array(
				'type'    => 'select',
				'name'    => 'background-style',
				'label'   => __( 'Background style', 'make' ),
				'default' => ttfmake_get_section_default( 'background-style', 'text' ),
				'options' => array(
					'tile'  => __( 'Tile', 'make' ),
					'cover' => __( 'Cover', 'make' ),
				),
			),
			700 => array(
				'type'    => 'color',
				'label'   => __( 'Background color', 'make' ),
				'name'    => 'background-color',
				'class'   => 'ttfmake-text-background-color ttfmake-configuration-color-picker',
				'default' => ttfmake_get_section_default( 'background-color', 'text' ),
			),
		);
	}

	/**
	 * Get default values for columns section
	 *
	 * @since 1.8
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'state' => 'open',
			'title' => '',
			'image-link' => '',
			'columns-number' => 3,
			'background-image' => '',
			'darken' => 0,
			'background-style' => 'tile',
			'background-color' => '',
			'full-width' => 0,
			'rows-number' => 1
		);
	}

	/**
	 * Get default values for column
	 *
	 * @since 1.8
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'title' => '',
			'image-id' => '',
			'image-link' => '',
			'image-url' => '',
			'size' => '',
			'content' => '',
			'sidebar-label' => '',
			'widget-area-id' => '',
			'widgets' => ''
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
		$defaults['text'] = $this->get_defaults();
		$defaults['text-item'] = $this->get_column_defaults();

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
		if ( $data['section-type'] == 'text' ) {
			$data = wp_parse_args( $data, $this->get_defaults() );
			$image = ttfmake_get_image_src( $data['background-image'], 'large' );

			if ( isset( $image[0] ) ) {
				$data['background-image-url'] = $image[0];
			}

			if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
				// back compatibility
				if ( isset( $data['columns-order'] ) ) {
					$ordered_items = array();

					foreach ( $data['columns-order'] as $index => $item_id ) {
						array_push($ordered_items, $data['columns'][$index+1]);

						if ( array_key_exists('sidebar-label', $ordered_items[$index]) && $ordered_items[$index]['sidebar-label'] != '' && empty($ordered_items[$index]['widget-area-id']) ) {
							$old_index = $index + 1; // index started at 1 before

							$page_id = get_the_ID();

							$ordered_items[$index]['widget-area-id'] = 'ttfmp-' . $page_id . '-' . $data['id'] . '-' . $old_index;
						}
					}

					$data['columns'] = $ordered_items;
					unset( $data['columns-order'] );
				}

				foreach ( $data['columns'] as $s => $column ) {
					$column = wp_parse_args( $column, $this->get_column_defaults() );

					// Handle legacy data layout
					$id = isset( $column['id'] ) ? $column['id']: $s;
					$data['columns'][$s]['id'] = $id;
					$column_image = ttfmake_get_image_src( $column['image-id'], 'large' );

					$column_image = ttfmake_get_image_src( $column['image-id'], 'large' );

					if ( isset( $column_image[0] ) ) {
						$data['columns'][$s]['image-url'] = $column_image[0];
					}

					if ( isset( $column['sidebar-label'] ) && !empty( $column['sidebar-label'] ) && empty( $column['widget-area-id'] ) ) {
						$data['columns'][$s]['widget-area-id'] = 'ttfmp-' . get_the_ID() . '-' . $data['id'] . '-' . $column['id'];
					}
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
		$clean_data = array();

		if ( isset( $data['columns-number'] ) ) {
			if ( in_array( $data['columns-number'], range( 1, 6 ) ) ) {
				$clean_data['columns-number'] = $data['columns-number'];
			}
		}

		$clean_data['title'] = $clean_data['label'] = ( isset( $data['title'] ) ) ? apply_filters( 'title_save_pre', $data['title'] ) : '';

		if ( isset( $data['background-image'] ) ) {
			$clean_data['background-image'] = ttfmake_sanitize_image_id( $data['background-image'] );
		}

		if ( isset( $data['darken'] ) && $data['darken'] == 1 ) {
			$clean_data['darken'] = 1;
		} else {
			$clean_data['darken'] = 0;
		}

		if ( isset( $data['background-color'] ) ) {
			$clean_data['background-color'] = maybe_hash_hex_color( $data['background-color'] );
		}

		if ( isset( $data['background-style'] ) ) {
			if ( in_array( $data['background-style'], array( 'tile', 'cover' ) ) ) {
				$clean_data['background-style'] = $data['background-style'];
			}
		}

		if ( isset( $data['full-width'] ) && $data['full-width'] == 1 ) {
			$clean_data['full-width'] = 1;
		} else {
			$clean_data['full-width'] = 0;
		}

		if ( isset( $data['columns'] ) && is_array( $data['columns'] ) ) {
			foreach ( $data['columns'] as $id => $item ) {
				if ( isset( $item['id'] ) ) {
					$clean_data['columns'][ $id ]['id'] = $item['id'];
				}

				if ( isset( $item['parentID'] ) ) {
					$clean_data['columns'][ $id ]['parentID'] = $item['parentID'];
				}

				if ( isset( $item['title'] ) ) {
					$clean_data['columns'][ $id ]['title'] = apply_filters( 'title_save_pre', $item['title'] );
				}

				if ( isset( $item['image-link'] ) ) {
					$clean_data['columns'][ $id ]['image-link'] = esc_url_raw( $item['image-link'] );
				}

				if ( isset( $item['image-id'] ) ) {
					$clean_data['columns'][ $id ]['image-id'] = ttfmake_sanitize_image_id( $item['image-id'] );

					$image = ttfmake_get_image_src( $item['image-id'], 'large' );

					if( isset( $image[0] ) ) {
						$clean_data['columns'][ $id ]['image-url'] = $image[0];
					}
				}

				if ( isset( $item['content'] ) ) {
					$clean_data['columns'][ $id ]['content'] = sanitize_post_field( 'post_content', $item['content'], ( get_post() ) ? get_the_ID() : 0, 'db' );
				}

				if ( isset( $item['size'] ) ) {
					$clean_data['columns'][ $id ]['size'] = esc_attr( $item['size'] );
				}

				if ( isset( $item['sidebar-label'] ) ) {
					$clean_data['columns'][ $id ]['sidebar-label'] = $item['sidebar-label'];
				}
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
			'builder-models-text',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/models/text.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-models-text-item',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/models/text-item.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-views-text',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/views/text.js',
			array(),
			TTFMAKE_VERSION,
			true
		);

		wp_register_script(
			'builder-views-text-item',
			Make()->scripts()->get_js_directory_uri() . '/builder/sections/views/text-item.js',
			array( 'builder-views-item' ),
			TTFMAKE_VERSION,
			true
		);

		return array_merge( $deps, array(
			'builder-models-text',
			'builder-models-text-item',
			'builder-views-text',
			'builder-views-text-item'
		) );
	}
}
endif;
