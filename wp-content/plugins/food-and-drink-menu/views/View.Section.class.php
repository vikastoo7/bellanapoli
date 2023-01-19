<?php

/**
 * Class for any section view requested on the front end.
 *
 * @since 1.1
 */

class fdmViewSection extends fdmView {

	public $title = '';
	public $description = '';

	public $background_image_placement = 'hidden';
	public $image_url = '';
	public $title_class = ''; 

	public $min_price = 1000000; 
	public $max_price = 0;

	// Full menu object to capture the section's post data
	public $menu = null;

	/**
	 * Initialize the class
	 * @since 1.1
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );

		// Gather data if it's not already set
		$this->load_section();
	}

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 1.1
	 */
	public function render() {
		global $fdm_controller;

		if ( !isset( $this->id ) ) {
			return;
		}

		if ( !isset( $this->items ) || ( is_array( $this->items ) && !count( $this->items ) ) ) {
			return;
		}

		$this->set_allowed_tags();

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Define the classes for this section
		$this->set_classes();

		// Capture output
		ob_start();

		if ( ! empty( $this->stand_alone ) ) { $this->add_custom_styling(); }

		$template = $this->find_template( 'menu-section' );

		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'fdm_menu_section_output', $output, $this );
	}

	/**
	 * Print the menu items in this section
	 *
	 * @note This just cleans up the template file a bit
	 * @since 1.1
	 */
	public function print_items() {
		$output = '';
		if ( isset( $this->items ) && is_array( $this->items ) ) {
			foreach ( $this->items as $item ) {
				$output .= $item->render();
			}
		}
		return $output;
	}

	/**
	 * Load section data
	 * @since 1.1
	 */
	public function load_section() {
		global $fdm_controller;

		if ( !isset( $this->id ) ) {
			return;
		}

		// Make sure the section has posts before we load the data.
		$items = new WP_Query( array(
			'post_type'      	=> 'fdm-menu-item',
			'posts_per_page' 	=> -1,
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order title',
			'tax_query'     	=> array(
				array(
					'taxonomy' => 'fdm-menu-section',
					'field'    => 'term_id',
					'terms'    => $this->id,
				),
			),
		));
		if ( !count( $items->posts ) ) {
			return;
		}

		// We go ahead and store all the posts data now to save on db calls
		$this->items = array();
		foreach( $items->posts as $item ) {
			$item = new fdmViewItem(
				array(
					'id' => $item->ID,
					'post' => $item,
					'section' => $this->id
				)
			);

			$item->load_item();

			$this->min_price = min( $this->min_price, $item->min_price );
			$this->max_price = max( $this->max_price, $item->max_price );

			$this->items[] = $item;
		}

		if ( !$this->title ) {
			$section = get_term( $this->id, 'fdm-menu-section' );
			$this->title = $section->name;
			$this->slug = $section->slug;
			$this->description = $section->description;

			$this->image_url = wp_get_attachment_url(get_term_meta( $this->id, '_fdm_menu_section_image', true ));
			$this->background_image_placement = $fdm_controller->settings->get_setting('fdm-menu-section-image-placement');
			$this->title_class = ( ( $fdm_controller->settings->get_setting('fdm-menu-section-image-placement') == 'background' and $this->image_url ) ? 'fdm-hike-up-title' : '' );
		}

		// Load any custom title that has been set for display in this menu
		if ( isset( $this->menu ) && get_class( $this->menu ) == 'fdmViewMenu' ) {
			$menu_post_meta = get_post_meta( $this->menu->id );

			if ( isset( $menu_post_meta['fdm_menu_section_' . $this->id ] ) ) {
				$this->title = $menu_post_meta['fdm_menu_section_' . $this->id ][0];
			}
		}

		do_action( 'fdm_load_section', $this );

	}

	/**
	 * Set the menu section css classes
	 * @since 1.1
	 */
	public function set_classes( $classes = array() ) {
		global $fdm_controller;

		if( 'image' == $fdm_controller->settings->get_setting('fdm-pro-style') ) {
			$number_of_columns = $fdm_controller->settings->get_setting('fdm-image-style-columns');
		}
		elseif( 'refined' == $fdm_controller->settings->get_setting('fdm-pro-style') ) {
			$number_of_columns = $fdm_controller->settings->get_setting('fdm-refined-style-columns');
		}
		else {
			$number_of_columns = 'one';
		}

		$classes = array_merge(
			$classes,
			array(
				'fdm-section',
				'fdm-sectionid-' . $this->id,
				'fdm-section-' . $this->slug,
				'fdm-section-' . $number_of_columns,
			)
		);

		// Order of this section appearing on this menu
		if ( isset( $this->order ) ) {
			$classes[] = 'fdm-section-' . $this->order;
		}

		$this->classes = apply_filters( 'fdm_menu_section_classes', $classes, $this );
	}

}
