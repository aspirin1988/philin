<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Post_Type_Listing
 *
 * @class Inventor_Post_Type_Listing
 * @package Inventor/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Post_Type_Listing {
	/**
	 * Initialize custom post type
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ) );
		add_action( 'init', array( __CLASS__, 'process_inquire_form' ), 9999 );
		add_action( 'pre_get_posts', array( __CLASS__, 'show_all_listings') );
	}

	/**
	 * @access public
	 * @param $post_id int
	 * @return null
	 */
	public static function get_inventor_poi( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$categories = wp_get_post_terms( $post_id, 'listing_categories', array(
			'orderby'   => 'parent',
			'order'     => 'ASC',
		) );

		if ( is_array( $categories ) && count( $categories ) > 0 ) {
			$category = array_shift( $categories );
			return Taxonomy_MetaData::get( 'listing_categories', $category->term_id, 'poi' );
		}

		return null;
	}

	/**
	 * Custom post type definition
	 *
	 * @access public
	 * @return void
	 */
	public static function definition() {
		$labels = array(
			'name'                  => __( 'Listings', 'inventor' ),
			'singular_name'         => __( 'Listing', 'inventor' ),
			'add_new'               => __( 'Add New Listing', 'inventor' ),
			'add_new_item'          => __( 'Add New Listing', 'inventor' ),
			'edit_item'             => __( 'Edit Listing', 'inventor' ),
			'new_item'              => __( 'New Listing', 'inventor' ),
			'all_items'             => __( 'Listings', 'inventor' ),
			'view_item'             => __( 'View Listing', 'inventor' ),
			'search_items'          => __( 'Search Listing', 'inventor' ),
			'not_found'             => __( 'No Listings found', 'inventor' ),
			'not_found_in_trash'    => __( 'No Listings Found in Trash', 'inventor' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Listing', 'inventor' ),
		);

		register_post_type( 'listing',
			array(
				'labels'            => $labels,
				'show_in_menu'	    => 'listings',
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
				'has_archive'       => true,
				'rewrite'           => array( 'slug' => _x( 'listings', 'URL slug', 'inventor' ) ),
				'public'            => true,
				'show_ui'           => false,
				'categories'        => array(),
			)
		);
	}

	/**
	 * Display all listings
	 *
	 * @access public
	 * @param $query
	 * @return mixed
	 */
	public static function show_all_listings( $query ) {
		if ( is_post_type_archive( 'listing' ) && $query->is_main_query() && ! is_admin() && 'listing' == $query->query_vars['post_type'] ) {
			$query->set( 'post_type', Inventor_Post_Types::get_listing_post_types( true ) );
			return $query;
		}

		return null;
	}

	/**
	 * Process enquire form
	 *
	 * @access public
	 * @return void
	 */
	public static function process_inquire_form() {
		if ( ! isset( $_POST['inquire_form'] ) || empty( $_POST['post_id'] ) ) {
			return;
		}

		if ( class_exists( 'Inventor_Recaptcha' ) && Inventor_Recaptcha_Logic::is_recaptcha_enabled() ) {
			if ( array_key_exists( 'g-recaptcha-response', $_POST ) ) {
				$is_recaptcha_valid = Inventor_Recaptcha_Logic::is_recaptcha_valid( $_POST['g-recaptcha-response'] );

				if ( ! $is_recaptcha_valid ) {
					Inventor_Utilities::show_message( 'danger', __( 'reCAPTCHA is not valid.', 'inventor' ) );
					return;
				}
			}
		}

		$post = get_post( $_POST['post_id'] );
		$email = empty( $_POST['email'] ) ? '' : esc_html( $_POST['email'] );
		$phone = empty( $_POST['phone'] ) ? '' : esc_html( $_POST['phone'] );
		$name = empty( $_POST['name'] ) ? '' : esc_html( $_POST['name'] );
		$date = empty( $_POST['date'] ) ? '' : esc_html( $_POST['date'] );
		$subject = empty( $_POST['subject'] ) ? __( 'Message from enquire form', 'inventor' ) : esc_html( $_POST['subject'] );

		$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $name, $email );

		ob_start();
		include Inventor_Template_Loader::locate( 'mails/inquire' );
		$message = ob_get_contents();
		ob_end_clean();

		$emails = array();

		// Author
		if ( ! empty( $_POST['receive_author'] ) ) {
			$emails[] = get_the_author_meta( 'user_email', $post->post_author );
		}

		// Admin
		if ( ! empty( $_POST['receive_admin'] ) ) {
			$emails[] = get_bloginfo( 'admin_email' );

			// all admins
			$admins = Inventor_Utilities::get_site_admins();

			foreach ( $admins as $admin_login ) {
				$admin = get_user_by( 'login', $admin_login );
				$emails[] = $admin->user_email;
			}
		}

		// Listing email
		if ( ! empty( $_POST['receive_listing_email'] ) ) {
			$email = get_post_meta( $_POST['post_id'], INVENTOR_LISTING_PREFIX . 'email', true );

			if ( ! empty( $email ) ) {
				$emails[] = $email;
			}
		}

		// Default fallback
		if ( empty( $_POST['receive_admin'] ) && empty( $_POST['receive_author'] ) ) {
			$emails[] = get_the_author_meta( 'user_email', $post->post_author );
		}

		$emails = array_unique( $emails );

		foreach ( $emails as $email ) {
			$status = wp_mail( $email, $subject, $message, $headers );
		}

		$success = ! empty( $status ) && 1 == $status;

		do_action(
			'inventor_inquire_message_sent',
			$success, $_POST['post_id'], $subject, $message, $_POST,
			! empty( $_POST['receive_author'] ), ! empty( $_POST['receive_admin'] ), ! empty( $_POST['receive_listing_email'] )
		);

		if ( $success ) {
			Inventor_Utilities::show_message( 'success', __( 'Message has been successfully sent.', 'inventor' ) );
		} else {
			Inventor_Utilities::show_message( 'danger', __( 'Unable to send a message.', 'inventor' ) );
		}

		// redirect to post
		$url = get_permalink( $_POST['post_id'] );
		wp_redirect( $url );
		die();
	}
}

Inventor_Post_Type_Listing::init();
