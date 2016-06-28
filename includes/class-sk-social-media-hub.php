<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 * @author     Your Name <email@example.com>
 */
class SK_Social_Media_Hub {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      sk_social_media_hub_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $sk_social_media_hub    The string used to uniquely identify this plugin.
	 */
	protected $sk_social_media_hub;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	

  public static $services_taxonomy = 'social_media_hub_service';
  public static $post_type = 'social_media_hub';



	public function __construct() {

		$this->sk_social_media_hub = 'sk-social-media-hub';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - sk_social_media_hub_Loader. Orchestrates the hooks of the plugin.
	 * - sk_social_media_hub_i18n. Defines internationalization functionality.
	 * - sk_social_media_hub_Admin. Defines all hooks for the admin area.
	 * - sk_social_media_hub_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-social-media-hub-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-social-media-hub-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sk-social-media-hub-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sk-social-media-hub-public.php';


		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/feeds/class-sk-service-feeder.php';


		$this->loader = new sk_social_media_hub_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the sk_social_media_hub_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new sk_social_media_hub_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new SK_Social_Media_Hub_Admin( $this->get_sk_social_media_hub(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin, 'register_post_type', 10 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_taxonomy', 10 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		//$this->loader->add_action( 'admin_init', $plugin_admin, 'token_request' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_page', 20 );
		$this->loader->add_action( 'init', $plugin_admin, 'post_request' );
		
		//$this->loader->add_action( 'admin_print_scripts', $plugin_admin, 'admin_inline_script', 99 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'wp_ajax_get_twitter_token', $plugin_admin, 'get_twitter_token' );
		$this->loader->add_action( 'wp_ajax_get_facebook_token', $plugin_admin, 'get_facebook_token' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SK_Social_Media_Hub_Public( $this->get_sk_social_media_hub(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'add_shortcode' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_sk_social_media_hub() {
		return $this->sk_social_media_hub;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    sk_social_media_hub_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

  public static function curl_processor( $url, $header = false, $custom_settings = false ){
    
    // default settings
    $settings = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => 2,
    );

    // add custom curl parameters
    if( !empty( $custom_settings ) ){
    	foreach( $custom_settings as $key => $value ){
    		$settings[$key] = $value;
    	}
    }

    if( !empty( $header ))
      $settings[CURLOPT_HTTPHEADER] = $header;

    try {
   
      $curl = curl_init();
      curl_setopt_array($curl, $settings );

      $result = curl_exec( $curl );
      curl_close($curl);   
      
      return $result;  
      

    } catch(Exception $e) {
      //util::debug( $e );
      return $e->getMessage();
    }
  }

}
