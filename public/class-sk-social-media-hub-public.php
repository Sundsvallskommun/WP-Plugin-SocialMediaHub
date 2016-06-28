<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    sk_social_media_hub
 * @subpackage sk_social_media_hub/public
 * @author     Your Name <email@example.com>
 */
class SK_Social_Media_Hub_Public {

	private $sk_social_media_hub;
	private $version;
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $sk_social_media_hub       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $sk_social_media_hub, $version ) {

		$this->sk_social_media_hub = $sk_social_media_hub;
		$this->version = $version;

	}

	public function add_shortcode(){
		add_shortcode( 'sk-social-media', array( $this, 'html_render' ) );
	}


	/**
	 * Render html output
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  array $atts
	 * 
	 * @return echo
	 */
	public function html_render( $atts ){
		  //start buffering
			$feeds = $this->get_feeds( $atts );
  		ob_start();
  		//$this->html_render( $atts );
  		require( 'partials/sk-social-media-hub-public-display.php' );
  		$output = ob_get_contents();
  		ob_get_clean();
  		//$form = apply_filters( 'stc_form', $form, 'teststring' );
  		return $output;		

	}
	
	/**
	 * Cuts a on next word, instead of breaking the word.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  string $string
	 * @param  int 		$max_length
	 * 
	 * @return string
	 */
	public function string_cut( $string, $max_length ){  

	  // remove shortcode if there is
	  $string = strip_shortcodes( $string );
	  $string = strip_tags( $string ); 

	  if( strlen( $string ) > $max_length ){  
	    $string = substr( $string, 0, $max_length );  
	    $pos = strrpos( $string, ' ' );  
	      
	    if($pos === false) {  
	      return substr($string, 0, $max_length)." ... ";  
	    }  
	    return substr($string, 0, $pos)." ... ";  

	  }else{  
	    return $string;  
	  }  
	}  

	/**
	 * Returns an array with activated feeds 
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @return array
	 */
	public function get_activated_feeds(){

		$activated = array();
		$this->options = get_option( 'sk_social_media_hub' );
		foreach ($this->options as $key => $option ) {
			if( isset( $option['activate'] ) && $option['activate'] === 'on' ){
				$activated[] = $key;
			}
		}

		return $activated;

	}

	/**
	 * Returns the feed posts extended with meta fields.
	 * 
	 * @author Daniel Söderström <daniel.soderstrom@cybercom.com>
	 * 
	 * @param  boolean $atts
	 * 
	 * @return object
	 */
	public function get_feeds( $atts = false ){

    extract( shortcode_atts( array(
      'feed' => false,
      'user' => false,
      'limit' => false        
    ), $atts ));

    if(empty( $limit ))
    	$limit = -1;

		$args = array( 
			'post_type' 	=> sk_social_media_hub::$post_type,
			'numberposts'	=> $limit
		);


    if(!empty( $feed ) ){
    	$feed = array( array(
				'taxonomy' 	=> 'social_media_hub_service',
				'field'			=> 'slug',
				'terms'			=> explode(',', $feed ),
			) );
    }

    if(!empty( $user ) ){
    	$user = array( array(
				'taxonomy' 	=> 'social_media_hub_user',
				'field'			=> 'slug',
				'terms'			=> explode(',', $user )
			) );    
    }

    $args['tax_query'] = array( 'relation' => 'AND', $feed, $user );

    $args = apply_filters( 'sksmh_get_posts', $args );

    //util::debug( $args );

		$feeds = get_posts( $args );

		$activated = $this->get_activated_feeds();

		$temp = array();
		if(! empty( $feeds ) ){
			foreach ($feeds as $key => $feed ) {
				$service_name = get_post_meta( $feed->ID, '_service_name', true );
				
				// remove from array if not activated.
				if( !in_array( $service_name, $activated ) ){
					unset( $feeds[$key] );
				}else{
					$temp[$key] = $feed;
					$temp[$key]->service_name 			= $service_name;
					$temp[$key]->service_link 			= get_post_meta( $feed->ID, '_service_link', true );
					$temp[$key]->service_id 				= get_post_meta( $feed->ID, '_service_id', true );
					$temp[$key]->service_image_url 	= get_post_meta( $feed->ID, '_service_image_url', true );
					$temp[$key]->service_user 			= get_post_meta( $feed->ID, '_service_user', true );
				}
			}
		}

		$feeds = $temp;

		return $feeds;

	}


  public static function get_feed_icon( $feed ){

    switch ( $feed->service_name ) {
      case 'facebook':
        echo '<i class="fa fa-facebook pull-right"></i>';
        break;
      case 'instagram':
        echo '<i class="fa fa-instagram pull-right"></i>';
        break;
      case 'twitter':
        echo '<i class="fa fa-twitter pull-right"></i>';
        break;
                          
      default:
        return false;
        break;
    }

  } 	

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sk_social_media_hub_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sk_social_media_hub_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->sk_social_media_hub, plugin_dir_url( __FILE__ ) . 'css/sk-social-media-hub-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sk_social_media_hub_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sk_social_media_hub_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		?>
		<script type="text/javascript">

		</script>

		<?php

		//if( isset( $_GET['']))

		wp_enqueue_script( $this->sk_social_media_hub, plugin_dir_url( __FILE__ ) . 'js/sk-social-media-hub-public.js', array( 'jquery' ), $this->version, false );

	}

}
