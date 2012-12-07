<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SeriouslySimpleSpamBlocker {
	private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $load_spam_blocker;

	public function __construct( $file ) {
		$this->dir = dirname( $file );
		$this->file = $file;
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
		$this->load_spam_blocker = true;

		$this->load_plugin_textdomain();
		add_action( 'init', array( &$this , 'load_localisation' ), 0 );

		add_action( 'wp' , array( &$this , 'check_page_template' ) );

		add_action( 'wp_head' , array( &$this , 'load_scripts' ) , 20 );
		add_action( 'wp_footer' , array( &$this , 'load_content' ) );

		add_action('plugins_loaded', array( &$this , 'check_request' ) );
		add_action('wp_head', array( &$this , 'check_failed' ) , 30);

	}

	/**
	 * Check if current page template is restricted
	 * @return void
	 */
	public function check_page_template() {

		$restricted = get_option('ss_spamblocker_restricted_templates');

		if( $restricted && is_array($restricted) ) {
		
			foreach( $restricted as $k => $template ) {

				foreach( $template as $file ) {

					if( $file == 'default' ) {
						if ( is_page() && ! is_page_template() ) {
							$this->load_spam_blocker = false;
						}
					} elseif( $file == 'posts' ) {
						if ( is_single() ) {
							$this->load_spam_blocker = false;
						}
					} else {
						if( is_page_template( $file) ) {
							$this->load_spam_blocker = false;
						}
					}

				}

			}

		}

	}
	
	/**
	 * Load spam blocker JS & CSS
	 * @return void
	 */
	public function load_scripts() {

		if( $this->load_spam_blocker ) {

			wp_register_script( 'ss_spamblocker' , esc_url( $this->assets_url . 'js/scripts.js' ) , array('jquery') );
			wp_enqueue_script( 'ss_spamblocker' );

			wp_register_style( 'ss_spamblocker' , esc_url( $this->assets_url . 'css/style.css' ) );
			wp_enqueue_style( 'ss_spamblocker' );

		}

	}
	
	/**
	 * Load content of spam blocker form
	 * @return str HTML output
	 */
	public function load_content() {

		if( $this->load_spam_blocker ) {

			// Set defaults
			$display_text = __( 'Drag the image into the box on the right' , 'ss-spamblocker' );
			$display_image = $this->assets_url . 'images/default.png';

			// Get user options
			$custom_text = get_option( 'ss_spamblocker_text' );
			$custom_image = get_option('ss_spamblocker_image');
			
			if( $custom_text && strlen( $custom_text ) > 0 ) {
				$display_text = __( $custom_text , 'ss-spamblocker' );
			}
			if( $custom_image && strlen( $custom_image ) > 0 ) {
				$display_image = $custom_image;
			}

			// Set & output HTML
			$output = '<img id="ss_spamblocker_user_image" src="' . $display_image . '"/>';
			$output .= '<div id="ss_spamblocker_user_text">' . $display_text . ':</div>';

			echo $output;

		}

	}

	/**
	 * Check if request passes spam blocker
	 * @return void
	 */
	public function check_request() {

		if( isset( $_REQUEST['ss_spamblocker_present'] ) && ! isset( $_REQUEST['ss_spamblocker_check'] ) ) {

			$ajaxform = false;
			if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
				$ajaxform = true;
			}

			if( ! $ajaxform ) {
				$referrer = $_SERVER['HTTP_REFERER'];
				$sep = '?';
				if ( strpos( $referrer, '?' ) != false ) { $sep = '&'; }
				wp_redirect( $referrer . $sep . 'sb_failed=1#ss_spamblocker_drag' );
			}

			exit;
		}

	}

	/**
	 * Handle failed check
	 * @return str HTML
	 */
	public function check_failed() {
		if( isset( $_GET['sb_failed'] ) && $_GET['sb_failed'] == 1 ) {
			echo '<style type="text/css">#ss_spamblocker_form #sb_target { border-color: red !important; }</style>';
		}
	}

	/**
	 * Load plugin localisation
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'ss-spamblocker', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin text domain
	 * @return void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'ss-spamblocker';
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

}