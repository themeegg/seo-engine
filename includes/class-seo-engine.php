<?php
/**
 * SEO_Engine setup
 *
 * @author   ThemeEgg
 * @category API
 * @package  SEO_Engine
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main SEO_Engine Class.
 *
 * @class SEO_Engine
 * @version    3.2.0
 */
final class SEO_Engine {

	/**
	 * SEO_Engine version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var SEO_Engine
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main SEO_Engine Instance.
	 *
	 * Ensures only one instance of SEO_Engine is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see sen()
	 * @return SEO_Engine - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'seo-engine' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'seo-engine' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key name.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {

	}

	/**
	 * SEO_Engine Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'seo_engine_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks() {
		//register_activation_hook( SEN_PLUGIN_FILE, array( 'SEN_Install', 'install' ) );
		//register_shutdown_function( array( $this, 'log_errors' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		//add_action( 'init', array( 'SEN_Shortcodes', 'init' ) );
	}

	/**
	 * Define SEN Constants.
	 */
	private function define_constants() {
		$this->define( 'SEN_ABSPATH', dirname( SEN_PLUGIN_FILE ) . '/' );
		$this->define( 'SEN_PLUGIN_BASENAME', plugin_basename( SEN_PLUGIN_FILE ) );
		$this->define( 'SEN_PLUGIN_URL', trailingslashit( plugins_url( null, SEN_PLUGIN_FILE ) ) );
		$this->define( 'SEN_VERSION', $this->version );
		$this->define( 'SEO_ENGINE_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once( SEN_ABSPATH . 'includes/class-sen-autoloader.php' );
		include_once( SEN_ABSPATH . 'includes/engines/class-sen-engine-images.php' );
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {

	}

	/**
	 * Function used to Init SEO_Engine Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
	}

	/**
	 * Init SEO_Engine when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_seo_engine_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'seo_engine_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/seo-engine/seo-engine-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/seo-engine-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'seo-engine' );

		unload_textdomain( 'seo-engine' );
		load_textdomain( 'seo-engine', WP_LANG_DIR . '/seo-engine/seo-engine-' . $locale . '.mo' );
		load_plugin_textdomain( 'seo-engine', false, plugin_basename( dirname( SEN_PLUGIN_FILE ) ) . '/i18n/languages' );
	}

	/**
	 * Ensure theme and server variable compatibility and setup image sizes.
	 */
	public function setup_environment() {
		/* @deprecated 2.2 Use WC()->template_path() instead. */
		$this->define( 'SEN_TEMPLATE_PATH', $this->template_path() );

	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', SEN_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( SEN_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'seo_engine_template_path', 'seo-engine/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}
}
