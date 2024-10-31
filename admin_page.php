<?php

// search and replace 'parseshortcode_' with something.


function parseshortcode_create_menu() {
  parseshortcode_add_submenu_page('options-general.php','Parse Shortcodes Options', 'Parse Shortcodes Options', 'manage_options', "parseshortcode-options");
}


function parseshortcode_api_init(){

	parseshortcode_add_settings_section('parseshortcode_setting_section', '', 'Note: Smilies will only be converted if the <code>Convert emoticons like :-) and :-P to graphics on display</code> option is checked in the Settings->Writing option page', "parseshortcode-options");
	parseshortcode_add_settings_field('mc_ps_parse', 'Should shortcodes and smilies be parsed?', 'select', "parseshortcode-options", 'parseshortcode_setting_section', array (
	 "shortcode,smilies" => "parse smilies and shortcodes",
	 "shortcode" => "parse shortcodes",
	 "smilies" => "parse emoticons",
	 "" => "Don't parse smilies or shortcodes",
	));

}


// CHANGE THE LINES BELOW AT YOUR OWN RISK
// THE SKY WILL FALL ON YOUR HEAD

add_action('admin_menu', 'parseshortcode_create_menu');
add_action('admin_init', 'parseshortcode_api_init');

if ( ! function_exists( 'parseshortcode_plugin_options' ) ){
function parseshortcode_plugin_options() {
	global $parseshortcode_page_title;
	global $parseshortcode_page_parent;
	$page = $_GET["page"];
	echo '<div class="wrap">';
	echo "<div class='icon32 icon_$page' id='icon-options-general'><br/></div>";
	echo '<h2>' . $parseshortcode_page_title[$page] . '</h2>';
	echo '</div>';
////	echo '<table class="form-table"><tr><td>';
	echo "<br /><a href='" .get_bloginfo("url"). "/wp-admin/plugin-install.php?tab=search&mc_find_plugins=TRUE'>" .__("Find more plugins by this author"). "</a>";
////	echo "</td></tr></table>";
	echo '<form action="options.php" method="post">';
	settings_fields( $page );
	do_settings_sections($page);
	echo '<br /><input type="submit" class="button-primary" value="' .  __('Save Changes') . '" />';
	echo '</form>';
}
}

if ( ! function_exists( 'parseshortcode_add_submenu_page' ) ){
function parseshortcode_add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function="", $icon_url="", $position=""){
	global $parseshortcode_page_title;
	global $parseshortcode_page_parent;
////	$temp_array = array("".$menu_slug => $page_title);
////	$parseshortcode_page_title = array_merge($parseshortcode_page_title, $temp_array);
	$parseshortcode_page_title[$menu_slug] = $page_title;
	$parent_slug = explode(",", $parent_slug);
	foreach ($parent_slug as $parent_slugX) {
		if($parent_slugX) {
			add_submenu_page($parent_slugX, $page_title, $menu_title, $capability, $menu_slug, "parseshortcode_plugin_options");
		} else {
			add_menu_page($page_title, $menu_title, $capability, $menu_slug, "parseshortcode_plugin_options", $icon_url, $position);
		}
	}
}
}

if ( ! function_exists( 'parseshortcode_add_settings_section' ) ){
function parseshortcode_add_settings_section($id, $title, $text, $pageX) {
	global $parseshortcode_setting_section_text;
	$parseshortcode_setting_section_text[$id] = $text;
	$pageX = explode(",", $pageX);
	foreach ($pageX as $page) {
		add_settings_section($id, $title, "parseshortcode_section_callback_function", $page);
	}
}
}


if ( ! function_exists( 'parseshortcode_add_settings_field' ) ){
function parseshortcode_add_settings_field($idSettingName, $title, $type, $pageX, $section, $args	){
	global $color_picker_count;
	if ($type=="colorpicker"){
		$color_picker_count++;
		if (!isset($color_picker_count)){
			$color_picker_count = 0;
		}
	}
	$args[] = $idSettingName;
	$args[] = $type;
	$pageX = explode(",", $pageX);
	foreach ($pageX as $page) {
		add_settings_field($idSettingName, $title, 'parseshortcode_field_callback_function', $page, $section, $args	);
		register_setting($page,$idSettingName);
	}
}
}

if ( ! function_exists( 'parseshortcode_section_callback_function' ) ){
function parseshortcode_section_callback_function($x) {
	global $parseshortcode_setting_section_text;
	echo $parseshortcode_setting_section_text[$x["id"]];
///	settings_fields( $x["id"] );
}
}

if ( ! function_exists( 'parseshortcode_field_callback_function' ) ){
function parseshortcode_field_callback_function($x){
	$type = array_pop($x);
	$id = array_pop($x);
	makeAdminOption($x, $id, $type);
}
}



if ( ! function_exists( 'makeAdminOption' ) ){

function makeAdminOption($vals, $my_field, $type) {
	global $color_picker_count;
	$tag = "input";
	$option_test = get_option($my_field);
	if ($type=="checkbox"){
		echo "<input type='hidden' value='' name='$my_field' />";

	}
	elseif ($type=="dropdown_pages"){
		wp_dropdown_pages(array('name' => $my_field, 'selected' => $option_test));
		return;
	}
	elseif ($type=="dropdown_posts"){
		wp_dropdown_pages(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => $vals));
		return;
	}
	elseif ($type=="dropdown_author"){
		wp_dropdown_users(array('name' => $my_field, 'selected' => $option_test));
		return;
	}
	elseif ($type=="dropdown_terms"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => $vals));
		return;
	}
	elseif ($type=="dropdown_link" || $type=="dropdown_links"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test, 'taxonomy' => 'link_category'));
		return;
	}
	elseif ($type=="dropdown_categories" || $type=="dropdown_cat" || $type=="dropdown_cats"){
		wp_dropdown_categories(array('name' => $my_field, 'selected' => $option_test));
		return;
	}elseif ($type=="textarea"){
		echo "<textarea class='$my_field' name='$my_field'>" . $option_test . "</textarea>";
		return;
	}
	elseif ($type=="text" || $type=="password"){
		echo "<input type='$type' name='$my_field' value='$option_test' />";
		return;
	}
	elseif ($type=="colorpicker"){
		echo " <div id='colorpicker$color_picker_countX'></div>";
		echo "<input type='$text' id='color$color_picker_countX' name='$my_field' value='$option_test'/>" . $option_test, $my_field;
		$color_picker_count++;
		return;
	}
	elseif ($type=="select"){
		echo "<select id='$my_field' name='$my_field'>";
		$tag = "option";
	}

	foreach ($vals as $stateKey => $stateValue) {
		$is_selected = "";
		$option_test = get_option($my_field);
		if ($option_test== $stateKey) {
			if ($type == "radio" || $type == "checkbox"){
				$is_selected = "checked='checked'";
			} else {
				$is_selected = "selected='selected'";
			}
		}
		if ($type == "radio" || $type == "checkbox"){
			$is_selected .= "type='$type' name = '$my_field' id='" . $type . $stateKey . $stateValue . "' /";
			$labelStart = " &nbsp;<label for='"	.$type . $stateKey . $stateValue.	"'>";
			$labelEnd   = "</label> &nbsp;";
		}
		echo "<$tag value='$stateKey' $is_selected>";
		echo $labelStart . $stateValue . $labelEnd;	
		if ($type != "radio" && $type != "checkbox"){
			echo "</$tag>";	
		} elseif ($type == "checkbox") {
			return;
		}
	}
	if ($type=="select"){
		echo "</select>";
	}
	return $my_string;
}
}

?>