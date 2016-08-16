<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 */
class SK_Social_Media_Hub {

	protected $loader;
	protected $sk_social_media_hub;
	protected $version;
	public static $services_taxonomy = 'social_media_hub_service';
	public static $post_type = 'social_media_hub';


	public function __construct() {

		$this->sk_social_media_hub = 'sk-social-media-hub';
		$this->version             = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-social-media-hub-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-social-media-hub-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sk-social-media-hub-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sk-social-media-hub-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/feeds/class-sk-service-feeder.php';

		$this->loader = new sk_social_media_hub_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
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
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_sk_social_media_hub() {
		return $this->sk_social_media_hub;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    sk_social_media_hub_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Curl processor.
	 *
	 * @since 1.0.0
	 *
	 * @param $url
	 * @param bool $header
	 * @param bool $custom_settings
	 *
	 * @return mixed|string
	 */
	public static function curl_processor( $url, $header = false, $custom_settings = false ) {

		// default settings
		$settings = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2,
		);

		// add custom curl parameters
		if ( ! empty( $custom_settings ) ) {
			foreach ( $custom_settings as $key => $value ) {
				$settings[ $key ] = $value;
			}
		}

		if ( ! empty( $header ) ) {
			$settings[ CURLOPT_HTTPHEADER ] = $header;
		}

		try {

			$curl = curl_init();
			curl_setopt_array( $curl, $settings );

			$result = curl_exec( $curl );
			curl_close( $curl );

			return $result;


		} catch ( Exception $e ) {
			//util::debug( $e );
			return $e->getMessage();
		}
	}

	public static function logger( $message ) {
		if ( empty( $message ) ) {
			return false;
		}

		echo plugin_dir_path( $file );

		//error_log( $message, 3, "/var/tmp/my-errors.log");
	}

}
