<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class sk_social_media_hub_Activator {

	/**
	 * Set scheduled event
	 *
	 * @since 1.0.0
	 */
	public static function activate() {

		wp_schedule_event( time(), 'hourly', 'sksmh_cron' );
		if ( ! wp_next_scheduled( 'sksmh_cron' ) ) {

		}
	}


}
