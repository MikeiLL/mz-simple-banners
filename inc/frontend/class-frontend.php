<?php

namespace Mz_Simple_Banners\Inc\Frontend;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author    Lex Web Dev
 */
class Frontend {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param       string $plugin_name        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 * @param       string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

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
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mz-simple-banners-frontend.css', array(), $this->version, 'all' );

	}

	/**
	 * Display latest Banner CPT post if on front page.
	 *
	 * @since    1.0.0
	 */
	public function display_banner() {
	    if (!is_front_page()) return;

        // The Query
        $args = array(
            'post_type' => 'mz_banner',
            'post_status' => 'publish',
            'posts_per_page'=> 1,
            'order'=>'DESC',
        );
        $the_query = new \WP_Query( $args );

        // The Loop
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                if (in_array('fl-builder', get_body_class())):
                // echo "<pre>";
                // print_r(get_post_meta($the_query->post->ID));
                // echo "</pre>";
                endif;
                if (function_exists('get_field')) {
                    $banner_background_color = get_field('banner_background_color');
                    $banner_background_opacity = get_field('banner_background_opacity');
                    $text_color = get_field('text_color');
                }
                // Assign variables with defaults
                $bgcolor = !empty($banner_background_color) ? $banner_background_color : '#99999';
                $opacity = !empty($banner_background_opacity) ? $banner_background_opacity : false;
                $text_color = !empty($text_color) ? $text_color : '#000';
                // Begin style rule
                $banner_style = 'style="';
                // build the background
                $banner_style .= 'background:' . $this->hex2rgba($bgcolor, $opacity) . ';';
                // build the text color
                $banner_style .= 'color:' . $text_color . ';';
                // build the positioning
                $banner_style .= 'position:fixed;';
                $banner_style .= 'bottom:0;';
                // build the sizing
                $banner_style .= 'min-height:200px;';
                $banner_style .= 'width:100%;';
                // z-index
                $banner_style .= 'z-index:1000;';
                //end the style rule
                $banner_style .= '"';
                echo '<div '. $banner_style . 'class="mx-auto p-2 mz-simple-banner mz-simple-banner-' . get_the_title(). '">';
                echo '  <button id="mzBannerClose" style="opacity:1" type="button" class="close" aria-label="Close">';
                echo '      <span aria-hidden="true" style="color:' . $text_color . ';">&times;</span>';
                echo '  </button>';
                echo '  <div class="text-center">';
                echo the_content();
                echo '  </div>';
                echo '</div>';
            }
            /* Restore original Post Data */
            wp_reset_postdata();
        } else {
            return;
        }


	}

	/**
	 * Private function Convert Hex to RGBa
	 *
	 * source: https://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
	 */
	 /* Convert hexdec color string to rgb(a) string */

    private function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if(empty($color))
              return $default;

        //Sanitize $color if "#" is provided
            if ($color[0] == '#' ) {
                $color = substr( $color, 1 );
            }

            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                    $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                    $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                    return $default;
            }

            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);

            //Check if opacity is set(rgba or rgb)
            if($opacity){
                if(abs($opacity) > 1)
                    $opacity = 1.0;
                $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                $output = 'rgb('.implode(",",$rgb).')';
            }

            //Return rgb(a) color string
            return $output;
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
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mz-simple-banners-frontend.js', array( 'jquery' ), $this->version, false );

	}

}
