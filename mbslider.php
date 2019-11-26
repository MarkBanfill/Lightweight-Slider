<?php
/*
Plugin Name: MBslider
Description: Simple non-bloated WordPress Image Slider
Version: 1.0
Author: Mark Banfill
Author URI: https://www.markbanf.co.uk
*/

// Kill the script if it is accessed directly
defined( 'ABSPATH' ) or die( 'Nope!' );


// Register script and stylesheet
add_action('wp_print_scripts', 'mbslider_register_scripts');
add_action('wp_print_styles', 'mbslider_register_styles');
function mbslider_register_scripts() {
  if (!is_admin()) {
    wp_register_script('mbslider_script', plugins_url('mbslider.js', __FILE__));
    wp_enqueue_script('mbslider_script');
  }
}
function mbslider_register_styles() {
  wp_register_style('mbslider_styles', plugins_url('mbslider.css', __FILE__));
  wp_enqueue_style('mbslider_styles');
}


// Setup custom post type
function mbslider_setup_post_type() {
  $args = array(
    'public' => true,
    'label' => 'MBslider',
    'supports' => array(
      'title',
      'thumbnail'
    )
  );
  register_post_type('mbslider', $args);
}
add_action('init', 'mbslider_setup_post_type');


// Create settings page
class MySettingsPage
{
  // Hold the values to be used in the fields callbacks
  private $options;

  // Start up
  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  // Add options page under "Settings"
  public function add_plugin_page()
  {
    add_submenu_page(
      'edit.php?post_type=mbslider',
      'MBslider Settings',
      'Settings',
      'manage_options',
      'my-setting-admin',
      array( $this, 'create_admin_page' )
    );
  }

  // Options page callback (send options to 'wp-admin/options.php' for saving)
  public function create_admin_page()
  {
    // Set class property
    $this->options = get_option( 'mbslider_option_name' );
    ?>
    <div class="wrap">
      <h1>MBslider</h1>
      <form method="post" action="options.php">
        <?php
        // This prints out all hidden setting fields
        settings_fields( 'mbslider_option_group' );
        do_settings_sections( 'my-setting-admin' );
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  // Register and add settings
  public function page_init()
  {
    add_settings_section(
      'setting_section_id', // ID
      'Settings', // Title
      array( $this, 'print_section_info' ), // Callback
      'my-setting-admin' // Page
    );
    add_settings_field(
      'slide_height', // ID
      'Slide Height (% of width)', // Title
      array( $this, 'slide_height_callback' ), // Callback
      'my-setting-admin', // Page
      'setting_section_id' // Section
    );
    add_settings_field(
      'delay',
      'Transition Delay (seconds)',
      array( $this, 'delay_callback' ),
      'my-setting-admin',
      'setting_section_id'
    );
    add_settings_field(
      'controls',
      'Show Controls',
      array( $this, 'controls_callback' ),
      'my-setting-admin',
      'setting_section_id',
      array('Activate this setting to display navigation controls.')
    );
    register_setting(
      'mbslider_option_group',
      'mbslider_option_name',
      array( $this, 'sanitize' )
    );
    register_setting(
      'mbslider_option_group',
      'controls'
    );
  }

  // Sanitize each setting field as needed
  // @param array $input contains all settings fields as array keys
  public function sanitize( $input )
  {
    $new_input = array();
    if( isset( $input['slide_height'] ) )
    $new_input['slide_height'] = absint( $input['slide_height'] );

    if( isset( $input['delay'] ) )
    $new_input['delay'] = absint( $input['delay'] );

    //if( isset( $input['delay'] ) )
    //$new_input['delay'] = sanitize_text_field( $input['delay'] );

    return $new_input;
  }

  // Print the Section text
  public function print_section_info()
  {
    print '<p>Thanks for using MBslider.</p>';
    print '<p>Please Add New slides then use the shortcode [mbslider-shortcode] to display the slideshow on your page.</p>';
  }

  // Get the settings option array and print one of its values
  public function slide_height_callback()
  {
    printf(
      '<input type="text" id="slide_height" name="mbslider_option_name[slide_height]" value="%s" />',
      isset( $this->options['slide_height'] ) ? esc_attr( $this->options['slide_height']) : ''
    );
  }

  public function delay_callback()
  {
    printf(
      '<input type="text" id="delay" name="mbslider_option_name[delay]" value="%s" />',
      isset( $this->options['delay'] ) ? esc_attr( $this->options['delay']) : ''
    );
  }

  public function controls_callback($args)
  {
    $html = '<input type="checkbox" id="controls" name="controls" value="1" ' . checked(1, get_option('controls'), false) . '/>';
    $html .= '<label for="controls"> '  . $args[0] . '</label>';
    echo $html;
  }
}

if( is_admin() )
$my_settings_page = new MySettingsPage();


// Output slideshow from custom post data
add_shortcode('mbslider-shortcode', 'mbslider_function');
function mbslider_function($type='mbslider_function') {
  $args = array(
    'post_type' => 'mbslider',
    'posts_per_page' => 5
  );
  $my_options = get_option('mbslider_option_name');
  $result .= '<div class="mbslider-wrapper">';

  // Add controls if required
  if ( get_option('controls') ) {
    $result .= '<a href="#" class="mbslider_next">&#10093</a>';
    $result .= '<a href="#" class="mbslider_prev">&#10092</a>';
  };

  $result .= '<div id="mbslider" class="mbslider" data-delay="' . $my_options['delay'] . '" style="padding-bottom:' . $my_options['slide_height'] . '%;">';

  //the loop
  $loop = new WP_Query($args);
  while ($loop->have_posts()) {
    $loop->the_post();

    $the_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);
    $the_srcset = wp_get_attachment_image_srcset(get_post_thumbnail_id($post->ID), $type);
    $result .='<figure><img src="' . $the_src[0] . '" srcset="' . $the_srcset . '" data-thumb="' . $the_src[0] . '" alt=""/><figcaption>'.get_the_title().'</figcaption></figure>';
  }
  $result .= '</div>';
  $result .='</div>';
  $result .='<p>This is a test paragraph after the slideshow</p>';
  return $result;

}

?>
