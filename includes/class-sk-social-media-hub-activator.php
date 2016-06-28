<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/includes
 * @author     Your Name <email@example.com>
 */
class sk_social_media_hub_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

    $timestamp = wp_next_scheduled( 'sksmh_cron' );
    if( $timestamp == false ){
      wp_schedule_event( time(), 'thirty_min', 'sksmh_cron' );
    }   

	}

}
