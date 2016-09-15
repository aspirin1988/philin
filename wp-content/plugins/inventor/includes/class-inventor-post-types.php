<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Post_Types
 *
 * @class Inventor_Post_Types
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Post_Types {
	public static $listings_types = array();

	/**
	 * Initialize listing types
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		self::includes();

		add_action( 'init', array( __CLASS__, 'set_all_listing_post_types' ), 12 );
		add_action( 'init', array( __CLASS__, 'disable_post_types' ), 12 );

		add_filter( 'inventor_listing_type_supported', array( __CLASS__, 'listing_type_supported' ), 10, 2 );
	}

	/**
	 * Loads listing types
	 *
	 * @access public
	 * @return void
	 */
	public static function includes() {
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-transaction.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-report.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-user.php';

		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-listing.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-business.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-car.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-dating.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-education.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-event.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-food.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-hotel.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-pet.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-shopping.php';
		require_once INVENTOR_DIR . 'includes/post-types/class-inventor-post-type-travel.php';
	}

	/**
	 * Checks if listing post type is supported
	 *
	 * @access public
	 * @param bool $supported
	 * @param string $post_type
	 * @return bool
	 */
	public static function listing_type_supported( $supported, $post_type ) {
		// check if theme supports listing type
		if ( ! current_theme_supports( 'inventor-listing-types' ) ) {
			return false;
		}

		$support = get_theme_support( 'inventor-listing-types' );
		$supported_by_theme = $support[0];

		if ( ! in_array( $post_type, $supported_by_theme ) ) {
			return false;
		}

		return $supported;
	}

	/**
	 * Remove post types base on theme settings
	 */
	public static function disable_post_types() {
		global $wp_post_types;

		$post_types_all = self::get_listing_post_types();
		$post_types_supported = get_theme_mod( 'inventor_general_post_types', $post_types_all );
		$post_types_unsupported = array_diff( $post_types_all, $post_types_supported );

		if ( is_array( $post_types_unsupported ) ) {
			foreach( $post_types_unsupported as $post_type ) {
				if ( ! empty( $wp_post_types[ $post_type ] ) ) {
					unset( $wp_post_types[ $post_type ] );
				}
			}
		}
	}

	/**
	 * Get list of enabled post type identifiers
	 *
	 * @access public
	 * @param bool $include_abstract
	 * @param bool $with_labels
	 * @return array
	 */
	public static function get_listing_post_types( $include_abstract = false, $with_labels = false ) {
		$listings_types = array();

		$post_types = get_post_types( array(), 'objects' ); // in this moment, all disabled post types should be removed

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( $post_type->show_in_menu === 'listings' ) {
					if ( $with_labels ) {
						$post_type_obj = get_post_type_object( $post_type->name );
						$listings_types[ $post_type->name ] = esc_attr( $post_type_obj->labels->singular_name );
					} else {
						$listings_types[] = $post_type->name;
					}
				}
			}
		}

		// Sort alphabetically
		if( $with_labels ) {
			asort( $listings_types );
		} else {
			sort( $listings_types );
		}

		if ( $include_abstract ) {
			array_unshift( $listings_types, 'listing' );
		}

		return $listings_types;
	}

	/**
	 * Gets all listing post type objects (including enabled and also disabled)
	 *
	 * @access public
	 * @return array
	 */
	public static function get_all_listing_post_types() {
		return self::$listings_types;
	}

	/**
	 * Sets all listing post type objects before we unregister some of them
	 *
	 * @access public
	 * @return array
	 */
	public static function set_all_listing_post_types() {
		$post_types = get_post_types( array(), 'objects' );
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( $post_type->show_in_menu === 'listings' ) {
					self::$listings_types[] = get_post_type_object( $post_type->name );
				}
			}
		}

		return self::$listings_types;
	}

	/**
	 * Returns listing type name of given listing
	 *
	 * @access public
	 * @param $post_id
	 * @return string
	 */
	public static function get_listing_type_name( $post_id = null ) {
		if ( empty ( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$listing_type = get_post_type_object( get_post_type( $post_id ) );
		if ( ! empty( $listing_type ) ) {
			return $listing_type->labels->singular_name;
		}

		return null;
	}

	/**
	 * Get listing by its id
	 *
	 * @access public
	 * @param int $id
	 * @return object
	 */
	public static function get_listing( $id ) {
		$post = get_post( $id );

		if ( empty( $post ) || ! in_array( $post->post_type, self::get_listing_post_types() ) ) {
			return null;
		}

		return $post;
	}

	/**
	 * Returns count of all listings of specified post type and status
	 *
	 * @access public
	 * @param $post_types array
	 * @return int
	 */
	public static function count_post_types( $post_types, $status = 'publish' ) {
		$result = 0;

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		foreach ( $post_types as $post_type ) {
			if ( ! empty( wp_count_posts( $post_type )->$status ) ) {
				$result += wp_count_posts( $post_type )->$status;
			}
		}

		return $result;
	}

	/**
	 * Adds metabox to post type
	 *
	 * @access public
	 * @param $post_type
	 * @param array $metaboxes
	 * @return void
	 */
	public static function add_metabox( $post_type, array $metaboxes ) {
		if ( sizeof( $metaboxes ) > 0 ) {
			foreach ( $metaboxes as $metabox ) {
				if ( 2 === count( explode( '::', $metabox ) ) ) {
					$parts = explode( '::', $metabox );
					$name = 'metabox_' . $parts[1];
					$parts[0]::$name( $post_type );
				} else {
					if ( apply_filters( 'inventor_metabox_assigned', true, $metabox, $post_type ) ) {
						$name = 'metabox_' . $metabox;
						Inventor_Metaboxes::$name( $post_type );
					}
				}
			}
		}
	}

	/**
	 * Removes metabox from post type
	 *
	 * @access public
	 * @param $post_type
	 * @param array $metaboxes
	 * @return void
	 */
	public static function remove_metabox( $post_type, array $metaboxes ) {
		if ( sizeof( $metaboxes ) > 0 ) {
			foreach ( $metaboxes as $metabox ) {
				$metabox_id = INVENTOR_LISTING_PREFIX . $post_type . '_' . $metabox;

				if ( ! empty( $metabox_id ) ) {
					CMB2_Boxes::remove( $metabox_id );
				}
			}
		}
	}

	/**
	 * Returns formatted opening hours output for given day
	 *
	 * @access public
	 * @param $day array
	 * @param $tags bool
	 * @return string
	 */
	public static function opening_hours_format_day( $day, $tags = false ) {
		// get time format from WordPress settings
		$time_format = get_option('time_format');

		$time_from = empty ( $day['listing_time_from'] ) ? '' : date( $time_format, strtotime( $day['listing_time_from'] ) );
		$time_to = empty ( $day['listing_time_to'] ) ? '' : date( $time_format, strtotime( $day['listing_time_to'] ) );
		$custom_text = empty ( $day['listing_custom'] ) ? '' : $day['listing_custom'];

		if ( ! empty( $custom_text ) ) {
			$opening = "{$custom_text}";
		} else {
			if ( $tags ) {
				$opening = "<span class=\"from\">{$time_from}</span> <span class=\"separator\">-</span> <span class=\"to\">{$time_to}</span>";
			} else {
				$opening = "{$time_from} - {$time_to}";
			}
		}

		$trim_characters = $time_from == '' && $time_to == '' ? ' -' : ' ';
		trim( $opening, $trim_characters );

		return $opening;
	}

	/**
	 * Returns current status of opening hours
	 *
	 * @access public
	 * @param int $listing_id
	 * @param string $day
	 * @return string
	 */
	public static function opening_hours_status( $listing_id, $day = null ) {
		$opening_hours = get_post_meta( $listing_id, INVENTOR_LISTING_PREFIX . 'opening_hours', true );

		if ( empty( $opening_hours ) ) {
			return 'unknown';
		}

		$week = array( 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY' );

		// backup previous timezone set
		$previous_timezone = date_default_timezone_get();

		// @TODO: should get timezone from opening hours field, if not empty
		$wordpress_timezone = get_option('timezone_string');

		// set timezone
		if ( ! empty( $wordpress_timezone ) ) {
			$offset = 0;
			date_default_timezone_set( $wordpress_timezone );
		} else {
			$offset = get_option( 'gmt_offset' );
		}

		// current time
		$now = time();

		if ( $offset != 0 ) {
			$now += $offset * 60 * 60;
		}

		// week day
		$today_index = date( 'N' );
		$week_day = $week[ $today_index - 1 ];

		if( $day != null && $day != $week_day ) {
			$status = 'other-day';
		} else {
			// default status
			$status = 'closed';

			// find opening hours for today
			foreach ( $opening_hours as $opening_day ) {
				if ( $opening_day['listing_day'] == $week_day ) {
					if ( ! empty( $opening_day['listing_time_from'] ) && ! empty( $opening_day['listing_time_to'] ) ) {
						// if opening hours is set, check current time
						$time_from = strtotime( $opening_day['listing_time_from'] );
						$time_to = strtotime( $opening_day['listing_time_to'] );

						$status = $time_from <= $now && $now <= $time_to ? 'open' : 'closed';
						break;
					}
				}
			}
		}

		// set back previous timezone
		if ( ! empty( $previous_timezone ) ) {
			date_default_timezone_set( $previous_timezone );
		}

		// return status
		return $status;
	}

	/**
	 * Check if listing has opening hours
	 *
	 * @access public
	 * @param $listing_id
	 * @return bool
	 */
	public static function opening_hours_visible( $listing_id = null ) {
		if ( $listing_id == null ) {
			$listing_id = get_the_ID();
		}

		$opening_hours = get_post_meta( $listing_id, INVENTOR_LISTING_PREFIX . 'opening_hours', true );

		if ( is_array( $opening_hours ) ) {
			foreach( $opening_hours as $opening_hour ) {
				if ( ! empty( $opening_hour['listing_time_from'] ) || ! empty( $opening_hour['listing_time_to'] ) || ! empty( $opening_hour['listing_custom'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns rendered field value of given post
	 *
	 * @access public
	 * @param array $field
	 * @param string $post_id
	 * @return mixed
	 */

	public static function get_field_value( $field, $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$value = get_post_meta( $post_id, $field['id'], true );

		if ( empty( $value ) && 0 !== strpos( $field['type'], 'taxonomy_' ) ) {
			return null;
		}

		// Select
		if ( in_array( $field['type'], array( 'select', 'radio', 'radio_inline' ) ) ) {
			$value = empty( $field['options'][ $value ] ) ? $value : $field['options'][ $value ];
		}

		// Regular taxonomies
		if ( in_array( $field['type'], array( 'taxonomy_select', 'taxonomy_radio', 'taxonomy_radio_inline', 'taxonomy_multicheck_inline', 'taxonomy_multicheck_hierarchy', 'taxonomy_select_hierarchy' ) ) ) {
			$terms = wp_get_post_terms( $post_id, $field['taxonomy'] );
			$count = count( $terms );

			if ( is_array( $terms ) && $count > 0 ) {
				$value = '';
				$index = 0;

				foreach ( $terms as $term ) {
					$value .= $term->name;
					if ( $index + 1 != $count ) {
						$value .= ', ';
					}
					$index++;
				}
			}
		}

		// Locations
		if ( array_key_exists( 'taxonomy', $field ) ) {
			switch ( $field['taxonomy'] ) {
				case 'locations':
					$value = Inventor_Query::get_listing_location_name( $post_id, '/', true );
					break;
				case 'listing_categories':
					$value = Inventor_Query::get_listing_category_name( $post_id, '/', true );
					break;
			}
		}

		// Multicheck and multicheck inline
		if ( in_array( $field['type'], array( 'multicheck', 'multicheck_inline' ) ) ) {
			$value = implode( ', ', $value );
			// TODO: $value values are keys now, it works for fields defined via inventor-fields plugin
			// TODO: it won't work for fields defined in source code. we need to pick 'options' attribute from field definition
		}

		// Email
		if ( 'text_email' == $field['type'] ) {
			$value = '<a href="mailto:' . $value . '">' . $value .'</a>';
		}

		// URL
		if ( 'text_url' == $field['type'] ) {
			$value = '<a href="' . $value . '">' . str_replace( array( 'http://', 'https://' ), '', $value ) .'</a>';
		}

		// File
		if ( 'file' == $field['type'] ) {
			$value = '<a href="'. $value .'">'. basename( $value ).'</a>';
		}

		// File
		if ( 'file_list' == $field['type'] && is_array( $value ) ) {
			$file_list = array();

			foreach ( $value as $file ) {
				$file_list[] = '<a href="'. $file .'">'. basename( $file ).'</a>';
			}

			$value = join( ', ', $file_list );
		}

		// Money
		if ( 'text_money' == $field['type'] ) {
			$value = Inventor_Price::format_price( $value );
		}

		// Checkbox
		if ( 'checkbox' == $field['type'] ) {
			if ( 'on' == $value ) {
				$value = __( 'Yes', 'inventor' );
			} else {
				$value = __( 'No', 'inventor' );
			}
		}

		// Text Date Timestamp
		if ( 'text_date_timestamp' == $field['type'] ) {
			$value = date_i18n( get_option( 'date_format' ), $value );
		}

		if ( 'text_datetime_timestamp' == $field['type'] ) {
			$date = date_i18n( get_option( 'date_format' ), $value );
			$time = date_i18n( get_option( 'time_format' ), $value );
			$value = $date . ' ' . $time;
		}

		// ColorPicker
		if ( 'colorpicker' == $field['type'] ) {
			$value = sprintf( '<span class="listing-color" style="background-color: %s"></span>', $value );
		}

		// Weight
		if ( INVENTOR_LISTING_PREFIX  . 'weight' == $field['id'] ) {
			$weight_unit = get_theme_mod( 'inventor_measurement_weight_unit', 'lbs' );
			$value = sprintf( '%s %s', $value, $weight_unit );
		}

		// Apply filter
		$value = apply_filters( 'inventor_attribute_value', $value, $field );

		return $value;
	}

	/**
	 * Gets list of attributes for post
	 *
	 * @param null $post_id
	 * @return array
	 */
	public static function get_attributes( $post_id = null ) {
		$results = array();

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$post_type = get_post_type( $post_id );
		$meta_boxes = CMB2_Boxes::get_all();

		// Price
		$price = Inventor_Price::get_price( $post_id );
		if ( ! empty( $price ) ) {
			$results['price'] = array(
				'name'      => __( 'Price', 'inventor' ),
				'value'     => $price
			);
		}

		foreach ( $meta_boxes as $meta_box ) {
			$object_types = $meta_box->meta_box['object_types'];

			if ( ! empty( $object_types ) && ! in_array( 'listing', $object_types ) && ! in_array( $post_type, $object_types ) ) {
				continue;
			}

			if ( ! empty( $meta_box->meta_box['skip'] ) && true == $meta_box->meta_box['skip'] ) {
				continue;
			}

			$fields = $meta_box->meta_box['fields'];
			foreach ( $fields as $field ) {
				if ( ! empty( $field['skip'] ) && true == $field['skip'] ) {
					continue;
				}

				$value = self::get_field_value( $field, $post_id );

				if ( empty( $value ) ) {
					continue;
				}

				// Automatically skip attributes without name (groups)
				if ( ! empty( $field['name'] ) ) {
					$results[ $field['id'] ] = array(
						'name'  => $field['name'],
						'value' => $value,
					);
				}
			}
		}

		return $results;
	}

	/**
	 * Gets all detail sections of current post and renders it
	 *
	 * @return void
	 */
	public static function render_listing_detail_sections() {
		$post_type = get_post_type();

		$default_section_titles = array(
			'gallery' => esc_attr__( 'Gallery', 'inventor' ),
			'description' => esc_attr__( 'Description', 'inventor' ),
			'overview' => esc_attr__( 'Details', 'inventor' ),
			'video' => esc_attr__( 'Video', 'inventor' ),
			'food_menu' => esc_attr__( 'Meals And Drinks', 'inventor' ),
			'opening_hours' => esc_attr__( 'Opening Hours', 'inventor' ),
			'location' => esc_attr__( 'Location', 'inventor' ),
			'contact' => esc_attr__( 'Contact', 'inventor' ),
			'social' => esc_attr__( 'Social connections', 'inventor' ),
			'faq' => esc_attr__( 'FAQ', 'inventor' ),
			'comments' => null,
			'report' => null
		);

		// get section titles from metaboxes (if exist)
		$sections = array();
		foreach( $default_section_titles as $metabox_key => $default_title ) {
			$metabox_id = Inventor_Metaboxes::get_metabox_id( $metabox_key, $post_type );
			$metabox = CMB2_Boxes::get( $metabox_id );
			$sections[ $metabox_key ] = empty ( $metabox ) ? $default_title : $metabox->meta_box['title'];
		}

		// custom sections
		$custom_sections = apply_filters( 'inventor_listing_detail_custom_sections', array(), $post_type );
		$sections = array_slice( $sections, 0, 3, true ) + $custom_sections + array_slice( $sections, 3, count( $sections )-3, true);
		$sections = apply_filters( 'inventor_listing_detail_sections', $sections, $post_type );

		// render each section
		foreach( $sections as $section => $section_title ) {
			$section_with_underscores = str_replace( '-', '_', $section );
			$section_with_hyphens = str_replace( '_', '-', $section );

			// action before listing section
			do_action( 'inventor_before_listing_detail_' . $section_with_underscores );

			$plugin_dir = apply_filters('inventor_listing_detail_section_root_dir', INVENTOR_DIR, $section);

			$metabox_key = $section;
			$metabox_id = Inventor_Metaboxes::get_metabox_id( $metabox_key, get_post_type() );
			$metabox = CMB2_Boxes::get( $metabox_id );

			$params = array(
				'metabox_key' 	=> $metabox_key,
				'metabox_id' 	=> $metabox_id,
				'section_title' => $section_title,
			);

			if( $metabox ) {
				$fields = $metabox->prop( 'fields' );
				$params['fields'] = $fields;
			}

			try {
				echo Inventor_Template_Loader::load( 'listings/detail/section-' . $section_with_hyphens, $params, $plugin_dir );
			} catch (Exception $e) {
				if ( strpos( $e->getMessage(), 'not found') !== false ) {
					echo Inventor_Template_Loader::load( 'listings/detail/section-generic', $params, $plugin_dir );
				}
			}
			// action after listing section
			do_action( 'inventor_after_listing_detail_' . $section_with_underscores );
		}
	}

	/**
	 * Checks if listing is featured
	 *
	 * @access public
	 * @param $post_id
	 * @return boolean
	 */
	public static function is_featured_listing( $post_id = null ) {
		$post_id = $post_id == null ? get_the_ID() : $post_id;
		return get_post_meta( $post_id, INVENTOR_LISTING_PREFIX . 'featured', true );
	}

	/**
	 * Checks if listing is reduced
	 *
	 * @access public
	 * @param $post_id
	 * @return boolean
	 */
	public static function is_reduced_listing( $post_id = null ) {
		$post_id = $post_id == null ? get_the_ID() : $post_id;
		return get_post_meta( $post_id, INVENTOR_LISTING_PREFIX . 'reduced', true );
	}
}

Inventor_Post_Types::init();