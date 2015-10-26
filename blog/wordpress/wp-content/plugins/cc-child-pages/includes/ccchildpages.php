<?php
/**
* ccchildpages
*
*/

class ccchildpages {

	// Used to uniquely identify this plugin's menu page in the WP manager
	const admin_menu_slug = 'ccchildpages';
	
	// Plugin name
	const plugin_name = 'CC Child Pages';

	// Plugin version
	const plugin_version = '1.30';
	
	public static function load_plugin_textdomain( ) {
//		$filename = basename( dirname( __FILE__ ) ) . '/languages/';
//		echo htmlentities($filename);
		load_plugin_textdomain( 'cc-child-pages', FALSE, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	public static function show_child_pages( $atts ) {
		// Store image size details in case we need to output Video Thumbnails, etc. which may be external files
		$img_sizes = get_intermediate_image_sizes();
		$img_sizes[] = 'full'; // allow "virtual" image size ...
		
		$default_atts = apply_filters( 'ccchildpages_defaults' , array(
			'id'			=> get_the_ID(),
			'cols'			=> '',
			'depth'			=> '1',
			'exclude'		=> '',
			'exclude_tree'	=> '',
			'skin'			=> 'simple',
			'class'			=> '',
			'orderby'		=> 'menu_order',
			'order'			=> 'ASC',
			'link_titles'	=> 'false',
			'title_link_class' => 'ccpage_title_link',
			'hide_more'		=> 'false',
			'hide_excerpt'	=> 'false',
			'truncate_excerpt'	=> 'true',
			'list'			=> 'false',
			'link_thumbs'	=> 'false',
			'thumbs'		=> 'false',
			'more'			=> __('Read more ...', 'cc-child-pages'),
			'link'			=> '',
			'siblings'		=> 'false',
			'words'			=> 55,
		));

		$a = shortcode_atts( $default_atts, $atts );
		
		$a = apply_filters( 'ccchildpages_attributes', $a);
				
		// If we are displaying siblings, set starting point to page parent and add current page to exclude list
		if ( strtolower(trim($a['siblings'])) == 'true' ) {
			$a['id'] = wp_get_post_parent_id( get_the_ID() ) ? wp_get_post_parent_id( get_the_ID() ) : 0;
			
			if ( $a['exclude'] != '' ) $a['exclude'] .= ',';
			
			$a['exclude'] .= get_the_ID();
		}

		$depth = intval($a['depth']);
		
		if ( strtolower(trim($a['list'])) != 'true' && $a['cols'] == '' ) $a['cols']='3';
		
		switch ( $a['cols'] ) {
			case '4':
				$class = 'fourcol';
				$cols = 4;
				break;
			case '3':
				$class = 'threecol';
				$cols = 3;
				break;
			case '2':
				$class = 'twocol';
				$cols = 2;
				break;
			case '1':
				$class = 'onecol';
				$cols = 1;
				break;
			default:
				$class = '';
				$cols = 1;
		}
		
		switch ( $a['skin'] ) {
			case 'red':
				$skin = 'ccred';
				break;
			case 'green':
				$skin = 'ccgreen';
				break;
			case 'blue':
				$skin = 'ccblue';
				break;
			default:
				$skin = 'simple';
		}
		
		if ( strtolower(trim($a['list'])) == 'true' ) {
			$list = TRUE;
		}
		else {
			$list = FALSE;
		}
		
		if ( strtolower(trim($a['truncate_excerpt'])) == 'true' ) {
			$truncate_excerpt = TRUE;
		}
		else {
			$truncate_excerpt = FALSE;
		}
		
		if ( strtolower(trim($a['link_titles'])) == 'true' ) {
			$link_titles = TRUE;
			$title_link_class = trim($a['title_link_class']);
		}
		else {
			$link_titles = FALSE;
		}
		
		if ( strtolower(trim($a['hide_more'])) == 'true' ) {
			$hide_more = TRUE;
		}
		else {
			$hide_more = FALSE;
		}
		
		if ( strtolower(trim($a['hide_excerpt'])) == 'true' ) {
			$hide_excerpt = TRUE;
		}
		else {
			$hide_excerpt = FALSE;
		}
		
		if ( $a['order'] == 'ASC' ) {
			$order = 'ASC';
		}
		else {
			$order = 'DESC';
		}

		switch ( $a['orderby'] ) {
			case 'post_id':
			case 'id':
			case 'ID':
				$orderby = 'ID';
				break;
			case 'post_author':
			case 'author':
				if ( $list ) {
					$orderby = 'post_author';
				}
				else {
					$orderby = 'author';
				}
				break;
			case 'post_date':
			case 'date':
				if ( $list ) {
					$orderby = 'post_date';
				}
				else {
					$orderby = 'date';
				}
				break;
			case 'post_modified':
			case 'modified':
				if ( $list ) {
					$orderby = 'post_modified';
				}
				else {
					$orderby = 'modified';
				}
				break;
			case 'post_title':
			case 'title':
				if ( $list ) {
					$orderby = 'post_title';
				}
				else {
					$orderby = 'title';
				}
				break;
			case 'post_name':
			case 'name':
			case 'slug':
				if ( $list ) {
					$orderby = 'post_name';
				}
				else {
					$orderby = 'name';
				}
				break;
			default:
				$orderby = 'menu_order';
		}

		
		if ( strtolower(trim($a['link_thumbs'])) == 'true' ) {
			$link_thumbs = TRUE;
		}
		else {
			$link_thumbs = FALSE;
		}
		
		if ( strtolower(trim($a['thumbs'])) == 'true' ) {
			$thumbs = 'medium';
		}
		else if ( strtolower(trim($a['thumbs'])) == 'false' ) {
			$thumbs = FALSE;
		}
		else {
			$thumbs = strtolower(trim($a['thumbs']));
			
			if ( ! in_array( $thumbs, $img_sizes ) ) $thumbs = 'medium';
		}
		
		$more = esc_html(trim($a['more']));
		
		// if class is specified, substitue value for skin class
		if ( $a['class'] != '' ) $skin = trim(esc_html($a['class']));
		
		$outer_template = str_replace( '{{class}}', $class, apply_filters('ccchildpages_outer_template','<div class="ccchildpages {{class}} {{skin}} ccclearfix">{{ccchildpages}}</div>', $a) );
		$outer_template = str_replace( '{{skin}}', $skin, $outer_template );
		
		$inner_template = apply_filters('ccchildpages_inner_template','<div class="ccchildpage {{page_class}}"><h3{{title_class}}>{{title}}</h3>{{thumbnail}}{{excerpt}}{{more}}</div>', $a);
				
//		$return_html = '<div class="ccchildpages ' . $class .' ' . $skin . ' ccclearfix">';
		
		$page_id = $a['id'];

		if ( $list ) {	
			$args = array(
				'title_li'		=> '',
				'child_of'		=> $page_id,
				'echo'			=> 0,
				'depth'			=> $depth,
				'exclude'		=> $a['exclude'],
				'sort_order'	=> $order,
				'sort_column'	=> $orderby
			);
			
			$post_type = get_post_type( $page_id );
			$args['post_type'] = $post_type;
			
			$args = apply_filters('ccchildpages_list_pages_args', $args, $a);
		
			$page_count = 0;		

			$return_html = '<ul class="ccchildpages_list ccclearfix">';
						
			$page_list = trim(wp_list_pages( $args ));
			
			if ( $page_list == '' ) return '';
			
			$return_html .= $page_list;
			
			$return_html .= '</ul>';
			
		}
		else {
			$return_html = '';
			
			$posts_array = explode(',', $page_id); // Allow for comma separated lists of IDs
			$post_count = count ($posts_array);
						
			$args = array(
//				'post_type'      => 'page',
//				'post_type'      => $post_type,
				'posts_per_page' => -1,
//				'post_parent'    => $page_id,
				'order'          => $order,
				'orderby'			=> $orderby,
				'post__not_in'		=> explode(',', $a['exclude']),
				'post_status'		=> 'publish'
			);
			
			if ( $post_count > 1 ) {
				// Multiple IDs specified, so set the post_parent__in parameter
				$args['post_parent__in'] = $posts_array;
				
				$post_type_array = array();
				
				// get post_type for each post specified ...
				foreach ( $posts_array as $post_id ) {
					// Get post_type
					$post_type = get_post_type( $post_id );
					
					if ( ! in_array($post_type, $post_type_array) ) $post_type_array[] = $post_type;
				}
				
				$args['post_type'] = $post_type_array;

			}
			else {
				// Single ID specified, so set the post_parent parameter
				$args['post_parent'] = $page_id;
				$args['post_type'] = get_post_type( $page_id );
			}
			
			$args = apply_filters('ccchildpages_query_args', $args, $a);

			$parent = new WP_Query( $args );
		
			if ( ! $parent->have_posts() ) return '';
		
			$page_count = 0;

			while ( $parent->have_posts() ) {
				
				$tmp_html = $inner_template;
			
				$parent->the_post();
				
				$id = get_the_ID();
			
				$page_count++;
			
				if ( $page_count%$cols == 0 && $cols > 1) {
					$page_class = ' cclast';
				}
				else if ( $page_count%$cols == 1 && $cols > 1 ) {
					$page_class = ' ccfirst';
				}
				else {
					$page_class = '';
				}

				if ( $page_count%2 == 0  ) {
					$page_class .= ' cceven';
				}
				else {
					$page_class .= ' ccodd';
				}
				
				$page_class .= ' ccpage-count-' . $page_count;
				$page_class .= ' ccpage-id-' . $id;
				$page_class .= ' ccpage-' . self::the_slug($id);
				
				if ( $a['link'] == '' ) {
					$link = get_permalink($id);
				}
				else {
					$link = $a['link'];
				}
							
				$tmp_html = str_replace('{{page_class}}', $page_class, $tmp_html);
			
				if ( ! $link_titles ) {
					$title_html = get_the_title();
					$title_class = '';
				}
				else {
					$title_html = '<a class="' . $title_link_class . '" href="' . $link . '" title="' . get_the_title() . '">' . get_the_title() . '</a>';
					$title_class = ' class="ccpage_linked_title"';
				}
				$tmp_html = str_replace('{{title}}', $title_html, $tmp_html);
				$tmp_html = str_replace('{{title_class}}', $title_class, $tmp_html);
				
				$thumb_url = '';
				$thumbs_html = '';
				
				if ( $thumbs != FALSE ) {
					
					$thumb_attr = array(
						'class'	=> "cc-child-pages-thumb",
						'alt'	=> get_the_title(),
						'title'	=> get_the_title(),
					);
					
					// Get the thumbnail code ...
					$thumbnail = get_the_post_thumbnail($id, $thumbs, $thumb_attr);
					
					if ( $thumbnail != '' ) {
						// Thumbnail found, so set thumb_url to actual URL of thumbnail
						$tmp_thumb_id = get_post_thumbnail_id($id);
						$tmp_thumb_url_array = wp_get_attachment_image_src($tmp_thumb_id, 'thumbnail-size', true);
						$thumb_url = $tmp_thumb_url_array[0];
					}
					
					// If no thumbnail found, request a "Video Thumbnail" (if plugin installed)
					// to try and force generation of thumbnail
					if ( $thumbnail == '' ) {
						// Check whether Video Thumbnail plugin is installed.
						// If so, call get_video_thumbnail() to make sure that thumnail is generated.
						if ( class_exists('Video_Thumbnails') && function_exists( 'get_video_thumbnail' ) ) {
							// Call get_video_thumbnail to generate video thumbnail
							$video_img = get_video_thumbnail($id);
							
							// If we got a result, display the image
							if ( $video_img != '' ) {
								
								// First, try to pick up the thumbnail in case it has been regenerated (may be the case if automatic featured image is turned on)
								$thumbnail = get_the_post_thumbnail($id, $thumbs, $thumb_attr);
								
								// If thumbnail hasn't been regenerated, use Video Thumbnail (may be the full size image)
								if ( $thumbnail == '' ) {
									
									// First, try and find the attachment ID from the URL
									$attachment_id = self::get_attachment_id($video_img);
									
									$thumb_url = $video_img;
									
									if ( $attachment_id != FALSE ) {
										// Attachment found, get thumbnail
										$thumbnail = wp_get_attachment_image( $attachment_id, $thumbs ) . "\n\n<!-- Thumbnail attachment -->\n\n";
									}
									else {
										$thumbnail .= '<img src="' . $video_img . '" alt="' . get_the_title() . '" />';
									}
								}
							}
						}
						
					}
					
					// If thumbnail is found, display it.
					
					if ( $thumbnail != '' ) {
						if ( $link_thumbs ) {
							$thumbs_html = '<a class="ccpage_linked_thumb" href="' . $link . '" title="' . get_the_title() . '">' . $thumbnail . '</a>';
						}
						else {
							$thumbs_html = $thumbnail;
						}
					}
				}
				
				$tmp_html = str_replace('{{thumbnail}}', $thumbs_html, $tmp_html);
				$tmp_html = str_replace('{{thumbnail_url}}', $thumb_url, $tmp_html);
				
				$page_excerpt = '';

				if ( ! $hide_excerpt ) {
					$words = ( intval($a['words']) > 0 ? intval($a['words']) : 55 );
				
					if ( has_excerpt() ) {
						$page_excerpt = get_the_excerpt();
						if ( str_word_count(strip_tags($page_excerpt) ) > $words && $truncate_excerpt ) $page_excerpt = wp_trim_words( $page_excerpt, $words, '...' );
					}
					else {
						$page_excerpt = do_shortcode( get_the_content() ); // get full page content
						
						if ( str_word_count( wp_trim_words($page_excerpt, $words+10, '') ) > $words ) {
							// If page content is longer than allowed words, 
							$trunc = '...';
						}
						else {
							// If page content is within allowed word count, do not add anything to the end of it
							$trunc = '';
						}
						$page_excerpt = wp_trim_words( $page_excerpt, $words, $trunc );
					}
				
					$page_excerpt = str_replace( '{{page_excerpt}}', $page_excerpt, apply_filters('ccchildpages_excerpt_template', '<div class="ccpages_excerpt">{{page_excerpt}}</div>', $a) );
				}
				
				$tmp_html = str_replace('{{excerpt}}', $page_excerpt, $tmp_html);
				
				$more_html = '';
			
				if ( ! $hide_more ) {
					$more_html = str_replace( '{{more}}', $more, apply_filters('ccchildpages_more_template', '<p class="ccpages_more"><a href="{{link}}" title="{{more}}">{{more}}</a></p>', $a ) );
				}
				
				$tmp_html = str_replace('{{more}}', $more_html, $tmp_html);
				$tmp_html = str_replace('{{link}}', $link, $tmp_html);
				
				$return_html .= $tmp_html;
				
				// Reset global post query
				wp_reset_postdata();
			}
		
		} 	

		$return_html = str_replace('{{ccchildpages}}', $return_html, $outer_template);
		
		$return_html = apply_filters( 'ccchildpages_before_shortcode', '', $a ) . $return_html . apply_filters( 'ccchildpages_after_shortcode', '', $a );
		
//		wp_reset_query(); // Should not be required
		
		return $return_html;
	}
	
	public static function enqueue_styles() {
		$css_file = plugins_url( 'css/styles.css' , __FILE__ );
		$css_skin_file = plugins_url( 'css/skins.css' , __FILE__ );
		$css_conditional_file = plugins_url( 'css/styles.ie.css' , __FILE__ );
		if ( !is_admin() ) {
			// Load main styles
			wp_register_style(
				'ccchildpagescss',
				$css_file,
				false,
				self::plugin_version
			);
			wp_enqueue_style( 'ccchildpagescss' );
			
			// Load skins
			wp_register_style(
				'ccchildpagesskincss',
				$css_skin_file,
				false,
				self::plugin_version
			);
			wp_enqueue_style( 'ccchildpagesskincss' );
			
			// Conditionally load fallback for older versions of Internet Explorer
			wp_register_style(
				'ccchildpagesiecss',
				$css_conditional_file,
				false,
				self::plugin_version
			);
			wp_enqueue_style( 'ccchildpagesiecss' );
			wp_style_add_data( 'ccchildpagesiecss', 'conditional', 'lt IE 8' );
			
			// Load custom CSS
			$custom_css = self::custom_css();
			
			if ( $custom_css != '' ) {
				wp_add_inline_style( 'ccchildpagesskincss', $custom_css );
			}
		}
	}

	private static function the_slug($id) {
		$post_data = get_post($id, ARRAY_A);
		$slug = $post_data['post_name'];
		return $slug; 
	}
	
	public static function dashboard_widgets() {
		if ( current_user_can( 'update_plugins' ) ) {
			wp_add_dashboard_widget('cc-child-pages-dashboard', 'CC Child Pages', 'ccchildpages::dashboard_widget_feed');
		}
	}
	
	public static function dashboard_widget_feed() {
		$content = file_get_contents('http://ccplugins.co.uk/feed/');
		$x = new SimpleXmlElement($content);
     
		echo '<ul>';
     
		foreach($x->channel->item as $entry) {
			echo '<li><a href="' . $entry->link . '" title="' . $entry->title . '" target="_blank">' . $entry->title . '</a></li>';
		}
		echo '</ul>';
	}
	
	public static function tinymce_buttons() {
		if ( $options = get_option('cc_child_pages') ) {
			if ( empty( $options['show_button'] ) ) {
				// undefined - so set to true for backward compatibility
				$show_button = TRUE;
			}
			else if ( $options['show_button'] == 'true' ) {
				$show_button = TRUE;
			}
			else {
				$show_button = FALSE;
			}
		}
		else {
			$show_button = TRUE;
		}
		
		if ( $show_button ) {
			add_filter( 'mce_external_plugins', 'ccchildpages::add_childpages_buttons' );
			add_filter( 'mce_buttons', 'ccchildpages::register_childpages_buttons' );
		}
	}
	
	public static function add_childpages_buttons ( $plugin_array ) {
		$plugin_array['ccchildpages'] = plugins_url( 'js/ccchildpages-plugin.js' , __FILE__ );
		return $plugin_array;
	}
	
	public static function register_childpages_buttons ( $buttons ) {
		array_push( $buttons, 'ccchildpages');
		return $buttons;
	}
	
	/*
	 * Add options page ...
	 */
	
	// Set default values on activation ...
	public static function options_activation () {
		$options = array();
		$options['show_button'] = 'true';
		$options['customcss'] = '';
		
		add_option('cc_child_pages', $options, '', 'yes');
	}
	 
	// Register settings ...
	public static function register_options () {
		register_setting('cc_child_pages', 'cc_child_pages');
	}
	
	// Add submenu
	public static function options_menu () {
		add_options_page('CC Child Pages', 'CC Child Pages', 'manage_options', 'cc-child-pages', 'ccchildpages::options_page');
	}
	
	// Display options page
	public static function options_page () {
?>
<div class="wrap">
	<form method="post" id="cc_child_page_form" action="options.php">
		<?php
			$show_button = FALSE;

			settings_fields('cc_child_pages');
			
			if ( $options = get_option('cc_child_pages') ) {
				if ( empty( $options['show_button'] ) ) {
					// undefined - so set to true for backward compatibility
					$show_button = TRUE;
				}
				else if ( $options['show_button'] == 'true' ) {
					$show_button = TRUE;
				}
				
				$customcss = empty( $options['customcss'] ) ? '' : $options['customcss'];
			}
			else {
				$show_button = TRUE;
				$customcss = '';
			}
		?>
		<h2><?php _e('CC Child Pages options', 'cc-child-pages' ) ?></h2>
		<p><label><?php _e( 'Add button to the visual editor:', 'cc-child-pages' ); ?> <input type="radio" name="cc_child_pages[show_button]" value="true" <?php checked(TRUE,$show_button) ?> > Yes <input type="radio" name="cc_child_pages[show_button]" value="false" <?php checked(FALSE,$show_button) ?> > No</label></p>
		<p><label><?php _e( 'Custom CSS:', 'cc-child-pages' ); ?><br /><textarea name="cc_child_pages[customcss]" class="large-text code" rows="10"><?php echo htmlentities($customcss) ?></textarea></label></p>
		<p class="submit"><input  type="submit" name="submit" class="button-primary" value="<?php _e('Update Options','cc-child-pages'); ?>" /></p>
	</form>

</div>
<?php
	}
	
	/*
	 * Output Custom CSS
	 */
	public static function custom_css() {
		$custom_css = '';
		if ( $options = get_option('cc_child_pages') ) {
			if ( ! empty($options['customcss'])) {
				if ( trim($options['customcss']) != '' ) {
					$custom_css = trim( $options['customcss'] );
				}
			}
		}
		return $custom_css;
	}
	
	/*
	 * Show Excerpt for Pages ...
	 */
	public static function show_page_excerpt () {
		add_post_type_support( 'page', 'excerpt' );
	}
	
	/*
	 * Get Attachment ID from URL
	 */
	public static function get_attachment_id( $url ) {
		$dir = wp_upload_dir();
		
		// baseurl never has a trailing slash
		if ( FALSE === strpos( $url, $dir['baseurl'] . '/' ) ) {
			// URL points to a place outside of upload directory
			return FALSE;
		}
		
		$file  = basename( $url );
		$query = array(
			'post_type'  => 'attachment',
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
				),#
			)
		);

		$query['meta_query'][0]['key'] = '_wp_attached_file';
		
		// query attachments
		$ids = get_posts( $query );
		
		if ( ! empty( $ids ) ) {
			foreach ( $ids as $id ) {
				// first entry of returned array is the URL
				$tmp_url = wp_get_attachment_image_src( $id, 'full' );
				if ( $url === array_shift( $tmp_url ) )
					return $id;
			}
		}
		
		return FALSE;
	}
	
	/*
	 * Get size information for thumbnail by size
	 */
	private static function get_image_dimensions($thumbs) {
		global $_wp_additional_image_sizes;
		
		$dimensions = array();
		
		// If a default image size, use get options method
		if ( in_array( $thumbs, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$dimensions['height'] = get_option( $thumbs . '_size_h' );
			$dimensions['width'] = get_option( $thumbs . '_size_w' );
		}
		elseif ( isset( $_wp_additional_image_sizes[ $thumbs ] ) ) {
			$dimensions['height'] = $_wp_additional_image_sizes[ $thumbs ]['height'];
			$dimensions['width'] = $_wp_additional_image_sizes[ $thumbs ]['width'];
		}
		
		return $dimensions;
	}
	
	/*
	 * Show plugin links
	 */
	public static function plugin_action_links( $links ) {
		$links[] = '<a href="https://wordpress.org/support/view/plugin-reviews/cc-child-pages" target="_blank">Rate this plugin...</a>';
//		$links[] = '<a href="http://www.ccplugins.co.uk" target="_blank">More from CC Plugins</a>';
		return $links;
	}
	
	public static function plugin_row_meta( $links, $file ) {
		$current_plugin = basename(dirname($file));
		
		if ( $current_plugin =='cc-child-pages' ) {
			$links[] = '<a href="options-general.php?page=cc-child-pages">' . __('Settings...', 'cc-child-pages') . '</a>';
			$links[] = '<a href="https://wordpress.org/support/view/plugin-reviews/cc-child-pages" target="_blank">' . __('Rate this plugin...', 'cc-child-pages') . '</a>';
			$links[] = '<a href="http://ccchildpages.ccplugins.co.uk/donate/" target="_blank">' . __('Donate...', 'cc-child-pages') . '</a> ' . __('(Your donations keep this plugin free &amp; supported)', 'cc-child-pages');
		}

		return $links;
	}
}

/*EOF*/