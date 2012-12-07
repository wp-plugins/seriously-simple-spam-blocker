<?php
/**
 * Plugin Name: Seriously Simple Spam Blocker
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: A form validation plugin that uses a simple drag-and-drop image instead of traditional unfriendly CAPTCHA fields.
 * Version: 1.0.0
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