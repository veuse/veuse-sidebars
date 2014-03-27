<?php /* Plugin options */

// ------------------------------------------------------------------------
// PLUGIN PREFIX:
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'veuse_sidebars_' prefix is derived from [p]plugin [o]ptions [s]tarter [k]it

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'veuse_sidebars_add_defaults');
register_uninstall_hook(__FILE__, 'veuse_sidebars_delete_plugin_options');
add_action('admin_init', 'veuse_sidebars_init' );
add_action('admin_menu', 'veuse_sidebars_add_options_page');
add_filter( 'plugin_action_links', 'veuse_sidebars_plugin_action_links', 10, 2 );


// Delete options table entries ONLY when plugin deactivated AND deleted
function veuse_sidebars_delete_plugin_options() {
	delete_option('veuse_sidebars_options');
}


// Define default option settings
function veuse_sidebars_add_defaults() {
	$tmp = get_option('veuse_sidebars_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('veuse_sidebars_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"posttype" => 'page' );
		update_option('veuse_sidebars_options', $arr);
	}
}


// Init plugin options to white list our options
function veuse_sidebars_init(){
	register_setting( 'veuse_sidebars_plugin_options', 'veuse_sidebars_options', 'veuse_sidebars_validate_options' );
}



// Add menu page
function veuse_sidebars_add_options_page() {
	add_options_page('Veuse Sidebars', __('Sidebar Generator','veuse-sidebars'), 'manage_options', __FILE__, 'veuse_sidebars_render_form');
}



// Render the Plugin options form
function veuse_sidebars_render_form() {
	?>
	<div class="wrap">

		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Sidebar Generator Settings','veuse-sidebars');?></h2>
		<p><?php _e('Select which post-types you want to use the custom sidebars.','veuse-sidebars');?></p>
		
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('veuse_sidebars_options'); ?>
			<?php $options = get_option('veuse_sidebars_options'); ?>
			
			<?php echo $options;?>
			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">


				<tr>
					<th scope="row"><strong><?php _e('Enable plugin stylesheet','veuse-sidebars');?></strong></th>
					<td>
						<?php

					$post_types = get_post_types( '', 'names' ); 
					
					foreach ( $post_types as $post_type ) {
						?>
						<input name="veuse_sidebars_options[posttypes][<?php echo $post_type;?>]" type="checkbox" <?php if(isset($options['posttypes'][$post_type])) echo 'checked="checked"'; ?>/><label><?php echo $post_type;?></label><br>
						
						<?php
					 
					}

?>
					
						
					</td>
				</tr>


			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>


	</div>
	<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function veuse_sidebars_validate_options($input) {
	 // strip html from textboxes
	$input['posttype'] =  $options['posttype']; // Sanitize textarea input (strip html tags, and escape characters)
	//$input['lightbox'] =$options['lightbox']; // Sanitize textarea input (strip html tags, and escape characters)

	//$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function veuse_sidebars_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$veuse_sidebars_links = '<a href="'.get_admin_url().'options-general.php?page=veuse-sidebars/veuse-sidebars.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $veuse_sidebars_links );
	}

	return $links;
}