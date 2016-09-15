<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Utilities
 *
 * @class Inventor_Utilities
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Utilities {
	/**
	 * Initialize utilities
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_filter( 'init', array( __CLASS__, 'check_gateway_in_payment_process' ), 1 );
		add_filter( 'inventor_listing_title', array( __CLASS__, 'listing_title' ), 10, 2 );
	}

	/**
	 * Saves alert message which will be shown to user
	 *
	 * @access public
	 * @param string $status
	 * @param string $message
	 * @return void
	 */
	public static function show_message( $status, $message ) {
		if ( 'SESSION' == apply_filters( 'inventor_visitor_data_storage', INVENTOR_DEFAULT_VISITOR_DATA_STORAGE ) ) {
			$_SESSION['messages'][] = array( $status, $message );
		} else {
			// save message to request if user is not redirected to new page
			$_REQUEST['messages'][] = array( $status, $message );

			// COOKIE messages only works if user is redirected to new page
			if ( isset( $_COOKIE['messages'] ) ) {
				$messages = $_COOKIE['messages'];
				$messages = json_decode( $messages, true );
			} else {
				$messages = array();
			}

			$messages[] = array( $status, $message );
			$messages = json_encode( $messages );

			setcookie( 'messages', $messages, 0, $path = '/' );
		}
	}

	/**
	 * Returns alert messages which will be shown to user
	 *
	 * @access public
	 * @return array
	 */
	public static function get_messages() {
		if ( isset( $_REQUEST['messages'] ) ) {
			$messages = $_REQUEST['messages'];
		} else {
			if ( isset( $_COOKIE['messages'] ) ) {
				$messages = json_decode(stripcslashes($_COOKIE['messages']), true);
			}
		}

		if ( isset( $messages ) ) {
			return Inventor_Utilities::array_unique_multidimensional( $messages );
		}

		return null;
	}

	/**
	 * Alters listing title. Appends verified branding logo.
	 *
	 * @access public
	 * @param string $title
	 * @param int $post_id
	 * @return string
	 */
	public static function listing_title( $title, $post_id ) {
//		$rendered_logo = self::render_logo( $post_id );
		$logo = get_post_meta( $post_id, INVENTOR_LISTING_PREFIX  . 'logo', true );

		if ( ! empty( $logo ) ) {
			$rendered_logo = '<img src="'. $logo .'" class="listing-title-logo">';
			$title = $rendered_logo. ' ' . $title;
		}

		return $title;
	}

	/**
	 * Checks if user allowed to remove post
	 *
	 * @access public
	 * @param $user_id int
	 * @param $item_id int
	 * @return bool
	 */
	public static function is_allowed_to_remove( $user_id, $item_id ) {
		$item = get_post( $item_id );
		if ( ! empty( $item->post_author ) ) {
			if ( $item->post_author == $user_id ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Gets link for login
	 *
	 * @access public
	 * @return bool|string
	 */
	public static function get_link_for_login() {
		$login_required_page = get_theme_mod( 'inventor_general_login_required_page', null );
		if ( ! empty( $login_required_page ) ) {
			return get_permalink( $login_required_page );
		}
		return false;
	}

	/**
	 * Makes multi dimensional array
	 *
	 * @access public
	 * @param $input array
	 * @return array
	 */
	public static function array_unique_multidimensional( $input ) {
		$serialized = array_map( 'serialize', $input );
		$unique = array_unique( $serialized );
		return array_intersect_key( $input, $unique );
	}

	/**
	 * Gets all pages list
	 *
	 * @access public
	 * @return array
	 */
	public static function get_pages() {
		$pages = array();
		$pages[] = __( 'Not set', 'inventor' );

		foreach ( get_pages() as $page ) {
			$pages[ $page->ID ] = $page->post_title;
		}

		return $pages;
	}

	/**
	 * Sanitize a string from textarea
	 *
	 * check for invalid UTF-8,
	 * Convert single < characters to entity,
	 * strip all tags,
	 * strip octets.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function sanitize_textarea( $str ) {
		$filtered = wp_check_invalid_utf8( $str );

		if ( strpos($filtered, '<') !== false ) {
			$filtered = wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = wp_strip_all_tags( $filtered, true );
		}

		$found = false;
		while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
			$filtered = str_replace($match[0], '', $filtered);
			$found = true;
		}

		if ( $found ) {
			// Strip out the whitespace that may now exist after removing the octets.
			$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
		}

		/**
		 * Filter a sanitized textarea string.
		 *
		 * @param string $filtered The sanitized string.
		 * @param string $str      The string prior to being sanitized.
		 */
		return apply_filters( 'sanitize_textarea', $filtered, $str );
	}

	/**
	 * Get UUID
	 *
	 * @access public
	 * @return string
	 */
	public static function get_uuid() {
		$chars = md5( uniqid( rand() ) );
		$uuid  = substr( $chars, 0, 8 ) . '-';
		$uuid .= substr( $chars, 8, 4 ) . '-';
		$uuid .= substr( $chars, 12, 4 ) . '-';
		$uuid .= substr( $chars, 16, 4 ) . '-';
		$uuid .= substr( $chars, 20, 12 );
		return $uuid;
	}

	/**
	 * Short UUID
	 *
	 * @access public
	 * @param string $prefix
	 * @return string
	 */
 	public static function get_short_uuid( $prefix = '') {
	    $uuid = self::get_uuid();
	    $parts = explode( '-', $uuid );
	    return $prefix . $parts[0];
	}

	/**
	 * Build children hierarchy
	 *
	 * @access public
	 * @param $taxonomy
	 * @param $selected
	 * @param $parent_term
	 * @param $depth
	 * @param $hide_empty
	 * @return null|string
	 */
	public static function build_hierarchical_taxonomy_select_options( $taxonomy, $selected = null, $parent_term = null, $depth = 1, $hide_empty = false ) {
		$output = null;

		$terms = get_terms( $taxonomy, array(
			'hide_empty'    => $hide_empty,
			'parent'        => $parent_term ? $parent_term->term_id : 0,
		) );

		if ( ! empty( $terms ) && is_array( $terms ) ) {
			$output = '';

			foreach( $terms as $term ) {
				$args = array(
					'value' => $term->slug,
					'label' => str_repeat( "&raquo;&nbsp;", $depth - 1 ) . ' ' . $term->name,
				);

				if ( $term->slug == $selected ) {
					$args['checked'] = 'checked';
				}

				$output .= sprintf( "\t" . '<option value="%s" %s>%s</option>', $args['value'], selected( isset( $args['checked'] ) && $args['checked'], true, false ), $args['label'] ) . "\n";
				$children = self::build_hierarchical_taxonomy_select_options( $taxonomy, $selected, $term, $depth + 1 );

				if ( ! empty( $children ) ) {
					$output .= $children;
				}
			}
		}

		return $output;
	}

	/**
	 * Checks if gateway was chose in payment form
	 *
	 * @access public
	 * @return void
	 */
	public static function check_gateway_in_payment_process() {
		if ( ! isset( $_POST['process-payment'] ) ) {
			return;
		}

		if ( empty( $_POST['payment_gateway'] ) ) {
			Inventor_Utilities::show_message( 'danger', __( 'Choose payment gateway please.', 'inventor' ) );
		}
	}

	/**
	 * Returns logins of all site administrators
	 *
	 * @access public
	 * @return array
	 */
	public static function get_site_admins() {
		$logins = array();
		$administrators = get_users( 'role=administrator' );

		foreach ( $administrators as $user ) {
			$logins[] = $user->user_login;
		}

		return $logins;
	}

	/**
	 * Returns url which user should be redirected after payment
	 *
	 * @access public
	 * @param $payment_type
	 * @param $object_id
	 * @return array
	 */
	public static function get_after_payment_url( $payment_type, $object_id ) {
		// check if object is of listing type and if so, gets its permalink instead of site url
		if ( in_array( $payment_type, array( 'featured_listing', 'publish_listing' ) ) ) {
			// redirect to my listings page (if exists)
			$submission_list_page = get_theme_mod( 'inventor_submission_list_page', false );
			if ( ! empty( $submission_list_page ) ) {
				$url = get_permalink( $submission_list_page );
			}
		}

		// object detail page
		if ( ! isset( $url ) && isset ( $object_id ) ) {
			$object_post_type = get_post_type( $object_id );
			if ( in_array( $object_post_type, Inventor_Post_Types::get_listing_post_types() ) ) {
				$url = get_permalink( $object_id );
			}
		}

		if ( ! isset( $url ) ) {
			// after payment page
			$after_payment_page = get_theme_mod( 'inventor_general_after_payment_page' );
			$url = $after_payment_page ? get_permalink( $after_payment_page ) : site_url();
		}

		return $url;
	}
}

Inventor_Utilities::init();