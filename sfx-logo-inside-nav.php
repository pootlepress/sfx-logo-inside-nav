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
 * @package SFX_Logo_Inside_Nav
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
 * Returns the main instance of SFX_Logo_Inside_Nav to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object SFX_Logo_Inside_Nav
 */
function SFX_Logo_Inside_Nav() {
	return SFX_Logo_Inside_Nav::instance();
} // End SFX_Logo_Inside_Nav()

SFX_Logo_Inside_Nav();

/**
 * Main SFX_Logo_Inside_Nav Class
 *
 * @class SFX_Logo_Inside_Nav
 * @version	1.0.0
 * @since 1.0.0
 * @package	SFX_Logo_Inside_Nav
 */
final class SFX_Logo_Inside_Nav {
	/**
	 * SFX_Logo_Inside_Nav The single instance of SFX_Logo_Inside_Nav.
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
	 * Main SFX_Logo_Inside_Nav Instance
	 *
	 * Ensures only one instance of SFX_Logo_Inside_Nav is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see SFX_Logo_Inside_Nav()
	 * @return Main SFX_Logo_Inside_Nav instance
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
			add_action( 'admin_notices', array( $this, 'slin_customizer_notice' ) );

			//Stuff that works
			add_filter('wp_nav_menu_items', array( $this, 'slin_logo_in_nav' ), 10, 2 );
			$slin_option_true = get_theme_mod('slin_activation', false);
			if($slin_option_true){
				remove_action( 'storefront_header', 'storefront_site_branding' , 20 );
			}


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

		$wp_customize->add_setting( 'slin_activation', array(
			'default'			=> apply_filters( 'slin_activation_default', false ),
			'sanitize_callback'	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'slin_activation', array(
			'label'			=> __( 'Center logo inside primary navigation', 'sfx-logo-inside-nav' ),
			'section'		=> 'header_image',
			'settings'		=> 'slin_activation',
			'type'			=> 'checkbox',
			'priority'		=> 99,
		) ) );

	}

	/**
	 * Enqueue CSS and custom styles.
	 * @since   1.0.0
	 * @return  void
	 */
	public function slin_styles() {
		wp_enqueue_style( 'slin-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
		$css = '';
		
		//Hide header stuff if slin activated
		$slin_option_true = get_theme_mod('slin_activation', false);
		if($slin_option_true){
			$css = ''
			  .'.site-header .site-branding, .site-header .site-logo-anchor, .site-header .site-logo-link{'
					.'display:none;'
			  .'}'
			  .'.woocommerce-active .site-header .main-navigation, .site-header .main-navigation{'
					.'width:100%;text-align:center;'
			  .'}'
			  .'.slin-logo-menu-item img{'
					.'display: block; position: absolute; top: -999%; bottom: -999%; left: -999%; right: -999%; margin: auto;max-height:160px;'
			  .'}'
			  .'.slin-logo-menu-item span{'
					.'font-size:2em;'
			  .'}'
			  .'.main-navigation ul.nav-menu > .slin-logo-menu-item.slin-logo-text a{'
					.'  padding: 0 1em; display: block; margin: -16px 0 0 0;'
			  .'}'
			  .'.slin-logo-menu-item.slin-logo-image{'
					.'width:20%;font-size:0;'
			  .'}';
		}

		wp_add_inline_style( 'slin-styles', $css );
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

	function slin_logo_in_nav( $items, $args ){
		$slin_option_true = get_theme_mod('slin_activation', false);
		if(!$slin_option_true)			return $items;

		//Fall back values
		$li_class = 'slin-logo-text';
		$logoHTML = '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home"><span>' . get_bloginfo( 'name' ) . '</span></a>';

		//For Jetpack by WordPress.com
		if ( function_exists( 'jetpack_has_site_logo' ) && jetpack_has_site_logo() ) {
			$logo = site_logo()->logo;
			$li_class = 'slin-logo-image';
			$logoHTML = ''
				. '<a class="slin-logo-anchor" href="' . esc_url( home_url( '/' ) ) . '" style="font-size:0px;" rel="home">'
				. get_bloginfo( 'name' )
				. '<img src="'. $logo['url']. '">'
				. '</a>'
			  . '';
		}

		//For Storefront Site Logo over-rides Jetpack
		$check = get_theme_mod( 'woa_sf_enable_logo', 'title_tagline' );
		$logo = get_theme_mod( 'woa_sf_logo', null );
		if( ( ( $check == 'logo_img' ) && $logo ) ) {
			if( is_ssl() ) {
				$logo = str_replace( 'http://', 'https://', $logo );
			}
			$li_class = 'slin-logo-image';
			$logoHTML = ''
				. '<a class="slin-logo-anchor" href="' . esc_url( home_url( '/' ) ) . '" style="font-size:0px;" rel="home">'
				. get_bloginfo( 'name' )
				. '<img src="'. $logo. '">'
				. '</a>'
			  . '';
		}
		
		if($args->theme_location != 'primary')return $items;
		//Init return value
		$html = '';
		//Convert items html into SimpleXML Object
		$items = new SimpleXMLElement( '<ul>' . $items . '</ul>' );
		//Num of top level menu items
		$num_items = count($items);
		
		$i = 0;
		$logo_done = false;
		foreach( $items as $item ){
			$i++;
			//If logo not done and $i > half the number of items
			if( !$logo_done && $i > ($num_items/2)){
				//Attach logo
				$html .= '<li class=" ' . $li_class . ' slin-logo-menu-item">'.$logoHTML.'</li>';
				//Set logo done to true
				$logo_done = TRUE;
			};
			//Attach the menu item
			$html .= $item->asXML();
		}
		return $html;
	}

} // End Class
