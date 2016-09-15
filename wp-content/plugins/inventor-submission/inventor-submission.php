<?php

/**
 * Plugin Name: Inventor Submission
 * Version: 1.4.0
 * Description: Adds ability for frontend listing submission.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-submission/
 * Text Domain: inventor-submission
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Submission' ) && class_exists( 'Inventor' ) ) {
    /**
     * Class Inventor_Submission
     *
     * @class Inventor_Submission
     * @package Inventor_Submission
     * @author Pragmatic Mates
     */
    final class Inventor_Submission {
        /**
         * Initialize Inventor_Submission plugin
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
            define( 'INVENTOR_SUBMISSION_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Include classes
         *
         * @access public
         * @return void
         */
        public function includes() {
            require_once INVENTOR_SUBMISSION_DIR . 'includes/class-inventor-submission-customizations.php';
            require_once INVENTOR_SUBMISSION_DIR . 'includes/class-inventor-submission-logic.php';
            require_once INVENTOR_SUBMISSION_DIR . 'includes/class-inventor-submission-shortcodes.php';
        }

        /**
         * Loads localization files
         *
         * @access public
         * @return void
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'inventor-submission', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }
    }

    new Inventor_Submission();
}