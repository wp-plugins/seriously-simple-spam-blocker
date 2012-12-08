<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeriouslySimpleSpamBlocker_Admin {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;

	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );

		add_action( 'admin_init' , array( &$this , 'register_settings' ) );

		add_action( 'admin_menu' , array( &$this , 'add_menu_item' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( &$this , 'add_settings_link' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ), 10 );

	}

	/**
	 * Load JS & CSS from admin page
	 * @return void
	 */
	public function enqueue_admin_scripts () {

		// Admin CSS
		wp_register_style( 'ss_spamblocker-admin', esc_url( $this->assets_url . 'css/admin.css' ), array(), '1.0.0' );
		wp_enqueue_style( 'ss_spamblocker-admin' );

		// Admin JS
		wp_register_script( 'ss_spamblocker-admin', esc_url( $this->assets_url . 'js/admin.js' ), array( 'jquery' , 'media-upload' , 'thickbox' ), '1.0.0' );

		// JS & CSS for media uploader
		wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'ss_spamblocker-admin' );

	}
	/**
	 * Add spam blocker to settings menu
	 * @return void
	 */
	public function add_menu_item( ) {
		add_options_page('Seriously Simple Spam Blocker', 'Spam Blocker', 'manage_options', 'ss_spamblocker', array( &$this , 'settings_page' ) );
	}

	/**
	 * Add settings link to plugins page
	 * @param array $links Existing links
	 * @return Modified links
	 */
	public function add_settings_link( $links ) {
		if( is_admin() ) {
			$settings_link = '<a href="options-general.php?page=ss_spamblocker">Settings</a>';
	  		array_push( $links, $settings_link );
	  		return $links;
	  	}
	}

	/**
	 * Register settings fields
	 * @return void
	 */
	public function register_settings() {

		// Add settings section
		add_settings_section('main_settings', __( 'A few simple settings to customise your spam blocker:' , 'ss-spamblocker' ), array( &$this , 'main_settings' ), 'ss_spamblocker');

		// Add settings fields
		add_settings_field('ss_spamblocker_text', __( 'Spam blocker text:' , 'ss-spamblocker' ), array( &$this , 'text_field' ), 'ss_spamblocker', 'main_settings');
		add_settings_field('ss_spamblocker_image', __( 'Spam blocker image:' , 'ss-spamblocker' ), array( &$this , 'image_field' ), 'ss_spamblocker', 'main_settings');
		add_settings_field('ss_spamblocker_restricted_templates', __( 'Restricted templates:' , 'ss-spamblocker' ), array( &$this , 'templates_field' ), 'ss_spamblocker', 'main_settings');
		add_settings_field('ss_spamblocker_restricted_elements', __( 'Restricted elements:' , 'ss-spamblocker' ), array( &$this , 'elements_field' ), 'ss_spamblocker', 'main_settings');
		
		// Register settings fields
		register_setting('ss_spamblocker', 'ss_spamblocker_text');
		register_setting('ss_spamblocker', 'ss_spamblocker_image');
		register_setting('ss_spamblocker', 'ss_spamblocker_restricted_templates' );
		register_setting('ss_spamblocker', 'ss_spamblocker_restricted_elements' );

	}

	/**
	 * Header for settings
	 * @return void
	 */
	public function main_settings() {}

	/**
	 * Output for spam blocker text field
	 * @return str HTML output
	 */
	public function text_field() {

		$option = get_option('ss_spamblocker_text');
		$text = __( 'Drag the image into the box on the right' , 'ss-spamblocker' );

		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$text = __( $option , 'ss-spamblocker' );
		}

		echo '<input id="ss_spamblocker_text" type="text" size="50" name="ss_spamblocker_text" value="' . $text . '"/><br/>
				<label for="ss_spamblocker_text"><span class="description">' . __( 'Text to be displayed to users above the spam blocker image.' , 'ss-spamblocker' ) . '</span></label>';
	}

	/**
	 * Output for spam blocker image field
	 * @return str HTML output
	 */
	public function image_field() {

		$option = get_option('ss_spamblocker_image');
		$image = $this->assets_url . 'images/default.png';
		$default = $image;

		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$image = $option;
		}

		echo '<img id="ss_spamblocker_image_preview" src="' . $image . '" /><br/>
			  <input type="hidden" id="sb_default_image" value="' . $default . '" />
			  <input id="sb_upload_file" type="button" class="button" value="'. __( 'Upload new image' , 'ss-spamblocker' ) . '" />
			  <input id="sb_reset_image" type="button" class="button" value="'. __( 'Reset to default' , 'ss-spamblocker' ) . '" />
			  <input id="ss_spamblocker_image" type="hidden" size="50" name="ss_spamblocker_image" value="' . $image . '"/>';
	}

	/**
	 * Output for spam blocker templates field
	 * @return str HTML output
	 */
	public function templates_field() {

		// Get existing option
		$option = get_option('ss_spamblocker_restricted_templates');

		// Get page templates
		$templates = get_page_templates();

		// Add built-in templates
		$extras = array(
			'Default Template' => 'default',
			'Single Posts' => 'posts'
		);

		// Merge arrays
		$templates = array_merge( $extras , $templates );

		// Build up select options
		$select_options = '';
		foreach( $templates as $name => $file ) {
			$selected = '';
			if( $option && is_array( $option ) ) {
				if( in_array( $file , $option['templates'] ) ) {
					$selected = "selected='selected'";
				}
			}
			$select_options .= '<option value="' . $file . '" ' . $selected . '>' . $name . '</option>';
		}

		if( $select_options != '' ) {
			echo '<select id="ss_spamblocker_restricted_templates" multiple="multiple" name="ss_spamblocker_restricted_templates[templates][]">
					' . $select_options . '
				  </select>
				  <br/>
				  <label for="ss_spamblocker_text"><span class="description">' . __( 'Select the page templates on which you do NOT want the spam blocker to appear.' , 'ss-spamblocker' ) . '</span></label>';
		}

	}

	/**
	 * Output for spam blocker text field
	 * @return str HTML output
	 */
	public function elements_field() {

		$option = get_option('ss_spamblocker_restricted_elements');
		$text = '';

		if( $option && strlen( $option ) > 0 && $option != '' ) {
			$text = __( $option , 'ss-spamblocker' );
		}

		echo '<input id="ss_spamblocker_restricted_elements" type="text" size="50" name="ss_spamblocker_restricted_elements" value="' . $text . '"/><br/>
				<label for="ss_spamblocker_restricted_elements"><span class="description">' . __( 'ADVANCED USERS ONLY!<br/>Supply a comma-separated list of submit button element names that you would like to hide the spam blocker from.<br/>EXAMPLE: #form_id submit.button_class, #another_form submit' , 'ss-spamblocker' ) . '</span></label>';
	}

	/**
	 * Load settings page
	 * @return str HTML output
	 */
	public function settings_page() {

		echo '<div class="wrap">
				<div class="icon32" id="ss_spamblocker-icon"><br/></div>
				<h2>Seriously Simple Spam Blocker</h2>
				<form method="post" action="options.php" enctype="multipart/form-data">';

				settings_fields( 'ss_spamblocker' );
				do_settings_sections( 'ss_spamblocker' );

			  echo '<p class="submit">
						<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'ss-spamblocker' ) ) . '" />
					</p>
				</form>
			  </div>';

	}

}