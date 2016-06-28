<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 * @author     Your Name <email@example.com>
 */
class sk_social_media_hub_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

    // kill hook for scheduled event
    wp_clear_scheduled_hook( 'sksmh_cron' );

	}

}
