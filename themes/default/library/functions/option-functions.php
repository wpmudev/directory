<?php
function _g($str)
{
return __($str, 'option-page');
}

$themename = "Directory";
$themeversion = "1";
$shortname = "dev";
$shortprefix = "_directory_";
/* get pages so can set them */
$dev_pages_obj = get_pages();
$dev_pages = array();
foreach ($dev_pages_obj as $dev_cat) {
	$dev_pages[$dev_cat->ID] = $dev_cat->post_name;
}
$pages_tmp = array_unshift($dev_pages, "Select a page:");
/* end of get pages */
/* get categories so can set them */
$dev_categories_obj = get_categories('hide_empty=0');
$dev_categories = array();
foreach ($dev_categories_obj as $dev_cat) {
	$dev_categories[$dev_cat->cat_ID] = $dev_cat->category_nicename;
}
$categories_tmp = array_unshift($dev_categories, "Select a category:");
/* end of get categories */

/* start of theme options */

$options = array (
	array("name" => __("Show custom header", TEMPLATE_DOMAIN),
		"description" => __("You can show or hide the custom header, the default is off", TEMPLATE_DOMAIN),
		"id" => $shortname . $shortprefix . "customheader_on",	     	
		"inblock" => "slideone",
	    "type" => "select",
		"std" => "Show",
		"options" => array("no", "yes")),

array(
	"name" => __("Turn simple SEO on?", TEMPLATE_DOMAIN),
	"description" => __("Simple SEO allows you to set a global title, description and keywords - default is no and off", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "seo_theme",
	"inblock" => "seo",
	"type" => "select",
	"std" => "Select",
	"options" => array("no", "yes")
),

array(
	"name" => __("Enter site global title", TEMPLATE_DOMAIN),
	"description" => __("If you leave title blank it will use the built in title for WordPress or BuddyPress", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "seo_title",
	"inblock" => "seo",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter site global description", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "seo_description",
	"inblock" => "seo",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter site global keywords", TEMPLATE_DOMAIN),
	"description" => __("Keywords must be seperated by a comma ie; keywordone, keywordtwo, keywordthree", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "seo_keywords",
	"inblock" => "seo",
	"type" => "textarea",
	"std" => "",
),

array(
	"name" => __("Enter google analytics code if using", TEMPLATE_DOMAIN),
	"description" => __("Just paste your full google code right here and it will be inserted before your /body tag", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "google",
	"inblock" => "seo",
	"type" => "textarea",
	"std" => "",
),

);

function directory_admin_panel() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
	if ( isset($_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
	?>
	<div id="options-panel">
	<form action="" method="post">

	  <div id="sbtabs">
	  <div class="tabmc">
	  <ul class="ui-tabs-nav" id="tabm">
	 	<li class="first ui-tabs-selected"><a href="#seo"><?php _e("SEO",TEMPLATE_DOMAIN); ?></a></li>
		  </ul>
		  </div>

		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="seo">
		<li>

		<h2><?php _e("SEO", TEMPLATE_DOMAIN) ?></h2>


		<?php $value_var = 'seo'; foreach ($options as $value) { ?>

		<?php if ((isset($value['inblock']) == $value_var) && ($value['type'] == "text")) { // setting ?>

		<div class="tab-option">
		<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
				if (isset($value['description'])){
			echo $value['description']; }
			?></span></div>
		<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
		</div>

		<?php } elseif ((isset($value['inblock']) == $value_var) && ($value['type'] == "textarea")) { // setting ?>


		<div class="tab-option">
		<?php
		$valuex = $value['id'];
		$valuey = stripslashes($valuex);
		$video_code = get_option($valuey);
		?>
		
		<div class="description"><?php echo $value['name']; ?><br /><span><?php 
			if (isset($value['description'])){
		echo $value['description']; }
		?></span></div>
		<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
		</textarea></p></div>
		</div>


		<?php } elseif ((isset($value['inblock']) == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

		<?php $i == $i++ ; ?>

		<div class="tab-option">
		<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
				if (isset($value['description'])){
			echo $value['description']; }
			?></span></div>
		<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
		</div>

		<?php } elseif ((isset($value['inblock']) == $value_var) && ($value['type'] == "select") ) { // setting ?>

		<div class="tab-option">
		<div class="description"><?php echo $value['name']; ?><br /><span><?php echo $value['description']; ?></span></div>
		<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
		<?php foreach ($value['options'] as $option) { ?>
		<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
		<?php } ?>
		</select>
		</p>
		</div>
		</div>

		<?php } ?>
		<?php } ?>
		</li></ul>

		</div>
		</div>


		<div id="submitsection">

			<div class="submit">
			<h2><?php _e("Click this to save your theme options", TEMPLATE_DOMAIN) ?></h2>
		<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options',TEMPLATE_DOMAIN)); ?>" />
		<input type="hidden" name="action" value="save" />
		</div>
		</div>
		</div>
		</form>



		<form method="post">
		<div id="resetsection">
		<div class="submit">
			<h2><?php _e("Clicking this will reset all theme options - use with caution", TEMPLATE_DOMAIN) ?></h2>
		<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options',TEMPLATE_DOMAIN)); ?>" />
		<input type="hidden" name="action" value="reset" />
		</div>
		</div>
		</form>


		</div>

		<?php
		}
	
$options3 = array (

array(
	"name" => __("Choose your body font", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "body_font",
	"type" => "select",
	"inblock" => "fonts",
	"std" => "Arial, sans-serif",
				"options" => array(
	     			              "Arial, sans-serif",
									"Cantarell, arial, serif",
									"Cardo, arial, serif",
								    "Courier New, Courier, monospace",
									"Crimson Text, arial, serif",
									"Droid Sans, arial, serif",
									"Droid Serif, arial, serif",
						            "Garamond, Georgia, serif",
									"Georgia, arial, serif",
						            "Helvetica, Arial, sans-serif",
									"IM Fell SW Pica, arial, serif",
									"Josefin Sans Std Light, arial, serif",
									"Lobster, arial, serif",
									"Lucida Sans Unicode, Lucinda Grande, sans-serif",
									"Molengo, arial, serif",
									"Neuton, arial, serif",
									"Nobile, arial, serif",
									"OFL Sorts Mill Goudy TT, arial, serif",
									"Old Standard TT, arial, serif",
									"Reenie Beanie, arial, serif",
									"Tahoma, sans-serif",
									"Tangerine, arial, serif",
						            "Trebuchet MS, sans-serif",
						            "Verdana, sans-serif",
									"Vollkorn, arial, serif",
									"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Choose your header font", TEMPLATE_DOMAIN),
	"description" => __("We include google font directory fonts you can <a href='http://code.google.com/webfonts'>view here</a> ", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "header_font",
	"type" => "select",
	"inblock" => "fonts",
	"std" => "Arial, sans-serif",
				"options" => array(
	            "Arial, sans-serif",
				"Cantarell, arial, serif",
				"Cardo, arial, serif",
			    "Courier New, Courier, monospace",
				"Crimson Text, arial, serif",
				"Droid Sans, arial, serif",
				"Droid Serif, arial, serif",
	            "Garamond, Georgia, serif",
				"Georgia, arial, serif",
	            "Helvetica, Arial, sans-serif",
				"IM Fell SW Pica, arial, serif",
				"Josefin Sans Std Light, arial, serif",
				"Lobster, arial, serif",
				"Lucida Sans Unicode, Lucinda Grande, sans-serif",
				"Molengo, arial, serif",
				"Neuton, arial, serif",
				"Nobile, arial, serif",
				"OFL Sorts Mill Goudy TT, arial, serif",
				"Old Standard TT, arial, serif",
				"Reenie Beanie, arial, serif",
				"Tahoma, sans-serif",
				"Tangerine, arial, serif",
	            "Trebuchet MS, sans-serif",
	            "Verdana, sans-serif",
				"Vollkorn, arial, serif",
				"Yanone Kaffeesatz, arial, serif"
	            )
),

array(
	"name" => __("Choose your feature wrapper background colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "feature_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header background colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "header_background_colour",
	"inblock" => "layout",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your body font colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "font_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "link_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link hover colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "link_hover_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your link visited colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "link_visited_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your header text shadow colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "header_shadow_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "feature_text_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your feature text shadow colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "feature_text_shadow_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your site header text colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "site_header_colour",
	"inblock" => "text",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation background colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation text shadow colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_shadow_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover text colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_hover_text_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation hover background colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_hover_background_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),

array(
	"name" => __("Choose your navigation border colour", TEMPLATE_DOMAIN),
	"id" => $shortname . $shortprefix . "nav_border_colour",
	"inblock" => "navigation",
	"std" => "",
	"type" => "colorpicker"),
);


function directory_custom_style_admin_panel() {

		global $options, $options2, $options3, $bp_existed, $multi_site_on;
		$themename = "Directory";
		if ( isset($_REQUEST['saved3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
		if ( isset($_REQUEST['reset3'] )) echo '<div id="message" class="updated fade"><p><strong>'. $themename . __(' settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
		?>

		<div id="options-panel">
		<form action="" method="post">

		  <div id="sbtabs">
		  <div class="tabmc">
		  <ul class="ui-tabs-nav" id="tabm">
		  <li class="first ui-tabs-selected"><a href="#fonts"><?php _e("Fonts",TEMPLATE_DOMAIN); ?></a></li>
		
		  <li class=""><a href="#layout"><?php _e("Layout Colours",TEMPLATE_DOMAIN); ?></a></li>
		
		  <li class=""><a href="#text"><?php _e("Text Colours",TEMPLATE_DOMAIN); ?></a></li>
		
		  <li class=""><a href="#navigation"><?php _e("Navigation Colours",TEMPLATE_DOMAIN); ?></a></li>
		  </ul>
		</div>


		<div class="tabc">


		<ul style="" class="ui-tabs-panel" id="fonts">
		<li>
			<h2><?php _e("Fonts", TEMPLATE_DOMAIN) ?></h2>

			<?php $value_var = 'fonts'; foreach ($options3 as $value) { ?>

			<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

			<div class="tab-option">
			<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
					if (isset($value['description'])){
				echo $value['description']; }
				?></span></div>
			<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
			</div>

			<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


			<div class="tab-option">
			<?php
			$valuex = $value['id'];
			$valuey = stripslashes($valuex);
			$video_code = get_option($valuey);
			?>
			<div class="description"><?php echo $value['name']; ?><br /><span><?php 
				if (isset($value['description'])){
			echo $value['description']; }
			?></span></div>
			<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
			</textarea></p></div>
			</div>


			<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

			<?php $i == $i++ ; ?>

			<div class="tab-option">
			<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
					if (isset($value['description'])){
				echo $value['description']; }
				?></span></div>
			<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
			</div>

			<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

			<div class="tab-option">
			<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
					if (isset($value['description'])){
				echo $value['description']; }
				?></span></div>
			<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
			<?php foreach ($value['options'] as $option) { ?>
			<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
			<?php } ?>
			</select>
			</p>
			</div>
			</div>

			<?php } ?>
			<?php } ?>
		</li>
		</ul>
			<ul style="" class="ui-tabs-panel" id="layout">
			<li>
				<h2><?php _e("Layout Colours", TEMPLATE_DOMAIN) ?></h2>

				<?php $value_var = 'layout'; foreach ($options3 as $value) { ?>

				<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

				<div class="tab-option">
				<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
				<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
				</div>

				<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


				<div class="tab-option">
				<?php
				$valuex = $value['id'];
				$valuey = stripslashes($valuex);
				$video_code = get_option($valuey);
				?>
				<div class="description"><?php echo $value['name']; ?><br /><span><?php 
					if (isset($value['description'])){
				echo $value['description']; }
				?></span></div>
				<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
				</textarea></p></div>
				</div>


				<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

				<?php $i = ""; $i == $i++ ; ?>

				<div class="tab-option">
				<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
				<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
				</div>

				<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

				<div class="tab-option">
				<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
				<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
				<?php foreach ($value['options'] as $option) { ?>
				<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
				<?php } ?>
				</select>
				</p>
				</div>
				</div>

				<?php } ?>
				<?php } ?>
			</li>
			</ul>
				<ul style="" class="ui-tabs-panel" id="text">
				<li>
					<h2><?php _e("Text colours", TEMPLATE_DOMAIN) ?></h2>

					<?php $value_var = 'text'; foreach ($options3 as $value) { ?>

					<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


					<div class="tab-option">
					<?php
					$valuex = $value['id'];
					$valuey = stripslashes($valuex);
					$video_code = get_option($valuey);
					?>
					<div class="description"><?php echo $value['name']; ?><br /><span><?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
					<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
					</textarea></p></div>
					</div>


					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

					<?php $i == $i++ ; ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span><?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
					<?php foreach ($value['options'] as $option) { ?>
					<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php } ?>
					</select>
					</p>
					</div>
					</div>

					<?php } ?>
					<?php } ?>
				</li>
				</ul>
				
				<ul style="" class="ui-tabs-panel" id="navigation">
				<li>
					<h2><?php _e("Navigation Colours", TEMPLATE_DOMAIN) ?></h2>

					<?php $value_var = 'navigation'; foreach ($options3 as $value) { ?>

					<?php if (($value['inblock'] == $value_var) && ($value['type'] == "text")) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo stripslashes($value['std']); } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "textarea")) { // setting ?>


					<div class="tab-option">
					<?php
					$valuex = $value['id'];
					$valuey = stripslashes($valuex);
					$video_code = get_option($valuey);
					?>
					<div class="description"><?php echo $value['name']; ?><br /><span><?php 
						if (isset($value['description'])){
					echo $value['description']; }
					?></span></div>
					<div class="input-option"><p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?>
					</textarea></p></div>
					</div>


					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "colorpicker") ) { // setting ?>

					<?php $i = ""; $i == $i++ ; ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><input class="color {required:false,hash:true}" name="<?php echo $value['id']; ?>" id="colorpickerField<?php echo $i; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p></div>
					</div>

					<?php } elseif (($value['inblock'] == $value_var) && ($value['type'] == "select") ) { // setting ?>

					<div class="tab-option">
					<div class="description"><?php echo $value['name']; ?><br /><span>	<?php 
							if (isset($value['description'])){
						echo $value['description']; }
						?></span></div>
					<div class="input-option"><p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
					<?php foreach ($value['options'] as $option) { ?>
					<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php } ?>
					</select>
					</p>
					</div>
					</div>

					<?php } ?>
					<?php } ?>
				</li>
				</ul>
	</div>
	</div>



	<div id="submitsection">
		
		<div class="submit">
		<h2><?php _e("Click this to save your theme options", TEMPLATE_DOMAIN) ?></h2>
	<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save All Options',TEMPLATE_DOMAIN)); ?>" />
	<input type="hidden" name="action" value="save3" />
	</div>
	</div>
	</div>
	</form>



	<form method="post">
	<div id="resetsection">
	<div class="submit">
		<h2><?php _e("Clicking this will reset all theme options - use with caution", TEMPLATE_DOMAIN) ?></h2>
	<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset All Options',TEMPLATE_DOMAIN)); ?>" />
	<input type="hidden" name="action" value="reset3" />
	</div>
	</div>
	</form>


	</div>
<?php
}


/* Preset Styling section */
/* stylesheet addition */
$alt_stylesheet_path = TEMPLATEPATH .'/library/styles/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
	if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
		while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
			if(stristr($alt_stylesheet_file, ".css") !== false) {
				$alt_stylesheets[] = $alt_stylesheet_file;
			}
		}
	}
}

$category_bulk_list = array_unshift($alt_stylesheets, "default.css");
	$options2 = array (

	array(  "name" => __("Choose Your BP directory Preset Style:", TEMPLATE_DOMAIN),
		  	"id" => $shortname. $shortprefix . "custom_style",
			"std" => "default.css",
			"type" => "radio",
			"options" => $alt_stylesheets)
	);

function directory_ready_style_admin_panel() {
	echo "<div id=\"admin-options\">";
	
	global $themename, $shortname, $options2;
	
	if ( isset($_REQUEST['saved2']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
	if ( isset($_REQUEST['reset2']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
?>

<h4><?php echo "$themename"; ?> <?php _e('Choose your Preset Style', TEMPLATE_DOMAIN); ?></h4>
<form action="" method="post">
<div class="get-listings">
<h2><?php _e("Style Select:", TEMPLATE_DOMAIN) ?></h2>
<div class="option-save">
<ul>
<?php foreach ($options2 as $value) { ?>

<?php foreach ($value['options'] as $option2) {
$screenshot_img = substr($option2,0,-4);
$radio_setting = get_option($value['id']);
if($radio_setting != '') {	
	if (get_option($value['id']) == $option2) { 
		$checked = "checked=\"checked\""; } else { $checked = ""; 
	}
} 
else {
	if(get_option($value['id']) == $value['std'] ){ 
		$checked = "checked=\"checked\""; 
	} 
	else { 
		$checked = ""; 
	}
} ?>

<li>
<div class="theme-img">
	<img src="<?php bloginfo('template_directory'); ?>/library/styles/images/<?php echo $screenshot_img . '.png'; ?>" alt="<?php echo $screenshot_img; ?>" />
</div>
<input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $option2; ?>" <?php echo $checked; ?> /><?php echo $option2; ?>
</li>

<?php } 
} ?>

</ul>
</div>
</div>
	<p id="top-margin" class="save-p">
		<input name="save" type="submit" class="sbutton" value="<?php echo esc_attr(__('Save Options', TEMPLATE_DOMAIN)); ?>" />
		<input type="hidden" name="action" value="save2" />
	</p>
</form>

<form method="post">
	<p class="save-p">
		<input name="reset" type="submit" class="sbutton" value="<?php echo esc_attr(__('Reset Options', TEMPLATE_DOMAIN)); ?>" />
		<input type="hidden" name="action" value="reset2" />
	</p>
</form>
</div>

<?php }

function directory_admin_register() {
	global $themename, $shortname, $options;
	if ( isset($_GET['page']) == 'functions.php' ) {
	if ( 'save' == isset($_REQUEST['action']) ) {
	foreach ($options as $value) {
	update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
	foreach ($options as $value) {
	if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
	header("Location: themes.php?page=functions.php&saved=true");
	die;
	} else if( 'reset' == isset($_REQUEST['action']) ) {
	foreach ($options as $value) {
	delete_option( $value['id'] ); }
	header("Location: themes.php?page=functions.php&reset=true");
	die;
	}
	}
		add_theme_page(_g ($themename . __(' Theme Options', TEMPLATE_DOMAIN)),  _g (__('Theme Options', TEMPLATE_DOMAIN)),  'edit_theme_options', 'functions.php', 'directory_admin_panel');
}


function directory_ready_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
	if ( isset($_GET['page']) == 'directory-themes.php' ) {
		if ( 'save2' == isset($_REQUEST['action']) ) {
			foreach ($options2 as $value) {
				update_option( $value['id'], isset($_REQUEST[ $value['id'] ]) ); 
			}
			foreach ($options2 as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) { 
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				} 
				else { 
					delete_option( $value['id'] ); 
				} 
			}	
			header("Location: themes.php?page=directory-themes.php&saved2=true");
			die;
		} 
		else if( 'reset2' == isset($_REQUEST['action']) ) {
			foreach ($options2 as $value) {
				delete_option( $value['id'] ); 
			}
			header("Location: themes.php?page=directory-themes.php&reset2=true");
			die;
		}
	}
	add_theme_page(_g (__('BP directory Preset Style', TEMPLATE_DOMAIN)),  _g (__('Preset Style', TEMPLATE_DOMAIN)),  'edit_theme_options', 'directory-themes.php', 'directory_ready_style_admin_panel');
}


function directory_custom_style_admin_register() {
	global $themename, $shortname, $options, $options2, $options3, $bp_existed, $multi_site_on;
		if ( isset($_GET['page']) == 'styling-functions.php' ) {
			if ( 'save3' == isset($_REQUEST['action']) ) {
				foreach ($options3 as $value) {	
					update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
				foreach ($options3 as $value) {
					if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
					} 
					else { delete_option( $value['id'] ); } 
				}
				header("Location: themes.php?page=styling-functions.php&saved3=true");
				die;
				} 
				else if( 'reset3' == isset($_REQUEST['action']) ) {
					foreach ($options3 as $value) {
						delete_option( $value['id'] ); 
					}
				header("Location: themes.php?page=styling-functions.php&reset3=true");
				die;
				}
			}
			add_theme_page(_g ($themename . __('Custom styling', TEMPLATE_DOMAIN)),  _g (__('Custom Styling', TEMPLATE_DOMAIN)),  'edit_theme_options', 'styling-functions.php', 'directory_custom_style_admin_panel');
	}

function directory_admin_head() { ?>
	<link href="<?php bloginfo('template_directory'); ?>/library/options/options-css.css" rel="stylesheet" type="text/css" />

	<?php if ( (isset($_GET['page']) == 'styling-functions.php' ) || ( isset($_GET['page']) == 'functions.php' )) {?>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jscolor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery-ui-personalized-1.6rc2.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/library/scripts/jquery.cookie.min.js"></script>

		<script type="text/javascript">
			   jQuery.noConflict();
		
		jQuery(document).ready(function(){
		jQuery('ul#tabm').tabs({event: "click"});
		});
		</script>

	<?php } ?>
	
	<?php if (isset($_GET['page']) == 'directory-themes.php'){?>
			<link href="<?php bloginfo('template_directory'); ?>/library/options/custom-admin.css" rel="stylesheet" type="text/css" />
	<?php } ?>
	
<?php }

add_action('admin_head', 'directory_admin_head');
add_action('admin_menu', 'directory_admin_register');
add_action('admin_menu', 'directory_ready_style_admin_register');
add_action('admin_menu', 'directory_custom_style_admin_register');

?>