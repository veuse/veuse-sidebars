<?php
/*
Plugin Name: Veuse Sidebar Generator
Plugin URI: http://veuse.com/veuse-sidebar-generator
Description: Register unlimited custom sidebars with wordpress. 
Version: 1.0
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
Text Domain: veuse-sidebars
Domain Path: /languages
*/

 
class Veuse_Custom_Sidebar{
 
	private $meta_id = 'veuse-sidebar';
	private $meta_key = 'veuse-sidebar-meta';
	private $keys = array('_page_sidebar');
	
	private $screens = NULL;
	
	/**
	 * Initiate Wordpress Hooks
	 * @return void
	 */
	public function __construct($screens){
				
		$this->screens = $screens;
	
		add_action('init', array( $this, 'register_sidebar' ));
		add_action('init', array( $this, 'load_sidebars' ));
 
		// manage post meta 
		add_action( 'load-post.php', array($this, 'setup_metabox' ));
		add_action( 'load-post-new.php', array($this, 'setup_metabox' ));
		add_action( 'save_post', array($this, 'save_metabox'), 10, 2 );
		
		add_filter('manage_sidebar_posts_columns',  array ( $this,'veuse_sidebars_columns'));
		add_action('manage_sidebar_posts_custom_column', array ( $this,'veuse_sidebars_custom_columns'), 10, 2 );
		
		add_shortcode('veuse_sidebar', array(&$this,'veuse_sidebar'));
		
		
		
			
	}
	
	public function veuse_sidebars_set_screens(){
					 
		return $this->screens;
		
	}
	
	
	

 
	/**
	 * Register Sidebar Poststype
	 * @return void
	 */
	public function register_sidebar(){
	
		$labels = array(
	        'name' => __( 'Sidebar Generator', 'veuse-sections' ), // Tip: _x('') is used for localization
	        'singular_name' => __( 'Sidebar', 'veuse-sections' ),
	        'add_new' => __( 'Add New Sidebar', 'veuse-sections' ),
	        'add_new_item' => __( 'Add New Sidebar','veuse-sections' ),
	        'edit_item' => __( 'Edit Sidebar', 'veuse-sections' ),
	        'all_items' => __( 'Sidebar Generator','veuse-sections' ),
	        'new_item' => __( 'New Sidebar','veuse-sections' ),
	        'view_item' => __( 'View Sidebar','veuse-sections' ),
	        'search_items' => __( 'Search Sidebars','veuse-sections' ),
	        'not_found' =>  __( 'No Sidebars','veuse-sections' ),
	        'not_found_in_trash' => __( 'No Sidebars found in Trash','veuse-sections' ),
	        'parent_item_colon' => ''
	    );
	
		register_post_type('sidebar',
			array(
				'labels' => $labels,
				'show_ui' => true,
				'_builtin' => false, // It's a custom post type, not built in
				'capability_type' => 'post',
				'rewrite' => array("slug" => "sidebars"), // Permalinks
				'query_var' => "sidebars", // This goes to the WP_Query schema
				'supports' => array('title'),
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'show_in_menu' => 'themes.php',
				'show_in_admin_bar' => false,
				'show_in_nav_menus' => false,
				'has_archive' => false,
				'public' => false,
				
				)
			);
	}
	
	
	/* Shortcode
	============================================= */
	
	public function veuse_sidebar( $atts, $content = null ) {
	
			 extract(shortcode_atts(array(
					'id' 	=> ''
	
			    ), $atts));
	
			
				ob_start();
	
				dynamic_sidebar('veuse-sidebar-'.$id);

				$content = ob_get_contents();

				ob_end_clean();
				
				wp_reset_query();
	
				return $content;
	
	
	}
	
	
	function veuse_sidebars_custom_columns($column, $post_id) {
	
		global $post;
				
		switch ($column) {
		 	
		 		
		 	
		 		case 'title' :
		 	
					echo get_the_title();
					break;
			 	

				case 'id' :
			 	
				 	echo 'veuse-sidebar-'.get_the_ID();
					break;	
				
				
				case 'slug' :
			 	
				 	echo $post->post_name;
					break;
				
				case 'shortcode' :
			 	
				 	echo '<code>[veuse_sidebar id="'.get_the_ID().'"]</code>';
					
					break;
				
		
				
		}			
				
	}
		
	function veuse_sidebars_columns($columns){
				
		$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => __("Sidebar name","veuse-sidebars"),
				"slug" => __("Slug","veuse-sidebars"),
				"id" => __("Sidebar ID","veuse-sidebars"),
				"shortcode" => __("Shortcode","veuse-sidebars")
		);
		return $columns;
	}
	
 
	/**
	 * Register Sidebars with Wordpress
	 * @return void
	 */
	 
	public function load_sidebars(){
		$sidebars = new WP_Query(array(
			'post_type' => 'sidebar'
		));
 
		if($sidebars->have_posts()){
			while($sidebars->have_posts()){ 
				$sidebars->the_post(); 
				global $post;
				
				register_sidebar( array(
				    'id'          => 'veuse-sidebar-' . $post->ID,
				    'name'        => __( $post->post_title ),
				    'description' => __( $post->post_content ),
				    'before_title' => '<h4 class="widget-title"><span>',
					'after_title' => '</span></h4>',
					'before_widget' => '<aside  id="%1$s" class="widget %2$s">',
					'after_widget' => '</aside>'
				) );
			}
		}
	}
 
	/**
	 * Register Sidebar Meta Box
	 * @return void
	 */
	public function setup_metabox(){
		add_action( 'add_meta_boxes', array($this, 'add_metabox' ));
	}
	
 
	/**
	 * Add Sidebar Meta box
	 * @return  void
	 */
	 function add_metabox(){
	
		$screens = $this->veuse_sidebars_set_screens();
		   	 		 
		 foreach ( $screens as $screen ) {
			
			add_meta_box(
				$this->meta_id, 
				__( 'Sidebar','veuse-sidebars'), 
				array($this, 'show_metabox'), 
				$screen, 
				'side', 
				'core'
			);
		}
	}
 
	/**
	 * Output Sidebar Select Menu
	 * @param  stdObj $object 
	 * @return void
	 */
	function show_metabox($object){
		wp_nonce_field( basename( __FILE__ ), $this->meta_key.'_nonce' );
		$values = $this->get_meta( $object->ID);
		extract($values);
		?>
		<p><?php _e('Choose sidebar:','veuse-sidebars');?> <select id="_page_sidebar" name="<?php echo $this->meta_id; ?>[_page_sidebar]">
			<option value="default_sidebar" <?php echo ($_page_sidebar == 'default_sidebar' || $_page_sidebar == '') ? " selected=\"selected\"" : '' ?>><?php _e('Default Sidebar','veuse-sidebars');?></option>
			
			<?php 
			
			$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'post_title',
				'order'            => 'DESC',
				'post_type'        => 'sidebar',
				'post_status'      => 'publish',
		
			); 
			
			$sidebars = get_posts($args);
			
			foreach ($sidebars as $sidebar){
			
				$selected = '';
				if(intval($_page_sidebar) == $sidebar->ID )
					$selected = " selected=\"selected\"";
			
				?>
				<option value="<?php echo $sidebar->ID; ?>"<?php echo $selected; ?>><?php echo $sidebar->post_title; ?></option>
				<?php
			
			}
	
			?>
		</select></p>
		<?php
	}
 
	/**
	 * Retrieve Post Meta Values
	 * @param  int $post_ID 
	 * @return array
	 */
	public function get_meta($post_ID){
		$values = array();
		$default = array(
			'_page_sidebar' => null,
		);
 
		foreach($this->keys as $key){
			$values[$key] = get_post_meta( $post_ID, $key, true );		
		}
 
		return is_array($values) ? array_merge($default, $values) : $default;
	}
 
	/**
	 * Save Sidebar meta box
	 * @param  int $post_id 
	 * @param  stdObj $post    
	 * @return void
	 */
	public function save_metabox($post_id, $post){
		if ( !isset( $_POST[$this->meta_key.'_nonce'] ) || !wp_verify_nonce( $_POST[$this->meta_key.'_nonce'], basename( __FILE__ ) ) )
			return $post_id;
		// Get the post type object. 
 
		$post_type = get_post_type_object( $post->post_type );
 
		// Check if the current user has permission to edit the post. 
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;
 
		foreach($this->keys as $key)
		{
			$name = isset( $_POST[$this->meta_id][$key] ) ?  $_POST[$this->meta_id][$key] : '' ;
			$value = get_post_meta( $post_id, $key, true );
 
			// If a new meta value was added and there was no previous value, add it. 
			if ( $name && '' == $value )
				add_post_meta( $post_id, $key, $name, true );
 
			// If the new meta value does not match the old value, update it. 
			elseif ( $name && $name != $value )
				update_post_meta( $post_id, $key, $name );
 
			// If there is no new meta value but an old value exists, delete it. 
			elseif ( '' == $name && $value )
				delete_post_meta( $post_id, $key, $value );
		}
	}	
}
 
new Veuse_Custom_Sidebar( array('page','post') );

//require_once('plugin-options.php');

function veuse_sidebars_contextual_help( $contextual_help, $screen_id, $screen ) { 
    
    if ( 'sidebar' == $screen->id ) {
    
    	// Remove default tabs
		$screen->remove_help_tabs();
    
        $contextual_help = '<h2>Sidebar Generator</h2>
        <p>Simply give your sidebar a title and publish. </p> 
        <p>You will now have a new widgetized sidebar you can populate with widgets in Appearance > Widgets.</p>
         <p>When editing or creating a new page or post, you can select the sidebar from a meta-panel.</p>';

    } elseif ( 'edit-sidebar' == $screen->id ) {
		
		// Remove default tabs
		$screen->remove_help_tabs();
    
        $contextual_help = '<h2>Adding Sidebars</h2>
        <p>Click on Add Sidebar to create a new widgetized sidebar</p>';

    }
    return $contextual_help;
}
add_action( 'contextual_help', 'veuse_sidebars_contextual_help', 10, 3 );

?>