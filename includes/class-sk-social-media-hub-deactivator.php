<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class sk_social_media_hub_Deactivator {

	/**
	 * Remove scheduled event.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

        // kill hook for scheduled event
        wp_clear_scheduled_hook( 'sksmh_cron' );

	}

}
