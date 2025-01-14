<?php
/* 
Plugin Name: Include Matomo Tracking, by Jonas Hellmann
Plugin URI: https://github.com/jonashellmann/include-matomo/
Description: This plugin includes Matomo into your Wordpress site
Version: 1.5.1
Author: Jonas Hellmann
Author URI: https://jonas-hellmann.de/en/
License: GPL3
*/

if( get_option('matomo_url') !== '' && get_option('matomo_site_id') !== '0' ) {
  add_action( 'wp_footer', 'include_matomo_script' );
}
 
function include_matomo_script() {
  echo "<!-- Include Matomo -->\n";
  echo "<script type='text/javascript'>\n";
  echo "  var _paq = _paq || [];\n";
  echo "  _paq.push(['trackPageView']);\n";
  echo "  _paq.push(['enableLinkTracking']);\n";
  echo "  (function() {\n";
  echo "    var u='//" . get_matomo_url() . "/';\n";
  echo "    _paq.push(['setTrackerUrl', u+'piwik.php']);\n";
  echo "    _paq.push(['setSiteId', '" . get_option('matomo_site_id') . "']);\n";
  if (get_option('matomo_disable_cookies', 'n') == 'y') {
    echo "    _paq.push(['disableCookies']);\n";
  }
  echo "    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];\n";
  echo "    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);\n";
  echo "  })();\n";
  echo "</script>\n";
  echo "<!-- End Matomo Code-->\n";
}

function get_matomo_url() {
  $matomo_url = get_option('matomo_url');
  if( substr_compare( $matomo_url, '/', -1, 1 ) == 0 ) {
    $matomo_url = substr( $matomo_url, 0, -1 );
  }
  if( substr_compare( $matomo_url, 'https://', 0, 8 ) == 0 ) {
    $matomo_url = substr( $matomo_url, 8 );
  }
  if( substr_compare( $matomo_url, 'http://', 0, 7 ) == 0 ) {
    $matomo_url = substr( $matomo_url, 7 );
  }
  return $matomo_url;
}

// TODO: Only include if this should be used
add_filter( 'the_permalink_rss', 'add_matomo_campaign_to_rss' );

function add_matomo_campaign_to_rss($guid) {
  global $post;
  $get_vars = array(
    'pk_campaign=' . urlencode( get_option('matomo_rss_campaign') ),
    'pk_source=' . urlencode( get_option('matomo_rss_source') )
  );
  return $guid . '?' . implode( '&', $get_vars );
}


add_action('admin_menu', 'include_matomo_menu');

function include_matomo_menu() {
    add_submenu_page('options-general.php', 'Include Matomo - Settings', 'Include Matomo', 'administrator', 'include-matomo-settings', 'include_matomo_settings_page');
}

add_action( 'admin_init', 'my_plugin_settings' );

function my_plugin_settings() {
  register_setting( 'include-matomo-settings-group', 'matomo_url' );
  register_setting( 'include-matomo-settings-group', 'matomo_site_id' );
  register_setting( 'include-matomo-settings-group', 'matomo_rss_campaign' );
  register_setting( 'include-matomo-settings-group', 'matomo_rss_source' );
  register_setting( 'include-matomo-settings-group', 'matomo_disable_cookies' );
}

function include_matomo_settings_page() { ?>
  <div class="wrap">
    <h2>Matomo Settings</h2>

    <form method="post" action="options.php">
     <?php settings_fields( 'include-matomo-settings-group' ); ?>
     <?php do_settings_sections( 'include-matomo-settings-group' ); ?>
   <table class="form-table">
    <tr valign="top">
      <th scope="row">Matomo URL</th>
      <td>
        <input type="text" name="matomo_url" value="<?php echo esc_attr( get_option('matomo_url') ); ?>" />
      </td>
    </tr>
                                  
    <tr valign="top">
      <th scope="row">Matomo Page ID</th>
      <td>
        <input type="number" name="matomo_site_id" value="<?php echo esc_attr( get_option('matomo_site_id') ); ?>" />
      </td>
    </tr>
    
    <tr valign="top">
      <th scope="row">Matomo RSS Campaign</th>
      <td>
        <input type="text" name="matomo_rss_campaign" value="<?php echo esc_attr( get_option('matomo_rss_campaign') ); ?>" />
      </td>
    </tr>
  
    <tr valign="top">
      <th scope="row">Matomo RSS Source</th>
      <td>
        <input type="text" name="matomo_rss_source" value="<?php echo esc_attr( get_option('matomo_rss_source') ); ?>" />
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Matomo Disable Cookies? (y/n)</th>
      <td>
        <input type="text" name="matomo_disable_cookies" value="<?php echo esc_attr( get_option('matomo_disable_cookies', 'n') ); ?>" />
      </td>
    </tr>
  </table>
  
  <?php submit_button(); ?>

 </form>
   </div> <?php
}

?>
