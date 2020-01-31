<?php
/*
Plugin Name: Lightweight Slider
Plugin URI: https://slider.markbanf.co.uk
Description: Simple non-bloated WordPress Image Slider
Version: 1.0
Author: Mark Banfill
Author URI: https://www.markbanf.co.uk
*/

// Kill the script if it is accessed directly
defined( 'ABSPATH' ) or die( 'Nope!' );


// Register script and stylesheet
add_action('wp_print_scripts', 'lws_register_scripts');
add_action('wp_print_styles', 'lws_register_styles');
function lws_register_scripts() {
  if (!is_admin()) {
    wp_register_script('lightweight_slider_script', plugins_url('lightweight-slider.js', __FILE__), array('jquery')); // jQuery is required by the plugin so add it as a dependancy
    wp_enqueue_script('lightweight_slider_script');
  }
}
function lws_register_styles() {
  wp_register_style('lightweight_slider_styles', plugins_url('lightweight-slider.css', __FILE__));
  wp_enqueue_style('lightweight_slider_styles');
}


// Setup custom post type
function lws_setup_post_type() {
  $lws_labels = array(
    'name' => _x( 'Lightweight Slider', 'post type general name' ),
    'singular_name' => _x( 'Slide', 'post type singular name' ),
    'add_new' => _x( 'Add New', 'Slide' ),
    'add_new_item' => __( 'Add New Slide' ),
    'edit_item' => __( 'Edit Slide' ),
    'new_item' => __( 'New Slide' ),
    'all_items' => __( 'All Slides' ),
    'view_item' => __( 'View Slide' ),
    'search_items' => __( 'Search slides' ),
    'not_found' => __( 'No slides found' ),
    'not_found_in_trash' => __( 'No slides found in the Trash' ),
    'parent_item_colon' => '',
    'menu_name' => 'Lightweight Slider'
  );
  $lws_args = array(
    'public' => true,
    'labels' => $lws_labels,
    'menu_position' => 4,
    'supports' => array(
      'title',
      'thumbnail'
    )
  );
  register_post_type('lightweight-slider', $lws_args);
}
add_action('init', 'lws_setup_post_type');


// Add slide_caption and slide_link custom fields to custom post type
add_action('admin_init', 'lws_admin_init');

function lws_admin_init(){
  add_meta_box('slide-caption-meta', 'Slide Caption', 'lws_slide_caption', 'lightweight-slider', 'normal', 'low');
  add_meta_box('slide-link-meta', 'Slide Link', 'lws_slide_link', 'lightweight-slider', 'normal', 'low');
}
function lws_slide_caption(){
  global $post;  // Required to access the global post object
  $lws_custom = get_post_custom($post->ID);
  $lws_slide_caption = $lws_custom['slide_caption'][0];
  ?>
  <label>Text</label>
  <input type="text" maxlength="500" name="slide_caption" value="<?php echo esc_html($lws_slide_caption); ?>" />
  <?php
}
function lws_slide_link(){
  global $post;
  $lws_custom = get_post_custom($post->ID);
  $lws_slide_link = $lws_custom['slide_link'][0];
  ?>
  <label>URL</label>
  <input type="url" pattern="https?://.+" name="slide_link" value="<?php echo esc_url($lws_slide_link); ?>" />
  <?php
}

add_action('save_post', 'lws_save_details');

function lws_save_details(){
  global $post;
  $lws_slide_caption = sanitize_text_field($_POST['slide_caption']);
  update_post_meta($post->ID, 'slide_caption', $lws_slide_caption);
  $lws_slide_link = sanitize_text_field($_POST['slide_link']);
  update_post_meta($post->ID, 'slide_link', $lws_slide_link);
}


// Override post columns (to add the custom fields)
add_action('manage_posts_custom_column', 'lws_custom_columns');
add_filter('manage_edit-lightweight-slider_columns', 'lws_edit_columns');

function lws_edit_columns($lws_columns){
  $lws_columns = array(
    'cb' => '<input type="checkbox" />',
    'title' => 'Slide Title',
    'slide_caption' => 'Slide Caption',
    'slide_link' => 'Slide Link',
    'date' => 'Date'
  );
  return $lws_columns;
}
function lws_custom_columns($lws_column){
  global $post;

  switch ($lws_column) {
    case 'slide_caption':
    $lws_custom = get_post_custom();
    echo $lws_custom['slide_caption'][0];
    break;
    case 'slide_link':
    $lws_custom = get_post_custom();
    echo $lws_custom['slide_link'][0];
    break;
  }
}


// Create settings page
class LightWeightSliderSettingsPage
{
  // Start up
  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'lws_add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'lws_page_init' ) );
  }

  // Add options page under 'Settings'
  public function lws_add_plugin_page()
  {
    add_submenu_page(
      'edit.php?post_type=lightweight-slider',
      'Lightweight Slider Settings',
      'Settings',
      'manage_options',
      'my-setting-admin',
      array( $this, 'lws_create_admin_page' )
    );
  }

  // Options page callback (send options to 'wp-admin/options.php' for saving)
  public function lws_create_admin_page()
  {
    ?>
    <div class="wrap">
      <h1>Lightweight Slider</h1>
      <form method="post" action="options.php">
        <?php
        // This prints out all hidden setting fields
        settings_fields( 'lws_option_group' );
        do_settings_sections( 'my-setting-admin' );
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }


  // Register and add plugin settings
  public function lws_page_init()
  {
    add_settings_section(
      'setting_section_id',
      'Settings',
      array( $this, 'lws_print_section_info' ),
      'my-setting-admin'
    );
    add_settings_field(
      'lws_slide_height',
      'Slide height (% of width)',
      array( $this, 'lws_slide_height_callback' ),
      'my-setting-admin',
      'setting_section_id'
    );
    add_settings_field(
      'lws_delay',
      'Delay (seconds)',
      array( $this, 'lws_delay_callback' ),
      'my-setting-admin',
      'setting_section_id'
    );
    add_settings_field(
      'lws_transition',
      'Transition (seconds)',
      array( $this, 'lws_transition_callback' ),
      'my-setting-admin',
      'setting_section_id'
    );
    add_settings_field(
      'lws_animation',
      'Animation type',
      array( $this, 'lws_animation_callback' ),
      'my-setting-admin',
      'setting_section_id'
    );
    add_settings_field(
      'lws_controls',
      'Show controls',
      array( $this, 'lws_controls_callback' ),
      'my-setting-admin',
      'setting_section_id',
      array('Activate this setting to display navigation controls.')
    );
    add_settings_field(
      'lws_pagination',
      'Show pagination',
      array( $this, 'lws_pagination_callback' ),
      'my-setting-admin',
      'setting_section_id',
      array('Activate this setting to display pagination.')
    );
    register_setting(
      'lws_option_group',
      'lws_slide_height',
      'lws_sanitize_text'
    );
    register_setting(
      'lws_option_group',
      'lws_delay',
      'lws_sanitize_text'
    );
    register_setting(
      'lws_option_group',
      'lws_transition',
      'lws_sanitize_text'
    );
    register_setting(
      'lws_option_group',
      'lws_animation'
    );
    register_setting(
      'lws_option_group',
      'lws_controls'
    );
    register_setting(
      'lws_option_group',
      'lws_pagination'
    );
  }


  // Sanitize input
  public function lws_sanitize_text($lws_input)
  {
    $lws_input = sanitize_text_field($lws_input);
    return $lws_input;
  }


  // Print the Section text and set default option values if none exist
  public function lws_print_section_info()
  {
    print '<p>Please Add New slides then use the shortcode [lightweight-slider-shortcode] to display the slideshow on your page.</p>';
    print '<p>To re-order the slides change their published date or use a separate plugin.</p>';

    if ( get_option( 'lws_slide_height' ) === false )
    update_option( 'lws_slide_height', '65' );

    if ( get_option( 'lws_delay' ) === false )
    update_option( 'lws_delay', '7' );

    if ( get_option( 'lws_transition' ) === false )
    update_option( 'lws_transition', '1' );

    if ( get_option( 'lws_animation' ) === false )
    update_option( 'lws_animation', 'slide' );

    if ( get_option( 'lws_controls' ) === false )
    update_option( 'lws_controls', 1 );

    if ( get_option( 'lws_pagination' ) === false )
    update_option( 'lws_pagination', 1 );
  }


  // Create an input for each setting
  public function lws_slide_height_callback()
  {
    $html = '<input type="number" min="1" max="100" required id="slide_height" name="lws_slide_height" value="' . esc_html(get_option('lws_slide_height')) . '" />';
    echo $html;
  }
  public function lws_delay_callback()
  {
    $html = '<input type="number" min="0" max="600" required id="delay" name="lws_delay" value="' . esc_html(get_option('lws_delay')) . '" />';
    $html .= ' <- To disable autoplay set delay to 0';
    echo $html;
  }
  public function lws_transition_callback()
  {
    $html = '<input type="number" min="1" max="600" required id="transition" name="lws_transition" value="' . esc_html(get_option('lws_transition')) . '" />';
    $html .= ' <- Must be less than the delay value';
    echo $html;
  }
  public function lws_animation_callback()
  {
    $lws_animation = get_option('lws_animation');
    ?>
    <select name="lws_animation">
      <option value="fade" <?php if ( $lws_animation == 'fade' ) echo 'selected="selected"'; ?>>Fade</option>
      <option value="slide" <?php if ( $lws_animation == 'slide' ) echo 'selected="selected"'; ?>>Slide</option>
      <option value="none" <?php if ( $lws_animation == 'none' ) echo 'selected="selected"'; ?>>None</option>
    </select>
    <?php
  }
  public function lws_controls_callback($args)
  {
    $html = '<input type="checkbox" id="controls" name="lws_controls" value="1" ' . checked(1, get_option('lws_controls'), false) . '/>';
    $html .= '<label for="controls"> '  . $args[0] . '</label>';
    echo $html;
  }
  public function lws_pagination_callback($args)
  {
    $html = '<input type="checkbox" id="pagination" name="lws_pagination" value="1" ' . checked(1, get_option('lws_pagination'), false) . '/>';
    $html .= '<label for="pagination"> '  . $args[0] . '</label>';
    echo $html;
  }

}


// Show the settings page
if( is_admin() )
$my_settings_page = new LightWeightSliderSettingsPage();


// Output slideshow from custom post data
add_shortcode('lightweight-slider-shortcode', 'lightweight_slider_function');
function lightweight_slider_function($type='lightweight_slider_function') {
  $args = array(
    'post_type' => 'lightweight-slider',
    'posts_per_page' => -1
  );
  $result = '<div class="lightweight-slider-wrapper">';

  // Add controls if required
  if ( get_option('lws_controls') ) {
    $result .= '<a href="#" class="lightweight-slider-next">&#10093</a>';
    $result .= '<a href="#" class="lightweight-slider-prev">&#10092</a>';
  };

  $result .= '<div id="lightweight-slider" class="lightweight-slider" data-delay="' . esc_html(get_option('lws_delay')) . '" data-transition="' . esc_html(get_option('lws_transition')) . '" data-animation="' . esc_html(get_option('lws_animation')) . '" style="padding-bottom:' . esc_html(get_option('lws_slide_height')) . '%;">';
  lws_ouput_posts($result, $args);
  $result .= '</div>';

  // Add pagination if required
  if ( get_option('lws_pagination') ) {
    $args = array(
      'post_type' => 'lightweight-slider',
      'posts_per_page' => -1
    );
    $result .= '<div class="pagination">';
    $lws_loop = new WP_Query($args);
    $currpostno = 0;
    while ($lws_loop->have_posts()) {
      $currpostno++;
      $lws_loop->the_post();
      if ($currpostno == 1) {
        $result .= '<div class="active">' . $currpostno . '</div>';
      } else {
        $result .= '<div>' . $currpostno . '</div>';
      }
    }
    $result .= '</div>';
  };

  $result .= '</div>';
  return $result;

}


// Add slides from lightweight-slider posts
function lws_ouput_posts(&$result, $args) {
  $currpostno = 0;
  $lws_loop = new WP_Query($args);
  global $post, $type;
  while ($lws_loop->have_posts()) {
    $currpostno++;
    $lws_loop->the_post();

    $the_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);
    $the_srcset = wp_get_attachment_image_srcset(get_post_thumbnail_id($post->ID), $type);
    $the_caption = get_post_custom_values('slide_caption', $post->ID);
    $the_link = get_post_custom_values('slide_link', $post->ID);
    if($currpostno == 1) {
      $result .= '<div class="active"><figure><p>' . esc_html(get_the_title()) . '</p>';
    } else {
      $result .= '<div class=""><figure><p>' . esc_html(get_the_title()) . '</p>';
    };
    $trimlink = trim($the_link[0]);
    if(isset($trimlink) === true && $trimlink === '') {
      // If no link, don't add one
    } else {
      $result .= '<a href="' . esc_url($trimlink) . '">';
    };
    $result .= '<img src="' . $the_src[0] . '" srcset="' . $the_srcset . '" data-thumb="' . $the_src[0] . '" alt=""/>';
    if(isset($trimlink) === true && $trimlink === '') {
      // If no link, don't add one
    } else {
      $result .= '</a>';
    };
    $result .= '<figcaption>' . $the_caption[0] . '</figcaption></figure></div>';
  }
}


// Add custom CSS to plugin admin page
add_action('admin_head', 'lws_custom_admin_css');

function lws_custom_admin_css() {
  echo '<style>
  input[name="slide_caption"], input[name="slide_link"] {
    width: 80%;
    padding: 0.4em;
    margin-left: 1em;
  }
  </style>';
}

?>
