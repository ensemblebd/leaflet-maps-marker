<?php
/*
    Checks for incompatible plugins and settings - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'compatibility-checks.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
require_once(ABSPATH . WPINC . DIRECTORY_SEPARATOR . "pluggable.php");
$lmm_options = get_option( 'leafletmapsmarker_options' ); //info: required for bing maps api key check

//info: check if bing maps api key is defined
if (( (($lmm_options['standard_basemap'] == 'bingaerial') || ($lmm_options['standard_basemap'] == 'bingaerialwithlabels') || ($lmm_options['standard_basemap'] == 'bingroad')) 
|| ((isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1 )) 
|| ((isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1 )) 
|| ((isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1 )) 
) && ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] == NULL ) 
)) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you enabled support for bing maps but did not provide an API key. Please visit <a href="http://www.mapsmarker.com/bing-maps" target="_blank">http://www.mapsmarker.com/bing-maps</a> for info on how to get a free bing maps API key!','lmm') . '</strong></div></p>';
}
//info: check if shadow image exists (for issues from moving dev to prod instances)
$shadow_icon_url = $lmm_options['defaults_marker_icon_shadow_url'];
$defaults_marker_icon_url = $lmm_options['defaults_marker_icon_url'];
$defaults_marker_icon_dir = $lmm_options['defaults_marker_icon_dir'];

function remoteFileExists($url) {
	$loaded_extensions = get_loaded_extensions();
	$loaded_extensions = array_flip($loaded_extensions);
	$ret = false;
	if ( isset($loaded_extensions['curl']) ) {
		$curl = curl_init($url);
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
		curl_setopt($curl, CURLOPT_USERAGENT, $agent);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = curl_exec($curl);
		if ($result !== false) {
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
			if ($statusCode == 200) {
				$ret = true;   
			}
		}
		curl_close($curl);
	} else {
		$ret = true;
	}
	return $ret;
}
$shadow_icon_url_exists = remoteFileExists($shadow_icon_url);
if ( ($shadow_icon_url != NULL) && (!$shadow_icon_url_exists) ) {
    echo '<div class="error" style="padding:10px;"><strong>' . sprintf(__('Leaflet Maps Marker Warning: the setting for the marker shadow url (%1s) seems to be invalid. This can happen when you moved your WordPress installation from one server to another one.<br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Shadow URL". If you do not know which values to enter, please <a href="%3s">reset all plugins options to their defaults</a>', 'lmm'), $defaults_marker_icon_url, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#mapdefaults-section5', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#reset') . '</strong></div>';
} 
//info: check marker icon url + dir
$defaults_marker_icon_url_exists = remoteFileExists($defaults_marker_icon_url . '/readme-icons.txt');
if ( ! $defaults_marker_icon_url_exists ) {
    echo '<div class="error" style="padding:10px;"><strong>' . sprintf(__('Leaflet Maps Marker Warning: the setting for your marker icon url (%1s) seems to be invalid. This can happen when you moved your WordPress installation from one server to another one.<br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Icons URL". If you do not know which values to enter, please <a href="%3s">reset all plugins options to their defaults</a>', 'lmm'), $defaults_marker_icon_url, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#mapdefaults-section5', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#reset') . '<br/>' . __('Please note that the file readme-icons.txt within this directory is used for this check, so please make sure, that this file is available!','lmm') . '</strong></div>';
}
if ( ! file_exists($defaults_marker_icon_dir . DIRECTORY_SEPARATOR . 'readme-icons.txt') ) {
    echo '<div class="error" style="padding:10px;"><strong>' . sprintf(__('Leaflet Maps Marker Warning: the setting for your the marker icon directory (%1s) seems to be invalid. This can happen when you moved your WordPress installation from one server to another one.<br/>Please navigate to <a href="%2s">Settings / Map Defaults / "Default values for marker icons"</a> and update the option "Icons directory". If you do not know which values to enter, please <a href="%3s">reset all plugins options to their defaults</a>', 'lmm'), $defaults_marker_icon_dir, LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#mapdefaults-section5', LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#reset') . '<br/>' . __('Please note that the file readme-icons.txt within this directory is used for this check, so please make sure, that this file is available!','lmm') . '</strong></div>';
}
//info: plugin JavaScript to Footer
if (is_plugin_active('footer-javascript/footer-javascript.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin Javascript to Footer which is incompatible with Leaflet Maps Marker and causing maps to break. Please deactivate this plugin in order to be able to use Leaflet Maps Marker.','lmm') . '</strong></div></p>';
}
//info: plugin Lazy Load
if (is_plugin_active('lazy-load/lazy-load.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin Lazy Load which is incompatible with Leaflet Maps Marker and causing maps to break. Please deactivate this plugin in order to be able to use Leaflet Maps Marker.','lmm') . '</strong></div></p>';
}
//info: plugin jQuery Colorbox
if (is_plugin_active('jquery-colorbox/jquery-colorbox.php') ) {
	$lmm_jquery_colorbox_options = get_option( 'jquery-colorbox_settings' );
	if ($lmm_jquery_colorbox_options['autoColorbox'] == TRUE) { 
		echo '<p><div class="error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin jQuery Colorbox which is causing maps to break!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "Settings" / "jQuery Colorbox" in your WordPress admin menu<br/>2. Uncheck the setting "Automate jQuery Colorbox for all images in pages, posts and galleries:"<br/>3. check the setting "Automate jQuery Colorbox for images in WordPress galleries only:" instead<br/>4. save changes<br/><br/>This message will disappear automatically when the jQuery Colorbox option was updated.','lmm') . '</div></p>';
	} 
}
//info: plugin cformsII
if (is_plugin_active('cforms/cforms.php') ) {
	$lmm_cforms_options = get_option( 'cforms_settings' );
	if ($lmm_cforms_options['global'][ 'cforms_show_quicktag_js' ] == FALSE) { 
		echo '<p><div class="error" style="padding:10px;">' . __('<strong>Warning: you are using the plugin cformsII which is causing the TinyMCE editor to break when creating new maps!</strong><br/><br/>Here is how to fix this:<br/>1. click on to "cformsII" / "Global Settings" in your WordPress admin menu<br/>2. open the tab "WP Editor Button support"<br/>3. check the option "Fix TinyMCE error"<br/>4. save changes<br/><br/>If you do not see this option in your settings, please upgrade to the latest version first (this has to be done manually - see plugin website http://www.deliciousdays.com/cforms-plugin/ for details)<br/><br/>This message will disappear automatically when the cformsII option "Fix TinyMCE error" is checked.','lmm') . '</div></p>';
	} 
}
//info: plugin WP Google Analytics
if (is_plugin_active('wp-google-analytics/wp-google-analytics.php') ) {
	echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the outdated plugin WP Google Analytics which is incompatible with Leaflet Maps Marker. Please update to a more current Google analytics plugin like http://wordpress.org/extend/plugins/google-analytics-for-wordpress/','lmm') . '</strong></div></p>';
}
//info: plugin Better WordPress Minify
if (is_plugin_active('bwp-minify/bwp-minify.php') ) {
	$lmm_bwpminify_options = get_option( 'bwp_minify_general' );
	if ($lmm_bwpminify_options['enable_min_js'] == 'yes') { 
		if (strpos($lmm_bwpminify_options['input_ignore'], 'leafletmapsmarker') === false)  { 
			echo '<p><div class="error" style="padding:10px;"><strong>' . __('Warning: you are using the plugin "Better WordPress Minify" which can cause Leaflet Maps Marker to break if the option "Minify JS files automatically?" is active. Please disable this option (Settings / BWP Minify) or add <strong>leafletmapsmarker</strong> to the form field "Scripts to be ignored (not minified)"','lmm') . '</strong></div></p>';
		}
	}
}
?>