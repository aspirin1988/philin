<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Shortcodes
 *
 * @class Inventor_Shortcodes
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Shortcodes {
	/**
	 * Initialize shortcodes
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'wp', array( __CLASS__, 'check_logout' ) );

		add_shortcode( 'inventor_breadcrumb', array( __CLASS__, 'breadcrumb' ) );
		add_shortcode( 'inventor_logout', array( __CLASS__, 'logout' ) );
		add_shortcode( 'inventor_login', array( __CLASS__, 'login' ) );
		add_shortcode( 'inventor_reset_password', array( __CLASS__, 'reset_password' ) );
		add_shortcode( 'inventor_register', array( __CLASS__, 'register' ) );
		add_shortcode( 'inventor_password', array( __CLASS__, 'password' ) );
		add_shortcode( 'inventor_payment', array( __CLASS__, 'payment' ) );
		add_shortcode( 'inventor_profile', array( __CLASS__, 'profile' ) );
		add_shortcode( 'inventor_transactions', array( __CLASS__, 'transactions' ) );
		add_shortcode( 'inventor_report_form', array( __CLASS__, 'report_form' ) );
		add_shortcode( 'inventor_users', array( __CLASS__, 'users' ) );
	}

	/**
	 * Logout checker
	 *
	 * @access public
	 * @param $wp
	 * @return void
	 */
	public static function check_logout( $wp ) {
		if ( is_admin() ) {
			return;
		}

		$post = get_post();

		if ( is_object( $post ) ) {
			if ( strpos( $post->post_content, '[inventor_logout]' ) !== false ) {
				Inventor_Utilities::show_message( 'success', __( 'You have been successfully logged out.', 'inventor' ) );
				wp_redirect( html_entity_decode( wp_logout_url( home_url( '/' ) ) ) );
				exit();
			}
		}
	}

	/**
	 * Breadcrumb
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function breadcrumb( $atts = array() ) {
		return Inventor_Template_Loader::load( 'misc/breadcrumb' );
	}

	/**
	 * Logout
	 *
	 * @access public
	 * @param $atts|array
	 * @return void
	 */
	public static function logout( $atts = array() ) {}

	/**
	 * Login
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function login( $atts = array() ) {
		return Inventor_Template_Loader::load( 'accounts/login' );
	}

	/**
	 * Reset
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function reset_password( $atts = array() ) {
		return Inventor_Template_Loader::load( 'accounts/reset' );
	}

	/**
	 * Register
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function register( $atts = array() ) {
		return Inventor_Template_Loader::load( 'accounts/register' );
	}

	/**
	 * Payment page
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function payment( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'payment/payment-form' );
	}

	/**
	 * Transactions
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function transactions( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'payment/transactions' );
	}

	/**
	 * Report form
	 *
	 * @access public
	 * @param $atts
	 * @return void
	 */
	public static function report_form( $atts = array() ) {
		$atts = array(
			'listing' => Inventor_Post_Types::get_listing( $_GET['id'] )
		);

		echo Inventor_Template_Loader::load( 'misc/report-form', $atts );
	}

	/**
	 * Users/Authors
	 *
	 * @access public
	 * @param $atts|array
	 * @return string
	 */
	public static function users( $atts = array() ) {
		$params = array(
			'users' => Inventor_Post_Type_User::get_users( 'author', -1, 'posts_count' )
		);

		return Inventor_Template_Loader::load( 'users/user-list', $params );
	}

	/**
	 * Change password
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function password( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		return Inventor_Template_Loader::load( 'accounts/password' );
	}

	/**
	 * Change profile
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function profile( $atts = array() ) {
		if ( ! is_user_logged_in() ) {
			return Inventor_Template_Loader::load( 'misc/not-allowed' );
		}

		$form = cmb2_get_metabox_form( INVENTOR_USER_PREFIX  . 'profile', get_current_user_id(), array(
			'form_format' => '<form action="//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-profile" value="%4$s" class="button"></form>',
			'save_button' => __( 'Save profile', 'inventor' ),
		) );

		return Inventor_Template_Loader::load( 'accounts/profile', array(
			'form' => $form,
		) );
	}
}

Inventor_Shortcodes::init();
