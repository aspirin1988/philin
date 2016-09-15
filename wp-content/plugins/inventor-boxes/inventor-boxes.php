<?php

/**
 * Plugin Name: Inventor Boxes
 * Version: 0.7.0
 * Description: Simple widget for displaying boxes.
 * Author: Pragmatic Mates
 * Author URI: http://inventorwp.com
 * Plugin URI: http://inventorwp.com/plugins/inventor-boxes/
 * Text Domain: inventor-cover
 * Domain Path: /languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! class_exists( 'Inventor_Boxes' ) && class_exists( 'Inventor' ) ) {
	/**
	 * Class Inventor_Boxes
	 *
	 * @class Inventor_Boxes
	 * @package Inventor_Boxes
	 * @author Pragmatic Mates
	 */
	final class Inventor_Boxes {
		/**
		 * Initialize Inventor_Boxes plugin
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
			define( 'INVENTOR_BOXES_DIR', plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Include classes
		 *
		 * @access public
		 * @return void
		 */
		public function includes() {
			require_once INVENTOR_BOXES_DIR . 'includes/class-inventor-boxes-scripts.php';
			require_once INVENTOR_BOXES_DIR . 'includes/class-inventor-boxes-widgets.php';
		}

		/**
		 * Loads localization files
		 *
		 * @access public
		 * @return void
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'inventor-boxes', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}
	}

	new Inventor_Boxes();
}