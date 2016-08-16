<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class SK_Social_Media_Hub_Admin {

	private $sk_social_media_hub; // ID of this plugin
	private $version;
	private $options;
	private $instagram_access_token;
	private $services;
	private static $post_type = 'social_media_hub';
	private $instagram_redirect_url = 'http://utveckling.sundsvall.se/instagram-auth.php';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $sk_social_media_hub The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $sk_social_media_hub, $version ) {

		$this->sk_social_media_hub = $sk_social_media_hub;
		$this->version             = $version;

		$this->set_terms();

	}

	/**
	 * Setting the terms for taxonomy social_media_hub_service.
	 *
	 * ON RENAME: Enter the display name for the old name
	 * in previous name to keep connection between posts and terms.
	 *
	 */
	private function set_terms() {

		$services = array(
			array(
				'name' => 'Instagram',
			),
			array(
				'name' => 'Facebook',
			),
			array(
				'name' => 'Twitter'
			)
		);

		$this->services = $services;

	}


	/**
	 * Check to see that box-type taxonomy is synced to default box types.
	 *
	 *
	 * @return none
	 */
	/*
	public function sync_box_types(){
	  $taxonomy = 'social_media_hub_service';
	  $services = apply_filters('sksmh_services', $this->services );

	  $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
	  $current_services = array();

	  foreach( $terms as $term ) {
		$current_services[] = $term->name;
	  }

	  $diff = array_diff( $defaults, $current_box_types );
	  echo "sync_box_types function...";
	  die();
	  // Compare arrays to get terms to insert or remove.
	  $inserts = array_diff( $defaults, $current_box_types );
	  $removes = array_diff( $current_box_types, $defaults );


	  if( !empty( $removes ) ){
		foreach ( $removes as $remove ) {
		  $term = get_term_by( 'name', $remove, $taxonomy );
		  wp_delete_term( $term->term_id, $taxonomy );
		}
	  }

	  if( !empty( $inserts ) ){
		foreach ( $inserts as $insert ) {
		  wp_insert_term( $insert, $taxonomy );
		}
	  }

	}
  */


	/**
	 * Register post type for storing the feeds
	 *
	 * @since    1.0.0
	 *
	 * @return [type]     [description]
	 */
	public function register_post_type() {

		register_post_type( 'social_media_hub',
			array(
				'labels'          => array(
					'name'          => __( 'Sociala flöden', 'sksmh' ),
					'singular_name' => __( 'Sociala flöden', 'sksmh' ),
					'add_new'       => __( 'Nytt flöde', 'sksmh' ),
					'add_new_item'  => __( 'Skapa nytt flöde', 'sksmh' ),
					'edit_item'     => __( 'Redigera flöde', 'sksmh' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_position'   => 6,
				'menu_icon'       => 'dashicons-share',
				'capability_type' => 'post',
				'hierarchical'    => false,
				'rewrite'         => array( 'slug' => 'sociala-floden', 'with_front' => false ),
				'supports'        => array( 'title', 'editor' )
			)
		);
	}

	/**
	 * Register taxonomies.
	 *
	 *
	 * @return none
	 */
	public function register_taxonomy() {


		// register custom category
		$labels = array(
			'name'          => _x( 'Tjänst', 'sksmh' ),
			'singular_name' => _x( 'Tjänst', 'sksmh' ),
			'search_items'  => __( 'Sök tjänster', 'sksmh' ),
			'all_items'     => __( 'Alla tjänster', 'sksmh' ),
			'parent_item'   => __( 'Föräldratjänst', 'sksmh' ),
			'edit_item'     => __( 'Ändra tjänst', 'sksmh' ),
			'update_item'   => __( 'Uppdatera tjänst', 'sksmh' ),
			'add_new_item'  => __( 'Lägg till ny tjänst', 'sksmh' ),
			'new_item_name' => __( 'Ny tjänst', 'sksmh' ),
			'menu_name'     => __( 'Tjänster', 'sksmh' )
		);

		$args = array(
			'labels'       => $labels,
			'rewrite'      => array( 'slug' => 'feed' ),
			'hierarchical' => true,
		);

		register_taxonomy( 'social_media_hub_service', 'social_media_hub', $args );


		// register custom category
		$labels = array(
			'name'          => _x( 'Användare', 'sksmh' ),
			'singular_name' => _x( 'Användare', 'sksmh' ),
			'search_items'  => __( 'Sök användare', 'sksmh' ),
			'all_items'     => __( 'Alla användare', 'sksmh' ),
			'parent_item'   => __( 'Föräldraanvändare', 'sksmh' ),
			'edit_item'     => __( 'Ändra användare', 'sksmh' ),
			'update_item'   => __( 'Uppdatera användare', 'sksmh' ),
			'add_new_item'  => __( 'Lägg till ny användare', 'sksmh' ),
			'new_item_name' => __( 'Ny användare', 'sksmh' ),
			'menu_name'     => __( 'Användare', 'sksmh' )
		);

		$args = array(
			'labels'       => $labels,
			'rewrite'      => array( 'slug' => 'user' ),
			'hierarchical' => true,
		);

		register_taxonomy( 'social_media_hub_user', 'social_media_hub', $args );

	}


	/**
	 * Adding the plugin page to settings
	 *
	 */
	public function add_plugin_page() {

		add_submenu_page(
			'general-settings',
			__( 'Sociala flöden', 'sksmh' ),
			__( 'Sociala flöden', 'sksmh' ),
			'edit_pages',
			$this->sk_social_media_hub,
			array( $this, 'create_editor_page' )
		);

	}

	public function update_options() {
		$this->options = get_option( 'sk_social_media_hub' );
		$currents      = $this->options;

		// nonce checker
		check_admin_referer( 'sksmh_nonce' );

		//update_option( $option, $newvalue );
		$data = $_POST['sk_social_media_hub'];

		if ( isset( $_POST['tab'] ) && $_POST['tab'] === 'advanced' ) {

			foreach ( $data as $key => $value ) {
				$currents[ $key ]['access_token'] = $value['access_token'];

				if ( $key === 'instagram' ) {
					$currents[ $key ]['client_id'] = $value['client_id'];
				}

				if ( $key === 'facebook' ) {
					$currents[ $key ]['app_id']     = $value['app_id'];
					$currents[ $key ]['app_secret'] = $value['app_secret'];
				}

				if ( $key === 'twitter' ) {
					$currents[ $key ]['consumer_key']    = $value['consumer_key'];
					$currents[ $key ]['consumer_secret'] = $value['consumer_secret'];
				}

			}

		} else {
			foreach ( $data as $key => $value ) {
				$currents[ $key ]['user_id_feed'] = $value['user_id_feed'];

				if ( isset( $value['activate'] ) && $value['activate'] === 'on' ) {
					$currents[ $key ]['activate'] = $value['activate'];
				} else {
					unset( $currents[ $key ]['activate'] );
				}
			}
		}

		update_option( 'sk_social_media_hub', $currents );

	}

	/**
	 * Collect post request and route to method.
	 *
	 *
	 */
	public function post_request() {
		if ( isset( $_POST['update_sksmh'] ) ) {
			$this->update_options();
		}

	}

	/**
	 * Setting page for editors who don´t have manage_options capability.
	 *
	 *
	 * @return [type]
	 */
	public function create_editor_page() {
		$this->options = get_option( 'sk_social_media_hub' );
		?>
		<div class="wrap social-media-hub-admin">


			<?php $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'start'; ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=sk-social-media-hub&amp;tab=start"
				   class="nav-tab <?php echo $active_tab == 'start' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Inställningar', 'sksmh' ) ?></a>
				<a href="?page=sk-social-media-hub&amp;tab=display"
				   class="nav-tab <?php echo $active_tab == 'display' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Visning', 'sksmh' ) ?></a>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<a href="?page=sk-social-media-hub&amp;tab=advanced"
					   class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Avancerat', 'sksmh' ) ?></a>
				<?php endif; ?>
			</h2>

			<?php if ( $active_tab == 'start' ) : ?>
				<form method="post">
					<h2><?php _e( 'Instagram', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php _e( 'Aktivera', 'sksmh' ); ?></th>
							<td><?php $this->instagram_activate_callback() ?></td>
						</tr>
						<tr>
							<th><?php _e( 'AnvändarID', 'sksmh' ); ?></th>
							<td><?php $this->instagram_user_id_feed(); ?></td>
						</tr>
						</tbody>
					</table>
					<hr/>

					<h2><?php _e( 'Facebook', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php _e( 'Aktivera', 'sksmh' ); ?></th>
							<td><?php $this->facebook_activate_callback() ?></td>
						</tr>
						<tr>
							<th><?php _e( 'AnvändarID', 'sksmh' ); ?></th>
							<td><?php $this->facebook_user_id_feed(); ?></td>
						</tr>
						</tbody>
					</table>
					<hr/>

					<h2><?php _e( 'Twitter', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php _e( 'Aktivera', 'sksmh' ); ?></th>
							<td><?php $this->twitter_activate_callback() ?></td>
						</tr>
						<tr>
							<th><?php _e( 'AnvändarID', 'sksmh' ); ?></th>
							<td><?php $this->twitter_user_id_feed(); ?></td>
						</tr>
						</tbody>
					</table>
					<hr/>

					<?php wp_nonce_field( 'sksmh_nonce' ); ?>
					<p class="submit"><input type="submit" name="update_sksmh" id="" class="button button-primary"
					                         value="<?php _e( 'Save Changes' ); ?>"></p>
				</form>
			<?php endif; ?>


			<?php if ( $active_tab == 'display' ) : ?>
				<div
					class="more"><?php _e( 'För att visa ett flöde använder du så kallade "shortcodes" där du har möjlighet att skicka med parametrar. En shortcode skrivs (eller kopieras in) direkt i den visuella editorn för en sida, inlägg eller puff.', 'sksmh' ); ?></div>
				<h2><?php _e( 'Shortcode alternativ', 'sksmh' ); ?></h2>
				<p><?php _e( 'Beskrivning över de olika alternativ som går att använda för shortcode. Alternativen går att kombinera som följande exempel som hämtar alla flöden av <em>sundsvallskommun</em> från tjänsten <em>instagram</em>.', 'sksmh' ); ?></p>
				<p><code>[sk-social-media feed="instagram" user="sundsvallskommun" limit="10"]</code></p>
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
					<tr>
						<th class="column-primary" width="10%"><?php _e( 'Alternativ', 'sksmh' ); ?></th>
						<th><?php _e( 'Beskrivning', 'sksmh' ); ?></th>
						<th><?php _e( 'Exempel', 'sksmh' ); ?></code></th>
					</tr>
					</thead>
					<tbody>

					<tr>
						<td>feed</td>
						<td>
							<p><?php _e( 'Möjlighet att filtrera flödet utifrån en eller flera tjänster. Flera tjänster separeras med kommatecken.', 'sksmh' ); ?></p>
							<p><?php _e( 'Valbara tjänster hittar du under "Sociala flöden > Tjänster".', 'sksmh' ); ?></p>
						</td>
						<td><code>[sk-social-media feed="instagram"]</code></td>
					</tr>
					<tr>
						<td>user</td>
						<td>
							<p><?php _e( 'Möjlighet att filtrera flödet på en eller flera användare. Flera användare separeras med kommatecken.', 'sksmh' ); ?></p>
							<p><?php _e( 'Valbara användare hittar du under "Sociala flöden > Användare".', 'sksmh' ); ?></p>
						</td>
						<td><code>[sk-social-media user="sundsvallskommun"]</code></td>
					</tr>
					<tr>
						<td>limit</td>
						<td><?php _e( 'Max antal poster. Som standard listas samtliga poster.', 'sksmh' ); ?></td>
						<td><code>[sk-social-media limit="5"]</code></td>
					</tr>


					</tbody>
				</table>

			<?php endif; ?>


			<?php if ( $active_tab == 'advanced' ) : ?>
				<div
					class="more"><?php _e( 'För att erhålla nycklar och koder som nämns för tjänsterna nedan behöver du tillgång till respektive applikation för tjänsterna.', 'sksmh' ); ?></div>
				<form method="post">
					<h2><?php _e( 'Instagram', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>

						<tr>
							<th><?php _e( 'Applikations-ID:', 'sksmh' ); ?></th>
							<td><?php $this->instagram_client_id(); ?></td>
						</tr>

						<tr>
							<th><?php _e( 'Åtkomstkod (Access Token):', 'sksmh' ); ?></th>
							<td><?php $this->instagram_access_token_callback(); ?></td>
						</tr>
						</tbody>
					</table>
					<hr/>

					<h2><?php _e( 'Facebook', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php _e( 'Applikations-ID:', 'sksmh' ); ?></th>
							<td><?php $this->facebook_app_id(); ?></td>
						</tr>
						<tr>
							<th><?php _e( 'Applikationshemlighet:', 'sksmh' ); ?></th>
							<td><?php $this->facebook_app_secret(); ?></td>
						</tr>
						<tr>
							<th><?php _e( 'Åtkomstkod (Access Token):', 'sksmh' ); ?></th>
							<td><?php $this->facebook_access_token_callback(); ?></td>
						</tr>
						</tbody>
					</table>
					<hr/>

					<h2><?php _e( 'Twitter', 'sksmh' ); ?></h2>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php _e( 'Applikationsnyckel:', 'sksmh' ); ?></th>
							<td><?php $this->twitter_consumer_key_callback(); ?></td>
						</tr>

						<tr>
							<th><?php _e( 'Applikationshemlighet:', 'sksmh' ); ?></th>
							<td><?php $this->twitter_consumer_secret_callback(); ?></td>
						</tr>

						<tr>
							<th><?php _e( 'Åtkomstkod (Access Token):', 'sksmh' ); ?></th>
							<td><?php $this->twitter_access_token_callback(); ?></td>
						</tr>

						</tbody>
					</table>
					<hr/>

					<input type="hidden" name="tab" value="advanced">
					<?php wp_nonce_field( 'sksmh_nonce' ); ?>
					<p class="submit"><input type="submit" name="update_sksmh" id="" class="button button-primary"
					                         value="<?php _e( 'Save Changes' ); ?>"></p>


				</form>
			<?php endif; ?>
		</div>

		<?php

	}


	public function register_settings() {

		// Instagram Settings
		add_settings_section(
			'instagram_settings', // ID
			__( 'Instagram Settings', 'sksmh' ), // Title
			'',
			$this->sk_social_media_hub // Page
		);

		add_settings_field(
			'instagram_activate',
			__( 'Aktivera:', 'sksmh' ),
			array( $this, 'instagram_activate_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'instagram_settings' // Section
		);

		add_settings_field(
			'instagram_access_token',
			__( 'Åtkomstkod (Access Token):', 'sksmh' ),
			array( $this, 'instagram_access_token_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'instagram_settings' // Section
		);


		add_settings_field(
			'instagram_user_id_feed',
			__( 'Användarid:', 'sksmh' ),
			array( $this, 'instagram_user_id_feed' ), // Callback
			$this->sk_social_media_hub, // Page
			'instagram_settings' // Section
		);

		register_setting(
			'sksmh_instagram_group', // Option group
			'sk_social_media_hub', // Option name
			array( $this, 'sanitize_input' ) // Callback function for validate and sanitize input values
		);

		// Facebook Settings
		add_settings_section(
			'facebook_settings', // ID
			__( 'Facebook Settings', 'sksmh' ), // Title
			'',
			$this->sk_social_media_hub // Page
		);

		add_settings_field(
			'facebook_activate',
			__( 'Aktivera:', 'sksmh' ),
			array( $this, 'facebook_activate_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'facebook_settings' // Section
		);

		add_settings_field(
			'facebook_access_token',
			__( 'Åtkomstkod (Access Token):', 'sksmh' ),
			array( $this, 'facebook_access_token_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'facebook_settings' // Section
		);

		add_settings_field(
			'facebook_user_id_feed',
			__( 'Användarid:', 'sksmh' ),
			array( $this, 'facebook_user_id_feed' ), // Callback
			$this->sk_social_media_hub, // Page
			'facebook_settings' // Section
		);

		register_setting(
			'sk_social_media_hub_group', // Option group
			'sk_social_media_hub', // Option name
			array( $this, 'sanitize_input' ) // Callback function for validate and sanitize input values
		);


		// Twitter Settings
		add_settings_section(
			'twitter_settings', // ID
			__( 'Twitter Settings', 'sksmh' ), // Title
			'',
			$this->sk_social_media_hub // Page
		);

		add_settings_field(
			'twitter_activate',
			__( 'Aktivera:', 'sksmh' ),
			array( $this, 'twitter_activate_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'twitter_settings' // Section
		);


		add_settings_field(
			'twitter_consumer_key',
			__( 'Consumer Key (API Key):', 'sksmh' ),
			array( $this, 'twitter_consumer_key_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'twitter_settings' // Section
		);
		add_settings_field(
			'twitter_consumer_secret',
			__( 'Consumer Secret (API Secret):', 'sksmh' ),
			array( $this, 'twitter_consumer_secret_callback' ), // Callback
			$this->sk_social_media_hub, // Page
			'twitter_settings' // Section
		);

		/*
		add_settings_field(
		  'twitter_access_token',
		  __( 'Access Token:', 'sksmh' ),
		  array( $this, 'twitter_access_token_callback' ), // Callback
		  $this->sk_social_media_hub, // Page
		  'twitter_settings' // Section
		);


		add_settings_field(
		  'twitter_access_token_secret',
		  __( 'Access Token Secret:', 'sksmh' ),
		  array( $this, 'twitter_access_token_secret_callback' ), // Callback
		  $this->sk_social_media_hub, // Page
		  'twitter_settings' // Section
		);
		*/

		add_settings_field(
			'twitter_user_id_feed',
			__( 'Användarid:', 'sksmh' ),
			array( $this, 'twitter_user_id_feed' ), // Callback
			$this->sk_social_media_hub, // Page
			'twitter_settings' // Section
		);

		register_setting(
			'sk_social_media_hub_group', // Option group
			'sk_social_media_hub', // Option name
			array( $this, 'sanitize_input' ) // Callback function for validate and sanitize input values
		);

	}

	/**
	 * Options page callback
	 *
	 * @since  1.0.0
	 *
	 */
	public function create_admin_page() {

		// Set class property
		//$this->options = get_option( 'sksmh_instagram' );
		$this->options = get_option( 'sk_social_media_hub' );
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( 'Settings for Social media hub', 'sksmh' ); ?></h2>


			<table class="widefat">
				<tbody>
				<tr>
					<td>asdfafsd</td>
				</tr>
				</tbody>
			</table>

			<form method="post" action="options.php">
				<?php
				// print out all hidden setting fields
				settings_fields( 'sksmh_instagram_group' );
				do_settings_sections( $this->sk_social_media_hub );
				submit_button();
				?>
			</form>

		</div>
		<?php
	}


	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize_input( $input ) {

		$new_input = array();

		// Instagram
		if ( isset( $input['instagram']['activate'] ) ) {
			$new_input['instagram']['activate'] = $input['instagram']['activate'];
		}

		if ( isset( $input['instagram']['access_token'] ) ) {
			$new_input['instagram']['access_token'] = $input['instagram']['access_token'];
		}

		if ( isset( $input['instagram']['user_id_feed'] ) ) {
			$new_input['instagram']['user_id_feed'] = $input['instagram']['user_id_feed'];
		}


		// Facebook
		if ( isset( $input['facebook']['activate'] ) ) {
			$new_input['facebook']['activate'] = $input['facebook']['activate'];
		}

		if ( isset( $input['facebook']['access_token'] ) ) {
			$new_input['facebook']['access_token'] = $input['facebook']['access_token'];
		}

		if ( isset( $input['facebook']['user_id_feed'] ) ) {
			$new_input['facebook']['user_id_feed'] = $input['facebook']['user_id_feed'];
		}


		// Twitter
		if ( isset( $input['twitter']['activate'] ) ) {
			$new_input['twitter']['activate'] = $input['twitter']['activate'];
		}

		if ( isset( $input['twitter']['access_token'] ) ) {
			$new_input['twitter']['access_token'] = $input['twitter']['access_token'];
		}

		if ( isset( $input['twitter']['user_id_feed'] ) ) {
			$new_input['twitter']['user_id_feed'] = $input['twitter']['user_id_feed'];
		}


		return $new_input;
	}


	/**
	 * Setting fields for activate.
	 *
	 * @since 1.0.0
	 *
	 */
	public function instagram_activate_callback() {
		$activated = isset( $this->options['instagram']['activate'] ) ? '1' : '0';

		?>
		<label for="instagram_activate">
			<input type="checkbox" id="instagram_activate"
			       name="sk_social_media_hub[instagram][activate]" <?php checked( $activated, '1', true ); ?>> <?php _e( 'Aktivera Instagram', 'sk' ); ?>
		</label>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since  1.0.0
	 *
	 */
	public function instagram_user_id_feed() {
		?>
		<input type="text" id="user_id_feed" size="60" name="sk_social_media_hub[instagram][user_id_feed]"
		       value="<?php echo isset( $this->options['instagram']['user_id_feed'] ) ? esc_attr( $this->options['instagram']['user_id_feed'] ) : ''; ?>">
		<p class="description"><?php _e( 'Ange användarens id att hämta flöden ifrån, separera flera med kommatecken.', 'sksmh' ); ?></p>
		<div class="more">
			<p><?php _e( 'Besök den användare du önskar följa på Instagram. Användarens id är användarnamnet som visas på användarens sida alternativt kontrollera url:en, som t ex för Sundsvalls kommun <code>https://www.instagram.com/sundsvallskommun/</code> så är användarens id sundsvallskommun.' ) ?></p>
		</div>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function instagram_client_id() {
		?>
		<input type="text" id="instagram_client_id" size="60" name="sk_social_media_hub[instagram][client_id]"
		       value="<?php echo isset( $this->options['instagram']['client_id'] ) ? esc_attr( $this->options['instagram']['client_id'] ) : ''; ?>">
		<p class="description"><?php _e( 'Applikations-ID för Instagram har benämningen Client ID', 'sksmh' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 */
	public function instagram_access_token_callback() {

		if ( isset( $_GET['access_token'] ) && is_user_logged_in() ) {
			$instagram_access_token = esc_attr( $_GET['access_token'] );
		} else {
			$instagram_access_token = isset( $this->options['instagram']['access_token'] ) ? esc_attr( $this->options['instagram']['access_token'] ) : '';
		}

		$link = 'https://instagram.com/oauth/authorize/?client_id=' . $this->options['instagram']['client_id'] . '&redirect_uri=' . $this->instagram_redirect_url . '?return_uri=' . admin_url( 'admin.php?page=' . $this->sk_social_media_hub ) . '&response_type=token&scope=public_content';
		?>
		<input type="text" id="access_token" size="60" name="sk_social_media_hub[instagram][access_token]"
		       value="<?php echo $instagram_access_token; ?>">
		<a href="<?php echo $link; ?>" class="button button-primary"><?php _e( 'Hämta åtkomstkod', 'sksmh' ); ?></a>
		<p class="description"><?php _e( 'Applikations-ID måste finnas angiven och sparad innan du kan hämta en åtkomstkod.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Setting fields for activate.
	 *
	 * @since 1.0.0
	 */
	public function facebook_activate_callback() {
		$activated = isset( $this->options['facebook']['activate'] ) ? '1' : '0';

		?>
		<label for="facebook_activate">
			<input type="checkbox" id="facebook_activate"
			       name="sk_social_media_hub[facebook][activate]" <?php checked( $activated, '1', true ); ?>> <?php _e( 'Aktivera Facebook', 'sk' ); ?>
		</label>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function facebook_app_id() {
		?>
		<input type="text" id="facebook_app_id" size="60" name="sk_social_media_hub[facebook][app_id]"
		       value="<?php echo isset( $this->options['facebook']['app_id'] ) ? esc_attr( $this->options['facebook']['app_id'] ) : '' ?>">
		<p class="description"><?php _e( 'Applikations-ID för Facebook är densamma.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function facebook_app_secret() {
		?>
		<input type="text" id="facebook_app_secret" size="60" name="sk_social_media_hub[facebook][app_secret]"
		       value="<?php echo isset( $this->options['facebook']['app_secret'] ) ? esc_attr( $this->options['facebook']['app_secret'] ) : '' ?>">
		<p class="description"><?php _e( 'Applikationshemlighet för Facebook är densamma.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function facebook_access_token_callback() {
		?>
		<input type="text" id="facebook_access_token" size="60" name="sk_social_media_hub[facebook][access_token]"
		       value="<?php echo isset( $this->options['facebook']['access_token'] ) ? esc_attr( $this->options['facebook']['access_token'] ) : '' ?>">
		<a id="get-facebook-token" href="<?php echo $link; ?>"
		   class="button button-primary"><?php _e( 'Hämta åtkomstkod', 'sksmh' ); ?></a>
		<p class="description"><?php _e( 'Applikations-ID och applikationshemlighet måste finnas angiven och sparad innan du kan hämta en åtkomstkod.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function facebook_user_id_feed() {
		?>
		<input type="text" id="user_id_feed" size="60" name="sk_social_media_hub[facebook][user_id_feed]"
		       value="<?php echo isset( $this->options['facebook']['user_id_feed'] ) ? esc_attr( $this->options['facebook']['user_id_feed'] ) : ''; ?>">
		<p class="description"><?php _e( 'Ange sidnamn att hämta flöden ifrån som t ex <code>sundsvallskommun</code>, separera flera med kommatecken.', 'sksmh' ); ?></p>
		<div class="more">
			<p><?php _e( 'Besök sidan du önskar följa på Facebook. Genom att kontrollera url:en, som t ex för Sundsvalls kommun <code>https://www.facebook.com/sundsvallskommun/</code> så är sidnamnet sundsvallskommun.', 'sksmh' ); ?></p>
		</div>
		<?php
	}


	/**
	 * Setting fields for activate.
	 *
	 * @since 1.0.0
	 */
	public function twitter_activate_callback() {
		$activated = isset( $this->options['twitter']['activate'] ) ? '1' : '0';

		?>
		<label for="twitter_activate">
			<input type="checkbox" id="twitter_activate"
			       name="sk_social_media_hub[twitter][activate]" <?php checked( $activated, '1', true ); ?>> <?php _e( 'Aktivera Twitter', 'sk' ); ?>
		</label>
		<?php
	}


	/**
	 * [twitter_access_token_callback description]
	 *
	 * @since 1.0.0
	 */
	public function twitter_access_token_callback() {

		if ( isset( $_GET['access_token'] ) && is_user_logged_in() ) {
			$twitter_access_token = esc_attr( $_GET['access_token'] );
		} else {
			$twitter_access_token = isset( $this->options['twitter']['access_token'] ) ? esc_attr( $this->options['twitter']['access_token'] ) : '';
		}
		?>
		<input type="text" id="twitter_access_token" size="60" name="sk_social_media_hub[twitter][access_token]"
		       value="<?php echo $this->options['twitter']['access_token'] ?>">
		<a id="get-twitter-token" href="<?php echo $link; ?>"
		   class="button button-primary"><?php _e( 'Hämta åtkomstkod', 'sksmh' ); ?></a>
		<p class="description"><?php _e( 'Applikations-ID och applikationshemlighet måste finnas angiven och sparad innan du kan hämta en åtkomstkod.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function twitter_consumer_key_callback() {
		?>
		<input type="text" id="consumer_key" size="60" name="sk_social_media_hub[twitter][consumer_key]"
		       value="<?php echo isset( $this->options['twitter']['consumer_key'] ) ? esc_attr( $this->options['twitter']['consumer_key'] ) : '' ?>">
		<p class="description"><?php _e( 'Applikationsnyckel för Twitter har benämningen Consumer Key.', 'sksmh' ); ?></p>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function twitter_consumer_secret_callback() {
		?>
		<input type="text" id="consumer_secret" size="60" name="sk_social_media_hub[twitter][consumer_secret]"
		       value="<?php echo isset( $this->options['twitter']['consumer_secret'] ) ? esc_attr( $this->options['twitter']['consumer_secret'] ) : '' ?>">
		<p class="description"><?php _e( 'Applikationshemlighet för Twitter har benämningen Consumer Secret.', 'sksmh' ); ?></p>
		<?php
	}


	/**
	 * Get the settings option array and print one of its values
	 *
	 * @since 1.0.0
	 *
	 */
	public function twitter_user_id_feed() {
		?>
		<input type="text" id="user_id_feed" size="60" name="sk_social_media_hub[twitter][user_id_feed]"
		       value="<?php echo isset( $this->options['twitter']['user_id_feed'] ) ? esc_attr( $this->options['twitter']['user_id_feed'] ) : ''; ?>">
		<p class="description"><?php _e( 'Ange användarens id att hämta flöden ifrån, separera flera med kommatecken.', 'sksmh' ); ?></p>
		<div class="more">
			<p><?php _e( 'Besök sidan du önskar följa på Twitter. Genom att kontrollera url:en, som t ex för Sundsvalls Tidning <code>https://twitter.com/STnu</code> så är sidnamnet STnu.', 'sksmh' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Get twitter token and save to option.
	 *
	 * @since 1.0.0
	 *
	 */
	public function get_twitter_token() {
		$this->options = get_option( 'sk_social_media_hub' );
		$auth_url      = 'https://api.twitter.com/oauth2/token';

		$api_credentials = base64_encode( $this->options['twitter']['consumer_key'] . ':' . $this->options['twitter']['consumer_secret'] );
		$header          = array(
			'Authorization: Basic ' . $api_credentials,
			'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'
		);

		$curl_settings = array(
			CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
		);

		$result = SK_Social_Media_Hub::curl_processor( $auth_url, $header, $curl_settings );

		wp_send_json( json_decode( $result ) );

		die();

	}

	/**
	 * Get twitter token and save to option.
	 *
	 * @since 1.0.0
	 */
	public function get_facebook_token() {
		$this->options = get_option( 'sk_social_media_hub' );
		$auth_url      = 'https://graph.facebook.com/oauth/access_token?';

		$query_args = array(
			'grant_type'    => 'client_credentials',
			'client_id'     => $this->options['facebook']['app_id'],
			'client_secret' => $this->options['facebook']['app_secret']
		);

		$query = add_query_arg( $query_args, $auth_url );

		$curl_settings = array(
			CURLOPT_URL => $query,
		);

		$result = SK_Social_Media_Hub::curl_processor( $auth_url, $header = false, $curl_settings );

		if ( strstr( $result, 'access_token=' ) ) {
			$temp                  = explode( '=', $result );
			$token['access_token'] = $temp[1];
			$result                = json_encode( $token );
		}

		wp_send_json( json_decode( $result ) );


		die();

	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->sk_social_media_hub, plugin_dir_url( __FILE__ ) . 'css/sk-social-media-hub-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->sk_social_media_hub, plugin_dir_url( __FILE__ ) . 'js/sk-social-media-hub-admin.js' );
		wp_enqueue_script( $this->sk_social_media_hub, array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->sk_social_media_hub, 'ajax_object', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ajax_nonce' )
			)
		); // setting ajaxurl and nonce

	}

}
