<?php
 // Settings Page Template
?>
<h1>Settings</h1>
<form method="post" action="../wp-content/plugins/mb-slider/settings.php">
 <?php
  settings_fields('mbslider_all_options');
  do_settings_sections( 'mbslider_all_options' );
 ?>
<label for="mbslider_test_option">Test Option</label><input type="text" name="mbslidertest_option" id="mbslider_test_option" value="<?php echo get_option('mbslider_test_option'); ?>"/>
<?php
  submit_button();
 ?>
</form>
