<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Listing_Slider_Widget_Property_Slider
 *
 * @class Inventor_Listing_Slider_Widget_Property_Slider
 * @package Inventor_Listing_Slider/Classes/Widgets
 * @author Pragmatic Mates
 */
class Inventor_Listing_Slider_Widget_Property_Slider extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct(
			'listing_slider',
			__( 'Listing Slider', 'inventor-listing-slider' ),
			array(
				'description' => __( 'Displays listings in slider.', 'inventor-listing-slider' ),
			)
		);

		add_action( 'body_class', array( __CLASS__, 'add_body_class' ) );
	}

	/**
	 * Adds classes to body
	 *
	 * @param $classes array
	 *
	 * @access public
	 * @return array
	 */
	public static function add_body_class( $classes ) {
		$settings = get_option( 'widget_listing_slider' );

		if ( is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				if ( is_active_widget( false, 'listing_slider-' . $key, 'listing_slider' ) ) {
					if ( ! empty( $value['classes'] ) ) {
						$parts   = explode( ',', $value['classes'] );
						$classes = array_merge( $classes, $parts );
					}
				}
			}
		}

		return $classes;
	}

	/**
	 * Frontend
	 *
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( class_exists( 'Inventor' ) ) {
			$ids = explode( ',', $instance['ids'] );

			query_posts( array(
				'post_type'         => Inventor_Post_Types::get_listing_post_types(),
				'post_status'       => 'publish',
				'posts_per_page'    => -1,
				'post__in'          => $ids,
				'orderby'           => 'post__in'
			) );

			include Inventor_Template_Loader::locate( 'widgets/listing-slider', INVENTOR_LISTING_SLIDER_DIR );

			wp_reset_query();
		}
	}

	/**
	 * Update
	 *
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Backend
	 *
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		if ( class_exists( 'Inventor_Template_Loader' ) ) {
			include Inventor_Template_Loader::locate( 'widgets/listing-slider-admin', INVENTOR_LISTING_SLIDER_DIR );
			include Inventor_Template_Loader::locate( 'widgets/visibility-admin' );
		}
	}
}