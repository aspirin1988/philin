<?php

/**
 * Plugin Name: Inventor reCAPTCHA
 * Version: 1.3.0
 * Description: Support for Google reCAPTCHA.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-recaptcha/
 * Text Domain: inventor-recaptcha
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Recaptcha' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Recaptcha
     *
     * @class Inventor_Recaptcha
     * @package Inventor_Recaptcha
     * @author Pragmatic Mates
     */
    final class Inventor_Recaptcha {
        /**
         * Initialize Inventor_Recaptcha plugin
         */
        public function __construct() {
            $this->constants();
            $this->includes();
            $this->load_plugin_textdomain();
        }

        /**
         * Defines constants
         *
         * @access public
         * @return void
         */
        public function constants() {
            define( 'INVENTOR_RECAPTCHA_URL', 'https://www.google.com/recaptcha/api/siteverify' );
            define( 'INVENTOR_RECAPTCHA_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_RECAPTCHA_DIR . 'includes/class-inventor-recaptcha-customizations.php';
            require_once INVENTOR_RECAPTCHA_DIR . 'includes/class-inventor-recaptcha-scripts.php';
            require_once INVENTOR_RECAPTCHA_DIR . 'includes/class-inventor-recaptcha-logic.php';
        }

        /**
         * Loads localization files
         *
         * @access public
         * @return void
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'inventor-recaptcha', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }
    }

    new Inventor_Recaptcha();
}
