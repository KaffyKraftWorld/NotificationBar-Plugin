<?php
/*
Plugin Name: Notification Bar
Plugin URI: /#
Description: Adds a customizable notification bar at the top or bottom of your website.
Version: 1.0.0
Author: Kafayat Faniran
Author URI: https://www.linkedin.com/in/kafayatfaniran
License: GPL2
*/

if(! defined( 'ABSPATH' )) {
  die('Fire off!');
}

register_activation_hook(__FILE__, 'notification_bar_activate');
register_deactivation_hook(__FILE__, 'notification_bar_deactivate');

function notification_bar_activate() {
    // .
}

function notification_bar_deactivate() {
    // .
}

class NotificationBar {

  public function __construct() {
      
      add_action('admin_init', array($this, 'register_settings'));
      add_action('admin_menu', array($this, 'add_settings_page'));
      add_action('wp_footer', array($this, 'display_notification_bar'));
  }

  public function register_settings() {
      register_setting('general', 'notification_bar_message', 'sanitize_text_field');
      register_setting('general', 'notification_bar_position', array(
          'type' => 'string',
          'default' => 'top',
          'sanitize_callback' => array($this, 'sanitize_position'),
      ));
      register_setting('general', 'notification_bar_background_color', array(
          'type' => 'string',
          'default' => '#333333',
          'sanitize_callback' => 'sanitize_hex_color',
      ));
      register_setting('general', 'notification_bar_text_color', array(
          'type' => 'string',
          'default' => '#ffffff',
          'sanitize_callback' => 'sanitize_hex_color',
      ));
      register_setting('general', 'notification_bar_link_color', array(
          'type' => 'string',
          'default' => '#ffffff',
          'sanitize_callback' => 'sanitize_hex_color',
      ));
      register_setting('general', 'notification_bar_duration', array(
          'type' => 'number',
          'default' => 5000,
          'sanitize_callback' => 'absint',
      ));
  }

  // Adding the settings page link in the admin menu
  public function add_settings_page() {
      add_options_page(
          'Notification Bar Settings',
          'Notification Bar',
          'manage_options',
          'notification-bar',
          array($this, 'render_settings_page')
      );
  }

    // Rendering the settings page content
    public function render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Notification Bar Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('general');
            do_settings_sections('general');
            submit_button();
            ?>
            <h2>Notification Bar Customization</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Notification Message</th>
                    <td>
                        <textarea id="notification_bar_message" name="notification_bar_message" rows="5" cols="50"><?php echo esc_textarea(get_option('notification_bar_message')); ?></textarea>
                        <p class="description">Enter the notification message to be displayed on the notification bar.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notification Bar Position</th>
                    <td>
                        <select name="notification_bar_position" id="notification_bar_position">
                            <option value="top" <?php selected('top', get_option('notification_bar_position', 'top')); ?>>Top</option>
                            <option value="bottom" <?php selected('bottom', get_option('notification_bar_position', 'top')); ?>>Bottom</option>
                        </select>
                        <p class="description">Choose whether the notification bar should appear at the top or bottom of the website.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td>
                        <input type="color" name="notification_bar_background_color" value="<?php echo esc_attr(get_option('notification_bar_background_color', '#333333')); ?>">
                        <p class="description">Select the background color of the notification bar.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td>
                        <input type="color" name="notification_bar_text_color" value="<?php echo esc_attr(get_option('notification_bar_text_color', '#ffffff')); ?>">
                        <p class="description">Select the text color of the notification bar.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Link Color</th>
                    <td>
                        <input type="color" name="notification_bar_link_color" value="<?php echo esc_attr(get_option('notification_bar_link_color', '#ffffff')); ?>">
                        <p class="description">Select the link color of the notification bar.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Display Duration (in milliseconds)</th>
                    <td>
                        <input type="number" name="notification_bar_duration" value="<?php echo esc_attr(get_option('notification_bar_duration', 5000)); ?>" step="100">
                        <p class="description">Set the display duration of the notification bar in milliseconds.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

  public function sanitize_position($position) {
      return ($position === 'bottom') ? 'bottom' : 'top';
  }

  // Displaying the notification bar in the footer
  public function display_notification_bar() {
      $message = get_option('notification_bar_message');
      $position = get_option('notification_bar_position', 'top');
      $bg_color = get_option('notification_bar_background_color', '#333333');
      $text_color = get_option('notification_bar_text_color', '#ffffff');
      $link_color = get_option('notification_bar_link_color', '#ffffff');
      $duration = get_option('notification_bar_duration', 5000);

      if (!empty($message)) {
          ?>
          <style>
              .notification-bar {
                  position: fixed;
                  <?php echo $position; ?>: 0;
                  left: 0;
                  right: 0;
                  background-color: <?php echo $bg_color; ?>;
                  color: <?php echo $text_color; ?>;
                  padding: 10px;
                  text-align: center;
                  z-index: 9999;
              }
              .notification-bar a {
                  color: <?php echo $link_color; ?>;
                  text-decoration: underline;
              }
          </style>
          <div class="notification-bar">
              <?php echo wp_kses_post($message); ?>
          </div>
          <script>
              setTimeout(function() {
                  document.querySelector('.notification-bar').style.display = 'none';
              }, <?php echo absint($duration); ?>);
          </script>
          <?php
      }
  }
}

new NotificationBar();