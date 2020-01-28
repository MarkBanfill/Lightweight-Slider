<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

delete_option('ls_slide_height');
delete_option('ls_delay');
delete_option('ls_transition');
delete_option('ls_animation');
delete_option('ls_controls');
delete_option('ls_pagination');

?>
