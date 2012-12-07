<?php
/**
 * Plugin Name: Seriously Simple Spam Blocker
 * Plugin URI: http://www.hughlashbrooke.com/portfolio-item/wordpress-plugin-seriously-simple-spam-blocker/
 * Description: A form validation plugin that replaces unfriendly CAPTCHA fields with a simple drag-and-drop image
 * Version: 1.0.2
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 3.3
 * Tested up to: 3.4
 * 
 * @package Seriously Simple Spam Blocker
 * @author Hugh Lashbrooke
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( 'classes/class-seriously-simple-spam-blocker.php' );

global $ss_spamblocker;
$ss_spamblocker = new SeriouslySimpleSpamBlocker( __FILE__ );

if( is_admin() ) {
	require_once( 'classes/class-seriously-simple-spam-blocker-admin.php' );
	$ss_spamblocker_admin = new SeriouslySimpleSpamBlocker_Admin( __FILE__ );
}