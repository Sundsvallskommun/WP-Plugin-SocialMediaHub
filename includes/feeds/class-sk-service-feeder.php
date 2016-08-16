<?php

/**
 * @todo
 * - Update feed instead of deleting.
 * - Error messages/logs
 *
 * Service feeder
 * Twitter: http://stackoverflow.com/questions/12916539/simplest-php-example-for-retrieving-user-timeline-with-twitter-api-version-1-1
 */
class SK_Service_Feeder {

	//private $client_id = '5c13d07e555f4530a37ce817b14bce82';

	private $api = array(
		'instagram' => 'https://api.instagram.com/v1/',
		'facebook'  => 'https://graph.facebook.com/',
		'twitter'   => 'https://api.twitter.com/1.1/statuses/user_timeline.json'
	);

	private $options;
	private $services_taxonomy = 'social_media_hub_service';
	private $users_taxonomy = 'social_media_hub_user';
	private $post_type = 'social_media_hub';


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 */
	function __construct() {
		$this->add_actions();
	}

	/**
	 * WP Action hooks
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'set_options' ), 10 );
		add_action( 'init', array( $this, 'actions' ), 15 );
		add_action( 'sksmh_cron', array( $this, 'importer' ), 1 );
	}

	/**
	 * Get current options and save to array.
	 *
	 * @since 1.0.0
	 */
	public function set_options() {
		$this->options = get_option( 'sk_social_media_hub' );
	}


	/**
	 * Method explode user ids by comma sign and remove white spaces.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $service
	 *
	 * @return mixed
	 */
	private function get_users_to_follow( $service ) {

		$users = explode( ',', $this->options[ $service ]['user_id_feed'] );
		$users = array_filter( array_map( 'trim', $users ) );

		if ( empty( $users ) ) {
			return false;
		}

		return $users;
	}

	/**
	 * GET actions for triggering import.
	 *
	 * @since 1.0.0
	 */
	public function actions() {
		if ( isset( $_GET['sksmh-import'] ) && $_GET['sksmh-import'] === 'HASHCODE' ) {
			$this->importer( 'GET_REQUEST' );
		}

	}

	/**
	 * Import the activated feeds.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $flag
	 */
	public function importer( $flag = false ) {
		if ( empty( $flag ) ) {
			$flag = 'WP_CRON';
		}

		update_option( 'sksmh_latest_import', date( 'Y-m-d H:i:s' ) . ' ' . $flag );

		$data = array();
		if ( isset( $this->options['instagram']['activate'] ) && $this->options['instagram']['activate'] === 'on' ) {

			$user_ids = $this->get_users_to_follow( 'instagram' );

			if ( ! empty( $user_ids ) ) {
				foreach ( $user_ids as $user_id ) {

					$user_feed     = $this->api['instagram'] . 'users/search?q=' . $user_id . '&access_token=' . $this->options['instagram']['access_token'];
					$user          = json_decode( $this->curl_processor( $user_feed ) );
					$insta_user_id = isset( $user->data[0]->id ) ? $user->data[0]->id : false;

					if ( isset( $user->data[0]->id ) ) {
						$instagram_api_feed = $this->api['instagram'] . 'users/' . $insta_user_id . '/media/recent/?access_token=' . $this->options['instagram']['access_token'] . '&count=20';
						$data               = array_merge( $data, $this->get_feeds( $instagram_api_feed, 'instagram' ) );
					}

				}
			}

		}


		if ( isset( $this->options['facebook']['activate'] ) && $this->options['facebook']['activate'] === 'on' ) {

			$user_ids = $this->get_users_to_follow( 'facebook' );

			if ( ! empty( $user_ids ) ) {
				foreach ( $user_ids as $user_id ) {
					$facebook_api_feed = 'https://graph.facebook.com/' . $user_id . '/posts?fields=id,picture,full_picture,from,message,message_tags,story,link,source,name,caption,description,type,status_type,object_id,created_time&access_token=' . $this->options['facebook']['access_token'] . '&limit=20';
					$data              = array_merge( $data, $this->get_feeds( $facebook_api_feed, 'facebook' ) );
				}
			}

		}


		if ( isset( $this->options['twitter']['activate'] ) && $this->options['twitter']['activate'] === 'on' ) {

			$user_ids = $this->get_users_to_follow( 'twitter' );
			if ( ! empty( $user_ids ) ) {
				foreach ( $user_ids as $user_id ) {
					$twitter_api_feed = 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=20&screen_name=' . $user_id;
					$data             = array_merge( $data, $this->get_feeds( $twitter_api_feed, 'twitter' ) );
				}
			}

		}

		$this->delete_feeds();
		$this->insert_feeds( $data );
		//update_option('sksmh_latest_import', date( 'Y-m-d H:i:s' ) . ' ' . $flag );

	}

	/**
	 * Using curl to grab feed from service.
	 *
	 *
	 * @param  string $url
	 * @param  boolean $header
	 *
	 * @return array
	 */
	private function curl_processor( $url, $header = false ) {

		$settings = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2,
		);

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
			return $e->getMessage();
		}
	}

	/**
	 * Get feeds from services and save to array.
	 *
	 * @since 1.0.0
	 *
	 * @param $feed
	 * @param $service
	 *
	 * @return array
	 */
	public function get_feeds( $feed, $service ) {

		$header = '';
		if ( $service === 'twitter' ) {
			$auth_token = $this->options['twitter']['access_token'];
			$header     = array( "Authorization: Bearer " . $auth_token );
		}

		$results = json_decode( $this->curl_processor( $feed, $header ) );
		if ( empty( $results ) ) {
			wp_die( 'Det gick inte att hämta flöde', 'sksmh' );
		}

		if ( $service === 'instagram' ) {
			$data = array();

			foreach ( $results->data as $key => $result ) {
				$data[ $key ]['post_date'] = date_i18n( 'Y-m-d H:i:s', $result->created_time );

				$data[ $key ]['text'] = '';
				if ( ! empty( $result->caption ) ) {
					$data[ $key ]['text'] = $result->caption->text;
				}

				$data[ $key ]['image_url'] = $result->images->low_resolution->url;
				$data[ $key ]['service']   = $service;
				$data[ $key ]['id']        = $result->id;
				$data[ $key ]['link']      = $result->link;
				$data[ $key ]['user']      = $result->user->username;
				$data[ $key ]['json']      = json_encode( $results->data[ $key ] );

			}
		}

		//http://stackoverflow.com/a/28298410/5428759
		//Images facebook.
		if ( $service === 'facebook' ) {

			$data = array();

			foreach ( $results->data as $key => $result ) {
				$data[ $key ]['post_date'] = date_i18n( 'Y-m-d H:i:s', strtotime( $result->created_time ) );

				$data[ $key ]['text'] = '';
				if ( ! empty( $result->message ) ) {
					$data[ $key ]['text'] = $result->message;
				}

				$data[ $key ]['image_url'] = isset( $result->full_picture ) ? $result->full_picture : '';
				$data[ $key ]['service']   = $service;
				$data[ $key ]['id']        = $result->id;
				$data[ $key ]['link']      = isset( $result->link ) ? $result->link : '';
				$data[ $key ]['user']      = $result->from->name;
				$data[ $key ]['json']      = json_encode( $results->data[ $key ] );

			}

		}

		if ( $service === 'twitter' ) {

			$data = array();

			foreach ( $results as $key => $result ) {
				$data[ $key ]['post_date'] = date_i18n( 'Y-m-d H:i:s', strtotime( $result->created_at ) );

				$data[ $key ]['text'] = '';
				if ( ! empty( $result->text ) ) {
					$data[ $key ]['text'] = $result->text;
				}

				$data[ $key ]['image_url'] = isset( $result->entities->media[0]->media_url_https ) ? $result->entities->media[0]->media_url_https : '';
				$data[ $key ]['service']   = $service;
				$data[ $key ]['id']        = $result->id;
				$data[ $key ]['link']      = 'https://twitter.com/statuses/' . $result->id;
				$data[ $key ]['user']      = $result->user->name;
				//$data[$key]['json']       = json_encode( $result );
				$data[ $key ]['json'] = '';

			}

		}

		return $data;
	}

	/**
	 * Save feeds to custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param $data
	 */
	public function insert_feeds( $data ) {

		foreach ( $data as $item ) {

			$text = '';
			if ( ! empty( $item['text'] ) ) {
				$text = ' : ' . mb_substr( $item['text'], 0, 50 ) . ' ... ';
			}

			$post_title = $item['service'] . ' : ' . $item['user'] . $text;

			// Create post object
			$post_data = array(
				'post_type'    => $this->post_type,
				'post_date'    => $item['post_date'],
				'post_title'   => wp_strip_all_tags( $post_title ),
				'post_content' => $item['text'],
				'post_status'  => 'publish',
				'post_author'  => 1,
			);


			// Insert the post into the database
			$post_id = wp_insert_post( $post_data );

			// setting service data as post meta
			update_post_meta( $post_id, '_service_name', $item['service'] );
			update_post_meta( $post_id, '_service_id', $item['id'] );
			update_post_meta( $post_id, '_service_image_url', $item['image_url'] );
			update_post_meta( $post_id, '_service_link', $item['link'] );
			update_post_meta( $post_id, '_service_user', $item['user'] );
			update_post_meta( $post_id, '_service_json', $item['json'] );


			// insert or update term for service
			wp_insert_term( $item['service'], $this->services_taxonomy );

			// setting service as term
			$terms = get_terms( 'social_media_hub_service', array( 'hide_empty' => false ) );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $item['service'] === mb_strtolower( $term->name ) ) {
						$post_term = $term->term_id;
						break;
					}

				}
			}

			wp_set_post_terms( $post_id, $post_term, $this->services_taxonomy );
			unset( $terms );


			$term = term_exists( $item['user'], $this->users_taxonomy );

			if ( $term !== 0 && $term !== null ) {
				$term = get_term( $term['term_id'], $this->users_taxonomy );
				$desc = $term->description;
				$desc .= ! strstr( $term->description, $item['service'] ) ? ', ' . $item['service'] : false;
				wp_update_term( $term->term_id, $this->users_taxonomy, array( 'description' => $desc ) );
			} else {
				wp_insert_term( $item['user'], $this->users_taxonomy, array( 'description' => $item['service'] ) );
			}

			$terms = get_terms( 'social_media_hub_user', array( 'hide_empty' => false ) );
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {

					if ( mb_strtolower( $item['user'] ) === mb_strtolower( $term->name ) ) {
						$post_term = $term->term_id;
						break;
					}

				}
			}

			wp_set_post_terms( $post_id, $post_term, $this->users_taxonomy );

		}


		//die();

	}
	/**
	 * Delete current feeds before import.
	 *
	 * @since 1.0.0
	 *
	 */
	private function delete_feeds() {
		global $wpdb;
		$post_type = $this->post_type;

		// Delete post meta
		$sql = "DELETE FROM $wpdb->postmeta WHERE post_id IN (
          SELECT * FROM ( 
            SELECT $wpdb->posts.ID FROM $wpdb->posts
              WHERE post_type = '" . $post_type . "'
          ) as P
        ) 
      ";
		$wpdb->query( $sql );

		// Delete posts where not in skolform YH
		$sql = "DELETE post FROM $wpdb->posts AS post 
      LEFT JOIN $wpdb->postmeta AS meta ON post.ID = meta.post_id 
      WHERE post.post_type = '" . $post_type . "';";
		$wpdb->query( $sql );

	}


}

new SK_Service_Feeder();