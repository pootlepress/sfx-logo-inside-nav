<?php
/**
 * Plugin Name:			SFX Logo Inside Nav
 * Plugin URI:			http://woothemes.com/products/sfx-logo-inside-nav/
 * Description:			A boilerplate plugin for creating Storefront extensions.
 * Version:				1.0.0
 * Author:				WooThemes
 * Author URI:			http://woothemes.com/
 * Requires at least:	4.0.0
 * Tested up to:		4.0.0
 *
 * Text Domain: sfx-logo-inside-nav
 * Domain Path: /languages/
 *
 * @package SFX_Logo_Inside_Nav_
 * @category Core
 * @author James Koster
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Sold On Woo - Start
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'FILE_ID', 'PRODUCT_ID' );
// Sold On Woo - End

/**
 * Returns the main instance of SFX_Logo_Inside_Nav_ to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object SFX_Logo_Inside_Nav_
 */
function SFX_Logo_Inside_Nav_() {
	return SFX_Logo_Inside_Nav_::instance();
} // End SFX_Logo_Inside_Nav_()

SFX_Logo_Inside_Nav_();

/**
 * Main SFX_Logo_Inside_Nav_ Class
 *
 * @class SFX_Logo_Inside_Nav_
 * @version	1.0.0
 * @since 1.0.0
 * @package	SFX_Logo_Inside_Nav_
 */
final class SFX_Logo_Inside_Nav_ {
	/**
	 * SFX_Logo_Inside_Nav_ The single instance of SFX_Logo_Inside_Nav_.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'sfx-logo-inside-nav';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'slin_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'slin_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'slin_plugin_links' ) );
	}

	/**
	 * Main SFX_Logo_Inside_Nav_ Instance
	 *
	 * Ensures only one instance of SFX_Logo_Inside_Nav_ is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see SFX_Logo_Inside_Nav_()
	 * @return Main SFX_Logo_Inside_Nav_ instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function slin_load_plugin_textdomain() {
		load_plugin_textdomain( 'sfx-logo-inside-nav', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links
	 *
	 * @since  1.0.0
	 */
	public function slin_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="http://support.woothemes.com/">' . __( 'Support', 'sfx-logo-inside-nav' ) . '</a>',
			'<a href="http://docs.woothemes.com/document/sfx-logo-inside-nav/">' . __( 'Docs', 'sfx-logo-inside-nav' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		// get theme customizer url
		$url = admin_url() . 'customize.php?';
		$url .= 'url=' . urlencode( site_url() . '?storefront-customizer=true' ) ;
		$url .= '&return=' . urlencode( admin_url() . 'plugins.php' );
		$url .= '&storefront-customizer=true';

		$notices 		= get_option( 'slin_activation_notice', array() );
		$notices[]		= sprintf( __( '%sThanks for installing the SFX Logo Inside Nav extension. To get started, visit the %sCustomizer%s.%s %sOpen the Customizer%s', 'sfx-logo-inside-nav' ), '<p>', '<a href="' . esc_url( $url ) . '">', '</a>', '</p>', '<p><a href="' . esc_url( $url ) . '" class="button button-primary">', '</a></p>' );

		update_option( 'slin_activation_notice', $notices );
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Setup all the things.
	 * Only executes if Storefront or a child theme using Storefront as a parent is active and the extension specific filter returns true.
	 * Child themes can disable this extension using the sfx_logo_inside_nav_enabled filter
	 * @return void
	 */
	public function slin_setup() {
		$theme = wp_get_theme();

		if ( 'Storefront' == $theme->name || 'storefront' == $theme->template && apply_filters( 'sfx_logo_inside_nav_supported', true ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'slin_styles' ), 999 );
			add_action( 'customize_register', array( $this, 'slin_customize_register' ) );
			add_action( 'customize_preview_init', array( $this, 'slin_customize_preview_js' ) );
			add_filter( 'body_class', array( $this, 'slin_body_class' ) );
			add_action( 'wp', array( $this, 'slin_layout_adjustments' ), 999 );
			add_action( 'admin_notices', array( $this, 'slin_customizer_notice' ) );

			// Hide the 'More' section in the customizer
			add_filter( 'storefront_customizer_more', '__return_false' );
		}
	}

	/**
	 * Admin notice
	 * Checks the notice setup in install(). If it exists display it then delete the option so it's not displayed again.
	 * @since   1.0.0
	 * @return  void
	 */
	public function slin_customizer_notice() {
		$notices = get_option( 'slin_activation_notice' );

		if ( $notices = get_option( 'slin_activation_notice' ) ) {

			foreach ( $notices as $notice ) {
				echo '<div class="updated">' . $notice . '</div>';
			}

			delete_option( 'slin_activation_notice' );
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function slin_customize_register( $wp_customize ) {

		/**
		 * Custom controls
		 * Load custom control classes
		 */
		require_once dirname( __FILE__ ) . '/includes/class-sfx-logo-inside-nav-images-control.php';

		/**
		 * Modify existing controls
		 */
		// Note: If you want to modiy existing controls, do it this way. You can set defaults, change the transport, etc.
		//$wp_customize->get_setting( 'storefront_header_background_color' )->transport = 'refresh';

		/**
	     * Add a new section
	     */
        $wp_customize->add_section( 'slin_section' , array(
		    'title'      	=> __( 'SFX Logo Inside Nav', 'storefront-extention-boilerplate' ),
		    'description' 	=> __( 'Add a description, if you want to!', 'storefront-extention-boilerplate' ),
		    'priority'   	=> 55,
		) );

		/**
		 * Image selector radios
		 * See class-control-images.php
		 */
		$wp_customize->add_setting( 'slin_image', array(
			'default'    		=> 'option-1',
			'sanitize_callback'	=> 'esc_attr'
		) );

		$wp_customize->add_control( new SFX_Logo_Inside_Nav__Images_Control( $wp_customize, 'slin_image', array(
			'label'    => __( 'Image selector', 'storefront' ),
			'section'  => 'slin_section',
			'settings' => 'slin_image',
			'priority' => 10,
		) ) );

		/**
		 * Add a divider.
		 * Type can be set to 'text' or 'heading' to display a title or description.
		 */
		if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
			$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'slin_divider', array(
				'section'  	=> 'slin_section',
				'type'		=> 'divider',
				'priority' 	=> 15,
			) ) );
		}

		/**
		 * Checkbox
		 */
		$wp_customize->add_setting( 'slin_checkbox', array(
			'default'			=> apply_filters( 'slin_checkbox_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'slin_checkbox', array(
			'label'			=> __( 'Checkbox', 'sfx-logo-inside-nav' ),
			'description'	=> __( 'Here\'s a simple boolean checkbox option. In this instance it toggles wrapping the main navigation in a wrapper div.', 'sfx-logo-inside-nav' ),
			'section'		=> 'slin_section',
			'settings'		=> 'slin_checkbox',
			'type'			=> 'checkbox',
			'priority'		=> 20,
		) ) );

		/**
		 * Color picker
		 */
		$wp_customize->add_setting( 'slin_color_picker', array(
			'default'			=> apply_filters( 'slin_color_picker_default', '#ff0000' ),
			'sanitize_callback'	=> 'sanitize_hex_color',
			'transport'			=> 'postMessage', // Refreshes instantly via js. See customizer.js. (default = refresh).
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'slin_color_picker', array(
			'label'			=> __( 'Color picker', 'sfx-logo-inside-nav' ),
			'description'	=> __( 'Here\'s an example color picker. In this instance it applies a background color to headings', 'sfx-logo-inside-nav' ),
			'section'		=> 'slin_section',
			'settings'		=> 'slin_color_picker',
			'priority'		=> 30,
		) ) );

		/**
		 * Select
		 */
		$wp_customize->add_setting( 'slin_select', array(
			'default' 			=> 'default',
			'sanitize_callback'	=> 'storefront_sanitize_choices',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'slin_select', array(
			'label'			=> __( 'Select', 'sfx-logo-inside-nav' ),
			'description'	=> __( 'Make a selection!', 'sfx-logo-inside-nav' ),
			'section'		=> 'slin_section',
			'settings'		=> 'slin_select',
			'type'			=> 'select', // To add a radio control, switch this to 'radio'.
			'priority'		=> 40,
			'choices'		=> array(
				'default'		=> 'Default',
				'non-default'	=> 'Non-default',
			),
		) ) );
	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function slin_styles() {
		wp_enqueue_style( 'slin-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );

		$heading_background_color 	= storefront_sanitize_hex_color( get_theme_mod( 'slin_color_picker', apply_filters( 'slin_default_heading_background_color', '#ff0000' ) ) );

		$slin_style = '
		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			background-color: ' . $heading_background_color . ';
		}';

		wp_add_inline_style( 'slin-styles', $slin_style );
	}

	/**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 *
	 * @since  1.0.0
	 */
	public function slin_customize_preview_js() {
		wp_enqueue_script( 'slin-customizer', plugins_url( '/assets/js/customizer.min.js', __FILE__ ), array( 'customize-preview' ), '1.1', true );
	}

	/**
	 * SFX Logo Inside Nav Body Class
	 * Adds a class based on the extension name and any relevant settings.
	 */
	public function slin_body_class( $classes ) {
		$classes[] = 'sfx-logo-inside-nav-active';

		return $classes;
	}

	/**
	 * Layout
	 * Adjusts the default Storefront layout when the plugin is active
	 */
	public function slin_layout_adjustments() {
		$slin_checkbox 	= get_theme_mod( 'slin_checkbox', apply_filters( 'slin_checkbox_default', false ) );

		if ( true == $slin_checkbox ) {
			add_action( 'storefront_header', array( $this, 'slin_primary_navigation_wrapper' ), 45 );
			add_action( 'storefront_header', array( $this, 'slin_primary_navigation_wrapper_close' ), 65 );
		}
	}

	/**
	 * Primary navigation wrapper
	 * @return void
	 */
	function slin_primary_navigation_wrapper() {
		echo '<section class="slin-primary-navigation">';
	}

	/**
	 * Primary navigation wrapper close
	 * @return void
	 */
	function slin_primary_navigation_wrapper_close() {
		echo '</section>';
	}

} // End Class
