<?php
/**
 * Implement some WP101 Plugin Customizations
 */
if ( ! class_exists( 'UMW_WP101_Enhancements' ) ) {
	class UMW_WP101_Enhancements {
		private static $instance     = false;
		
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		public function __construct() {
			self::$instance = $this;
			
			add_action( 'init', array( $this, 'init' ) );
		}
		
		function init() {
			add_filter( 'wp101_get_help_topics', array( $this, 'topics_to_caps' ), 9, 2 );
			add_filter( 'wp101_get_help_topics', array( $this, 'get_help_topics' ), 10, 2 );
			add_filter( 'wp101_get_help_topics', array( $this, 'get_umw_help_topics' ), 11, 2 );
			add_filter( 'wp101_get_wpseo_help_topics', array( $this, 'get_umw_wpseo_help_topics' ), 10, 2 );
			
			/**
			 * Remove the admin menu entry for subscribers
			 */
			/*add_action( 'admin_menu', array( $this, 'adjust_admin_menu' ), 11 );*/
		}
		
		function adjust_admin_menu() {
			global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;
			
			foreach ( $menu as $k=>$v ) {
				if ( 'wp101' == $v[2] ) {
					if ( 'read' == $v[1] ) {
						$menu[$k][1] = 'edit_posts';
					} else {
						print( '<pre><code>' );
						var_dump( $v );
						print( '</code></pre>' );
						die();
					}
				}
			}
		}
		
		function topics_to_caps( $topics=array(), $wp101obj=null ) {
			foreach ( $topics as $id=>$props ) {
				switch ( $props['title'] ) {
					case 'Posts vs. Pages' : 
					case 'Creating and Editing Pages' : 
						$topics[$id]['cap'] = 'edit_pages';
						break;
					case 'Add Photos and Images' : 
					case 'Using the Media Library' : 
						$topics[$id]['cap'] = 'upload_files';
						break;
					case 'Managing Comments' : 
						$topics[$id]['cap'] = 'moderate_comments';
						break;
					case 'Changing the Theme' : 
						$topics[$id]['cap'] = 'switch_themes'; 
						break;
					case 'Adding Widgets' : 
					case 'Custom Menus' : 
						$topics[$id]['cap'] = 'edit_theme_options';
						break;
					case 'Installing Plugins' : 
						$topics[$id]['cap'] = 'install_plugins';
						break;
					case 'Adding New Users' : 
						$topics[$id]['cap'] = array( 'create_users', 'add_users' );
						break;
					case 'Settings &amp; Configuration' : 
						$topics[$id]['cap'] = 'manage_options';
						break;
						
					case 'The Dashboard' : 
					case 'The Editor' : 
					case 'Creating a New Post' : 
					case 'Post Formats' : 
					case 'Edit an Existing Post' : 
					case 'Using Categories and Tags' : 
					case 'How to Embed Video' : 
					case 'Creating Links' : 
					case 'Useful Tools' : 
					default : 
						$topics[$id]['cap'] = 'edit_posts';
						break;
				}
			}
			
			return $topics;
		}
		
		function get_help_topics( $topics=array(), $wp101obj=null ) {
			$new_topic_list = array();
			foreach ( $topics as $id=>$props ) {
				if ( ! array_key_exists( 'cap', $props ) )
					$props['cap'] = 'edit_posts';
				
				if ( is_array( $props['cap'] ) ) {
					foreach ( $props['cap'] as $cap ) {
						if ( current_user_can( $cap ) )
							$new_topic_list[$id] = $props;
					}
				} else {
					if ( current_user_can( $props['cap'] ) )
						$new_topic_list[$id] = $props;
				}
			}
			return $new_topic_list;
		}
		
		function get_umw_help_topics( $topics=array(), $wp101obj=null ) {
			$new_topic_list = array();
			foreach ( $topics as $id=>$props ) {
				switch( $props['title'] ) {
					case 'Changing the Theme' : 
					case 'Adding Widgets' : 
					case 'Custom Menus' : 
					case 'Installing Plugins' : 
					case 'Adding New Users' : 
					case 'Useful Tools' : 
					case 'Settings &amp; Configuration' : 
						if ( current_user_can( 'manage_network_themes' ) ) {
							$new_topic_list[$id] = $props;
						}
						break;
					default : 
						$new_topic_list[$id] = $props;
						break;
				}
			}
			
			return $new_topic_list;
		}
		
		function get_umw_wpseo_help_topics( $topics=array(), $wp101obj=null ) {
			$new_topic_list = array();
			foreach ( $topics as $id=>$props ) {
				switch( $props['title'] ) {
					case 'General Tab' : 
					case 'Page Analysis Tab' : 
						$new_topic_list[$id] = $props;
						break;
					default : 
						if ( current_user_can( 'manage_network_themes' ) ) {
							$new_topic_list[$id] = $props;
						}
						break;
				}
			}
			
			return $new_topic_list;
		}
	}
}

UMW_WP101_Enhancements::get_instance();