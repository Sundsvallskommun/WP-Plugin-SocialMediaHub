<?php
/**
 * Plugin Name:       Sundsvalls Kommun - Social Media Hub
 * Plugin URI:        https://github.com/Sundsvallskommun/WP-Plugin-SocialMediaHub
 * Description:       Hämta sociala flöden från Instagram, Facebook och Twitter.
 * Version:           1.0.0
 * Author:            Daniel Pihlström
 * Author URI:        http://cybercom.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sksmh
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sk-social-media-hub-activator.php
 */
function activate_sk_social_media_hub() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sk-social-media-hub-activator.php';
	sk_social_media_hub_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sk-social-media-hub-deactivator.php
 */
function deactivate_sk_social_media_hub() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sk-social-media-hub-deactivator.php';
	sk_social_media_hub_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sk_social_media_hub' );
register_deactivation_hook( __FILE__, 'deactivate_sk_social_media_hub' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sk-social-media-hub.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sk_social_media_hub() {

	$plugin = new SK_Social_Media_Hub();
	$plugin->run();

}

run_sk_social_media_hub();