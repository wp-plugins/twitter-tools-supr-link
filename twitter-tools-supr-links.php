<?php /*

**************************************************************************

Plugin Name:  Twitter Tools: su.pr Links
Plugin URI:   http://www.jonrogers.co.uk/su-pr-for-wordpress-twitter-tools/
Version:      0.1.3
Description:  Makes the links that <a href="http://wordpress.org/extend/plugins/twitter-tools/">Twitter Tools</a> posts to Twitter be API-created <a href="http://su.pr/">su.pr</a> links so you can track the number of clicks and such via your su.pr account. Requires PHP 5.2.0+. 
Author:       Jon Rogers
Author URI:   http://www.jonrogers.co.uk/

*************************************************************************
Apologies to Viper007Bond whose code I blatantly ripped!*/

class TwitterToolsSuprLinks {

	// Initalize the plugin by registering the hooks
	function __construct() {
		// This plugin requires PHP 5.2.0+ and WordPress 2.7+
		if ( !function_exists('json_decode') || !function_exists('wp_remote_retrieve_body') )
			return;

		// Load localization domain
		load_plugin_textdomain( 'twitter-tools-supr-links', false, '/twitter-tools-supr-links/localization' );

		// Register options
		add_option( 'viper_ttbl_login' );
		add_option( 'viper_ttbl_apikey' );

		// Register hooks
		add_action( 'admin_menu',               array(&$this, 'register_settings_page') );
		add_action( 'wp_ajax_viper_ttbl_check', array(&$this, 'ajax_authentication_check') );
		add_filter( 'whitelist_options',        array(&$this, 'whitelist_options') );
		add_filter( 'tweet_blog_post_url',      array(&$this, 'modify_url') );

		// Make sure the user has filled in their login and API key
		$login  = trim( get_option('viper_ttbl_login') );
		$apikey = trim( get_option('viper_ttbl_apikey') );
		if ( ( !$login || !$apikey ) && current_user_can('manage_options') )
			add_action( 'admin_notices',        array(&$this, 'settings_warn') );
	}


	// Register the settings page
	function register_settings_page() {
		add_options_page( __('Twitter Tools: su.pr Links', 'twitter-tools-supr-links'), __('Twitter Tools: su.pr', 'twitter-tools-supr-links'), 'manage_options', 'twitter-tools-supr-links', array(&$this, 'settings_page') );
	}


	// Whitelist the options to allow saving via options.php
	function whitelist_options( $whitelist_options ) {
		$whitelist_options['twitter-tools-supr-links'] = array( 'viper_ttbl_login', 'viper_ttbl_apikey' );

		return $whitelist_options;
	}


	// Display a notice telling the user to fill in their su.pr details
	function settings_warn() {
		echo '<div class="error"><p>' . sprintf( __( '<strong>Twitter Tools: su.pr Links:</strong> You must fill in your su.pr details on the <a href="%s">settings page</a> in order for this plugin to function.', 'vipers-video-quicktags' ), admin_url('options-general.php?page=twitter-tools-supr-links') ) . "</p></div>\n";
	}


	// Modify the URL being sent to Twitter by Twitter Tools
	function modify_url( $url ) {

		// Make sure the user has filled in their login and API key
		$login  = urlencode( strtolower( trim( get_option('viper_ttbl_login') ) ) );
		$apikey = urlencode( trim( get_option('viper_ttbl_apikey') ) );
		if ( empty($login) || empty($apikey) )
			return $url;

		// Tell su.pr to shorten the URL for us
		$response = wp_remote_retrieve_body( wp_remote_get( "http://su.pr/api/shorten?login={$login}&apiKey={$apikey}&longUrl=" . urlencode( $url ) ) );

		if ( empty($response) )
			return $url;

		// Decode the response from su.pr
		if ( !$response = json_decode( $response, true ) )
			return $url;

		if ( !isset($response['errorCode']) || 0 != $response['errorCode'] || empty($response['results']) || empty($response['results'][$url]) || empty($response['results'][$url]['shortUrl']) )
			return $url;

		if (strlen($response['results'][$url]['hash']) > 6)
			{

			return "http://".$response['results'][$url]['hash'];
			}
		else
			return $response['results'][$url]['shortUrl'];
		
	}


	// Settings page
	function settings_page() { ?>

<script type="text/javascript">
// <![CDATA[
	function viper_ttbl_ajax() {
		jQuery("#viper_ttbl_status").load("<?php echo admin_url('admin-ajax.php'); ?>?nocache=" + Math.random(), { action: "viper_ttbl_check", login: jQuery("#viper_ttbl_login").val(), apikey: jQuery("#viper_ttbl_apikey").val() });
	}
	jQuery(document).ready(function(){ viper_ttbl_ajax() });
	jQuery("body").change(function(){ viper_ttbl_ajax() }); // I couldn't get anything but "body" to work for some reason
// ]]>
</script>

<div class="wrap">
<?php screen_icon(); ?>
	<h2><?php _e( 'Twitter Tools: su.pr Links Settings', 'twitter-tools-supr-links' ); ?></h2>

	<form id="viper_ttbl_form" method="post" action="options.php">
<?php settings_fields('twitter-tools-supr-links'); ?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="viper_ttbl_login"><?php _e( 'su.pr Login', 'twitter-tools-supr-links' ); ?></label></th>
			<td><input type="text" name="viper_ttbl_login" id="viper_ttbl_login" value="<?php form_option('viper_ttbl_login'); ?>" class="regular-text" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="viper_ttbl_apikey"><?php _e( 'su.pr API Key', 'twitter-tools-supr-links' ); ?></label></th>
			<td>
				<input type="text" name="viper_ttbl_apikey" id="viper_ttbl_apikey" value="<?php form_option('viper_ttbl_apikey'); ?>" class="regular-text" />
				<span class="description"><?php printf( __( 'This can be found on your <a href="%s">account page</a>.', 'twitter-tools-supr-links' ), 'http://su.pr/settings/' ); ?></span>
			</td>
		</tr>
		<tr valign="top" class="hide-if-no-js">
			<th scope="row"><?php _e( 'API Status', 'twitter-tools-supr-links' ); ?></th>
			<td style="font-size:1em"><span id="viper_ttbl_status"><em>Checking...</em></span></td>
		</tr>
	</table>

	<p class="submit">
		<input type="submit" name="twitter-tools-supr-links-submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
</div>

<?php
	}


	// Check the authentication details via AJAX
	function ajax_authentication_check() {
		// Make sure the user has filled in their login and API key
		$login = $apikey = false;
		if ( !empty($_POST['login']) )
			$login  = urlencode( strtolower( trim( $_POST['login'] ) ) );
		if ( !empty($_POST['apikey']) )
			$apikey = urlencode( trim( $_POST['apikey'] ) );
		if ( empty($login) || empty($apikey) )
			exit();

		// Ask su.pr for details about a random shortened URL in order to test the authentication details
		$response = wp_remote_retrieve_body( wp_remote_get( "http://su.pr/api/expand?shortUrl=http://su.pr/2l5JSm&login={$login}&apiKey={$apikey}" ) );

		if ( empty($response) )
			exit( '<strong style="color:red">' . __('Failed to test credentials. Hmm.', 'twitter-tools-supr-links') . '</strong>' );

		// Decode the response from su.pr
		if ( !$response = json_decode( $response, true ) )
			exit( '<strong style="color:red">' . __('Failed to parse su.pr API response. Hmm.', 'twitter-tools-supr-links') . '</strong>' );

		if ( !isset($response['errorCode']) || 0 != $response['errorCode'] )
			exit( '<strong style="color:red">' . __('Your credentials are invalid. Please double-check them.', 'twitter-tools-supr-links') . '</strong>' );

		exit( '<strong style="color:green">' . __('Your credentials are valid.', 'twitter-tools-supr-links') . '</strong>' );
	}
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', 'TwitterToolsSuprLinks' ); function TwitterToolsSuprLinks() { global $TwitterToolsSuprLinks; $TwitterToolsSuprLinks = new TwitterToolsSuprLinks(); }

?>
