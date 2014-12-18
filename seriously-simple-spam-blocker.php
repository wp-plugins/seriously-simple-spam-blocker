<?php
/**
 * Plugin Name: Seriously Simple Spam Blocker
 * Plugin URI: http://gmortensen-ohwp.com/?utm_source=wp_plugin_header&utm_medium=plugin&utm_campaign=sssb
 * Description: A plugin that interacts with the StopForumSpam.com api to protect your website.
 * Version: 2.0
 * Author: Garth Mortensen
 * Author URI: http://gmortensen-ohwp.com/?utm_source=wp_plugin_header&utm_medium=plugin&utm_campaign=home
 * Text Domain: sssb
 * Tested up to: 4.1
 * 
 * @package Seriously Simple Spam Blocker
 * @author Garth Mortensen
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'SSSB_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSSB_BASE_URL', plugin_dir_url( __FILE__ ) );

require_once( SSSB_BASE_DIR . 'spam.php' );
require_once( SSSB_BASE_DIR . 'admin/page.php' );
