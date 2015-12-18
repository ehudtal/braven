<?php
/**
 * Braven Theme functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link https://codex.wordpress.org/Plugin_API
 */


/**
 * Braven Theme setup.
 *
 * Sets up theme defaults and registers the various WordPress features that
 * Braven Theme supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add Visual Editor stylesheets.
 * @uses add_theme_support() To add support for automatic feed links, post
 * formats, and post thumbnails.
 * @uses register_nav_menu() To add support for a navigation menu.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 */
function braven_setup() {
	/*
	 * Makes Braven Theme available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Braven Theme, use a find and
	 * replace to change 'braven' to the name of your theme in all
	 * template files.
	 */
	load_theme_textdomain( 'braven', get_template_directory() . '/languages' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css' ) );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Switches default core markup for search form, comment form,
	 * and comments to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * This theme supports all available post formats by default.
	 * See https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
	) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Navigation Menu', 'braven' ) );

	/*
	 * This theme uses a custom image size for featured images, displayed on
	 * "standard" posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270, true );
	add_image_size('testimonial-thumb',300,350, true);
	add_image_size( 'blog-thumb', 250, 250, true );
	add_image_size( 'featured-page-thumb', 9999, 400, true ); 
	add_image_size('spotlight-thumb',350,250, true);
	add_image_size( 'staff-thumb', 350, 350, true ); 
	add_image_size( 'partner-thumb', 800, 350); 

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
add_action( 'after_setup_theme', 'braven_setup' );

/**
 * Retrieve the Braven Join/Signup URL domain.
 *
 * @param string $url default URL to return if not configured
 * @return string the Braven Join/Signup URL domain
 */
function braven_join_domain( $join_domain = '' ) {
    if ( defined( 'BRAVEN_JOIN_DOMAIN' ) )
        return untrailingslashit( BRAVEN_JOIN_DOMAIN );
    return $join_domain;
}

/**
 * Retrieve the Braven Portal URL domain.
 *
 * @param string $url default URL to return if not configured
 * @return string the Braven Portal URL domain
 */
function braven_portal_domain( $portal_domain = '' ) {
    if ( defined( 'BRAVEN_PORTAL_DOMAIN' ) )
        return untrailingslashit( BRAVEN_PORTAL_DOMAIN );
    return $portal_domain;
}

/**
 * Enqueue scripts and styles for the front end.
 */
function braven_scripts_styles() {
	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	// Loads JavaScript file with functionality specific to Braven Theme.
	wp_enqueue_script( 'braven-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );

	// Add Genericons font, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.03' );

	// Loads our main stylesheet.
	wp_enqueue_style( 'braven-style', get_stylesheet_uri(), array(), '2013-07-18' );

	// Loads the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'braven-ie', get_template_directory_uri() . '/css/ie.css', array( 'braven-style' ), '20151011' );
	wp_style_add_data( 'braven-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'braven_scripts_styles' );

/**
 * Filter the page title.
 *
 * Creates a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *

 *
 * @param string $title Default title text for current view.
 * @param string $sep   Optional separator.
 * @return string The filtered title.
 */
function braven_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'braven' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'braven_wp_title', 10, 2 );

/**
 * Register widget areas (like sidebars, but not only).
 * What goes in them is defined from the wp-admin Appearance menu > widgets. 
 */
function braven_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Main Widget Area', 'braven' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Appears in the footer section of the site.', 'braven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Homepage CTA' ),
		'id'            => 'hp-cta',
		'description'   => __( 'Appears on the homepage as Discover, Develop & Connect', 'braven' ),
		'before_widget' => '<div class="cta-overlay one_third %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="cta-title">',
		'after_title'   => '</h3>',
	) );

	
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar'),
		'id'            => 'sidebar-primary',
		'description'   => __( 'Appears default on posts and pages in the sidebar.' ),
		'before_widget' => '<div class="sb-sections widget %2$s  general">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="sb-sections-title">',
		'after_title'   => '</h2>',
	) );
	

	register_sidebar( array(
		'name'          => __( 'Blog Sidebar' ),
		'id'            => 'sidebar-blog-sec',
		'description'   => __( 'Appears on the blog.' ),
		'before_widget' => '<div class="sb-sections widget %2$s  blog">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="sb-sections-title">',
		'after_title'   => '</h2>',
	) );
	
		register_sidebar( array(
		'name'          => __( 'Footer Menu' ),
		'id'            => 'fmenu',
		'description'   => __( 'Appears as the footer menu of the site.' ),
		'before_widget' => '<div id="%1$s" class="footer_widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer-title">',
		'after_title'   => '</h3>',
	) );
	
		register_sidebar( array(
		'name'          => __( 'Footer Copyright' ),
		'id'            => 'fcopyright',
		'description'   => __( 'Appears as the copyright of the site.' ),
		'before_widget' => '<div class="footer-copyright">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="footer-title">',
		'after_title'   => '</h3>',
	) );
	
	
}
add_action( 'widgets_init', 'braven_widgets_init' );

if ( ! function_exists( 'braven_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function braven_paging_nav() {
	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 )
		return;
	?>

<nav class="navigation paging-navigation" role="navigation">
	<h1 class="screen-reader-text">
		<?php _e( 'Posts navigation', 'braven' ); ?>
	</h1>
	<div class="nav-links">
		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous">
			<?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'braven' ) ); ?>
		</div>
		<?php endif; ?>
		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next">
			<?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'braven' ) ); ?>
		</div>
		<?php endif; ?>
	</div>
	<!-- .nav-links --> 
</nav>
<!-- .navigation -->
<?php
}
endif;

if ( ! function_exists( 'braven_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
*
*/
function braven_post_nav() {
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
<nav class="navigation post-navigation" role="navigation">
	<h1 class="screen-reader-text">
		<?php _e( 'Post navigation', 'braven' ); ?>
	</h1>
	<div class="nav-links">
		<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'braven' ) ); ?>
		<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'braven' ) ); ?>
	</div>
	<!-- .nav-links --> 
</nav>
<!-- .navigation -->
<?php
}
endif;

if ( ! function_exists( 'braven_entry_meta' ) ) :
/**
 * Print HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own braven_entry_meta() to override in a child theme.
 */
function braven_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . esc_html__( 'Sticky', 'braven' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		braven_entry_date();

	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'braven' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'braven' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'braven' ), get_the_author() ) ),
			get_the_author()
		);
	}
}
endif;

if ( ! function_exists( 'braven_entry_date' ) ) :
/**
 * Print HTML with date information for current post.
 *
 * Create your own braven_entry_date() to override in a child theme.
 *

 *
 * @param boolean $echo (optional) Whether to echo the date. Default true.
 * @return string The HTML-formatted post date.
 */
function braven_entry_date( $echo = true ) {
	if ( has_post_format( array( 'chat', 'status' ) ) )
		$format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'braven' );
	else
		$format_prefix = '%2$s';

	$date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a></span>',
		esc_url( get_permalink() ),
		esc_attr( sprintf( __( 'Permalink to %s', 'braven' ), the_title_attribute( 'echo=0' ) ) ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}
endif;

if ( ! function_exists( 'braven_the_attached_image' ) ) :
/**
 * Print the attached image with a link to the next attached image.
 */
function braven_the_attached_image() {
	/**
	 * Filter the image attachment size to use.
	 *
	
	 *
	 * @param array $size {
	 *     @type int The attachment height in pixels.
	 *     @type int The attachment width in pixels.
	 * }
	 */
	$attachment_size     = apply_filters( 'braven_attachment_size', array( 724, 724 ) );
	$next_attachment_url = wp_get_attachment_url();
	$post                = get_post();

	/*
	 * Grab the IDs of all the image attachments in a gallery so we can get the URL
	 * of the next adjacent image in a gallery, or the first image (if we're
	 * looking at the last image in a gallery), or, in a gallery of one, just the
	 * link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID',
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( reset( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;




/**
 * Load Custom Stylesheet
 */
 
 add_action('wp_enqueue_scripts', 'load_css_files');

function load_css_files() {
    wp_register_style( 'bravenstyle', get_template_directory_uri() . '/css/braven.css');
    wp_register_style( 'BravenTheme', get_stylesheet_uri(), array( 'bravenstyle' ));
    wp_enqueue_style( 'BravenTheme' );
}


/*
DISABLED because it runs on all queries.

Alphabetical ordering by last name for bios (used on staff-page.php) 	
function braven_posts_orderby ($orderby) {
   global $braven_global_orderby;
   if ($braven_global_orderby) $orderby = $braven_global_orderby;
   return $orderby;
}
add_filter('posts_orderby','braven_posts_orderby');
$braven_global_orderby = "
	UPPER(CONCAT(REVERSE(SUBSTRING_INDEX(REVERSE($wpdb->posts.post_title),' ',1)),$wpdb->posts.post_title))
";

/**/

/**
 * Load Dynamic Widget Classes
 */

//add_action('init', 'braven_add_order_classes_for_widgets' );
function braven_add_order_classes_for_widgets() {
    global $wp_registered_sidebars, $wp_registered_widgets;
 
    #Grab the widgets
    $sidebars = wp_get_sidebars_widgets();
 
    if ( empty( $sidebars ) ) {
        return;
    }
 
    #Loop through each widget and change the class names
    foreach ( $sidebars as $sidebar_id => $widgets ) {
        if ( empty( $widgets ) ) {
            continue;
        }
 
        $number_of_widgets = count( $widgets );
        
        foreach ( $widgets as $i => $widget_id ) {
            $wp_registered_widgets[$widget_id]['classname'] .= ' braven-widget-order-' . $i;
 
            # Add first widget class
            if ( 0 == $i ) {
                $wp_registered_widgets[$widget_id]['classname'] .= ' braven-widget-first';
            }
 
            # Add last widget class
            if ( $number_of_widgets == ( $i + 1 ) ) {
                $wp_registered_widgets[$widget_id]['classname'] .= ' braven-widget-last';
            }
        }
    }
}



/**
 * CUSTOM POST TYPES (CPTs):
 ///////////////////////////////////////////////////////////////////////////////////////////////////
 */

/**
 * Init custom post type for Highlights (featured fellows)
 */

add_action( 'init', 'braven_register_cpt_highlights' );

function braven_register_cpt_highlights() {

    $bhlabels = array( 
        'name' => _x( 'Braven Highlights', 'highlights' ),
        'singular_name' => _x( 'highlights', 'highlights' ),
        'add_new' => _x( 'Add New', 'highlights' ),
        'add_new_item' => _x( 'Add New Highlight', 'highlights' ),
        'edit_item' => _x( 'Edit highlight', 'highlights' ),
        'new_item' => _x( 'New Highlight', 'highlights' ),
        'view_item' => _x( 'View highlight', 'highlights' ),
        'search_items' => _x( 'Search highlight', 'highlights' ),
        'not_found' => _x( 'No highlights found', 'highlights' ),
        'not_found_in_trash' => _x( 'No highlights found in Trash', 'highlights' ),
        'parent_item_colon' => _x( 'Parent Highlight:', 'highlights' ),
        'menu_name' => _x( 'Braven Highlights', 'highlights' ),
    );

    $bhargs = array( 
        'labels' => $bhlabels,
        'hierarchical' => false,       
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),       
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'highlights', $bhargs );
}

$bhkey = "highlights";
$bhmeta_boxes = array(
	"fellow-name" => array(
		"name" => "fellow-name",
		"title" => "Person's Name",
		"description" => "Enter the name of the person who is being highlighted.",
		"type"=>"text"
	),
	"fellow-school" => array(
		"name" => "fellow-school",
		"title" => "School",
		"description" => "Enter the school they attended with Braven",
		"type"=>"text"
	),
	"fellow-hometown" => array(
		"name" => "fellow-hometown",
		"title" => "Hometown",
		"description" => "Enter their hometown.",
		"type"=>"text"
	),
	"fellow-education" => array(
		"name" => "fellow-education",
		"title" => "Education",
		"description" => "Enter the education the fellow received.",
		"type"=>"text"
	),
	"fellow-distinctions" => array(
		"name" => "fellow-distinctions",
		"title" => "Awards/Honors",
		"description" => "List the types of honors or distinctions",
		"type"=>"textarea"
	)
);
/* The following functions create a box for signed-in users to edit these highlight fields conveniently. */
add_action( 'admin_menu', 'braven_create_highlights_box' );
add_action( 'save_post', 'braven_save_bhmeta_box' );
add_action( 'init', 'braven_create_highlights_taxonomies', 0 );

function braven_create_highlights_box() {
	global $bhkey;
	 
	if( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'new-bhmeta-boxes', ucfirst( $bhkey ) . ' Information', 'braven_display_highlights_box', 'highlights', 'normal', 'high'	);
	}
}
/* see note above */
function braven_display_highlights_box() {
	global $post, $bhmeta_boxes, $bhkey;
	echo '<div class="form-wrap">';
		echo wp_nonce_field( plugin_basename( __FILE__ ), $bhkey . '_wpnonce', false, true );
		  
		foreach($bhmeta_boxes as $bhmeta_box) {
			$bhdata = get_post_meta($post->ID, $bhkey, true);
		
			echo '<div class="form-field form-required">';
				echo '<label for="', $bhmeta_box[ 'name' ],'">';
				echo $bhmeta_box[ 'title' ]; 
				echo '</label>';
			
				switch ($bhmeta_box['type'] ) {
					case 'text':
						echo '<input type="text" name="', $bhmeta_box[ 'name' ],'" value="',$bhdata[$bhmeta_box[ 'name' ]],'" />'; 
						echo '<br />';
						echo $bhmeta_box[ 'description' ];
						break;
					case 'textarea':
						echo '<textarea name="', $bhmeta_box[ 'name' ],'">';
						echo htmlspecialchars( $bhdata[ $bhmeta_box[ 'name' ] ] );
						echo '</textarea> ';
						echo '<br />';
						echo $bhmeta_box[ 'description' ];
						break;
					default;
				} // switch
			echo '</div>';
			} // foreach 
	echo '</div>'; //<div class="form-wrap">
}
/* see note above */
function braven_save_bhmeta_box( $post_id ) {
	global $post, $bhmeta_boxes, $bhkey;
	 
	foreach( $bhmeta_boxes as $bhmeta_box ) {
		if (isset($_POST[ $bhmeta_box[ 'name' ] ])) {
			$bhdata[ $bhmeta_box[ 'name' ] ] = $_POST[ $bhmeta_box[ 'name' ] ];
		}
	}
 
	if (!isset($_POST[ $bhkey . '_wpnonce' ])) 
		return $post_id;

	if ( !wp_verify_nonce( $_POST[ $bhkey . '_wpnonce' ], plugin_basename(__FILE__) ) )
		return $post_id;
 
	if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;
 
	update_post_meta( $post_id, $bhkey, $bhdata );
}
function braven_create_highlights_taxonomies() {
    register_taxonomy(
        'braven_categories',
        'highlights',
        array(
            'labels' => array(
                'name' => 'Fellows Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => "New Fellows Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}


/**
 * END Braven Highlights
 */




/**
 * Init custom post type for Staff & Board Members
 */

add_action( 'init', 'braven_register_cpt_staff' );

function braven_register_cpt_staff() {

    $staff_labels = array( 
        'name' => _x( 'Staff', 'staff' ),
        'singular_name' => _x( 'testimonial', 'staff' ),
        'add_new' => _x( 'Add New', 'Staff Member' ),
        'add_new_item' => _x( 'Add New Staff Member', 'staff' ),
        'edit_item' => _x( 'Edit Staff', 'staff' ),
        'new_item' => _x( 'New staff', 'staff' ),
        'view_item' => _x( 'View staff', 'staff' ),
        'search_items' => _x( 'Search Staff', 'staff' ),
        'not_found' => _x( 'No staff found', 'staff' ),
        'not_found_in_trash' => _x( 'No staff found in Trash', 'staff' ),
        'parent_item_colon' => _x( 'Parent staff:', 'staff' ),
        'menu_name' => _x( 'Braven Team', 'staff' ),
    );

    $staff_args = array( 
        'labels' => $staff_labels,
        'hierarchical' => false,    
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'staff', $staff_args );
}

$st_key = "staff";
$staff_meta_boxes = array(
	"staff-first-name" => array(
		"name" => "staff-first-name",
		"title" => "First Name",
		"description" => "Enter the first name of the person.",
		"type"=>"text"
	),
	"staff-last-name" => array(
		"name" => "staff-last-name",
		"title" => "Last Name",
		"description" => "Enter the surname of the person.",
		"type"=>"text"
	),
	"staff-position" => array(
		"name" => "staff-position",
		"title" => "Position in Company",
		"description" => "Enter their position in Braven.",
		"type"=>"text"
	),
	"staff-hometown" => array(
		"name" => "staff-hometown",
		"title" => "Hometown",
		"description" => "Enter their hometown.",
		"type"=>"text"
	),
	"staff-education" => array(
		"name" => "staff-education",
		"title" => "Education",
		"description" => "Degrees and Experience.",
		"type"=>"textarea"
	),
	"staff-link" => array(
		"name" => "staff-link",
		"title" => "Staff Email",
		"description" => "Enter the email address of your staff member.",
		"type"=>"text"
	)
);
 
function braven_create_staff_meta_box() {
	global $st_key;
	if( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'new-meta-staff', ucfirst( $st_key ) . ' Information', 'display_meta_staff', 'staff', 'normal', 'high' );
	}
}
 
function display_meta_staff() {
	global $post, $staff_meta_boxes, $st_key;

	echo '<div class="form-wrap">';
	echo wp_nonce_field( plugin_basename( __FILE__ ), $st_key . '_wpnonce', false, true );
  
	foreach($staff_meta_boxes as $staff_meta_box) {
		$st_data = get_post_meta($post->ID, $st_key, true);

		echo '<div class="form-field form-required">';
		echo '<label for="', $staff_meta_box[ 'name' ],'">';
		echo $staff_meta_box[ 'title' ]; 
		echo '</label>';

		switch ($staff_meta_box['type'] ) {
			case 'text':
				echo '<input type="text" name="', $staff_meta_box[ 'name' ],'" value="',$st_data[$staff_meta_box[ 'name' ]],'" />'; 
				echo '<br />';
				echo $staff_meta_box[ 'description' ];
				break;
			case 'textarea':
				echo '<textarea name="', $staff_meta_box[ 'name' ],'">';
				echo htmlspecialchars( $st_data[ $staff_meta_box[ 'name' ] ] );
				echo '</textarea> ';
				echo '<br />';
				echo $staff_meta_box[ 'description' ];
				break;
			default;
		}
		
		echo '</div>';
	}
}
 
function save_meta_staff( $post_id ) {
	global $post, $staff_meta_boxes, $st_key;
 
	foreach( $staff_meta_boxes as $staff_meta_box ) {
		if (isset($_POST[ $staff_meta_box[ 'name' ] ])) {
			$st_data[ $staff_meta_box[ 'name' ] ] = $_POST[ $staff_meta_box[ 'name' ] ];
		}
	}
	 
	if (!isset($_POST[ $st_key . '_wpnonce' ])) 
		return $post_id;

	if ( !wp_verify_nonce( $_POST[ $st_key . '_wpnonce' ], plugin_basename(__FILE__) ) )
		return $post_id;
 
	if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;
 
	update_post_meta( $post_id, $st_key, $st_data );
}
 
//add_action( 'admin_menu', 'braven_create_meta_staff' );
add_action( 'save_post', 'save_meta_staff' );


add_action( 'init', 'create_staff_taxonomies', 0 );

function create_staff_taxonomies() {
    register_taxonomy(
        'staff_categories',
        'staff',
        array(
            'labels' => array(
                'name' => 'Staff Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => "New Staff Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}



/**
 * END Staff & Board Members
 */


/**
 * Init custom post type for Testimonials
 */

add_action( 'init', 'braven_register_cpt_testimonial' );

function braven_register_cpt_testimonial() {

    $labels = array( 
        'name' => _x( 'Testimonials', 'testimonial' ),
        'singular_name' => _x( 'testimonial', 'testimonial' ),
        'add_new' => _x( 'Add New', 'testimonial' ),
        'add_new_item' => _x( 'Add New testimonial', 'testimonial' ),
        'edit_item' => _x( 'Edit testimonial', 'testimonial' ),
        'new_item' => _x( 'New testimonial', 'testimonial' ),
        'view_item' => _x( 'View testimonial', 'testimonial' ),
        'search_items' => _x( 'Search Testimonials', 'testimonial' ),
        'not_found' => _x( 'No testimonials found', 'testimonial' ),
        'not_found_in_trash' => _x( 'No testimonials found in Trash', 'testimonial' ),
        'parent_item_colon' => _x( 'Parent testimonial:', 'testimonial' ),
        'menu_name' => _x( 'Testimonials', 'testimonial' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'revisions' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'testimonial', $args );
}

$key = "testimonial";
$testimonial_meta_boxes = array(
	"person-name" => array(
	"name" => "person-name",
	"title" => "Person's Name",
	"description" => "Enter the name of the person who gave you the testimonial."),
	"position" => array(
	"name" => "position",
	"title" => "Position in Company",
	"description" => "Enter their position in their specific company."),
	"company" => array(
	"name" => "company",
	"title" => "Company Name",
	"description" => "Enter the client Company Name"),
	"link" => array(
		"name" => "link",
		"title" => "Client Link",
		"description" => "Enter the link to client's site, or you can enter the link to your portfolio page where you have the client displayed."
	)
);
 
function braven_create_testimonial_meta_box() {
	global $key;
 
	if( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'new-meta-boxes', ucfirst( $key ) . ' Information', 'braven_display_testimonial_meta_box', 'testimonial', 'normal', 'high' );
	}
}
 
function braven_display_testimonial_meta_box() {
	global $post, $testimonial_meta_boxes, $key;

	echo '<div class="form-wrap">';
	wp_nonce_field( plugin_basename( __FILE__ ), $key . '_wpnonce', false, true );
	foreach($testimonial_meta_boxes as $meta_box) {
		$data = get_post_meta($post->ID, $key, true);
		echo '<div class="form-field form-required">';
			echo '<label for="'.$meta_box[ 'name' ].'">'.$meta_box[ 'title' ].'</label>';
			echo '<input type="text" name="'.$meta_box[ 'name' ].'" value="'.(isset($data[ $meta_box[ 'name' ] ]) ? htmlspecialchars( $data[ $meta_box[ 'name' ] ] ) : '').'" />';
			echo '<p>'.$meta_box[ 'description' ].'</p>';
		echo '</div>';
	}
	echo '</div>';
}
 
function braven_save_testimonial_meta_box( $post_id ) {
	global $post, $testimonial_meta_boxes, $key;
 
	foreach( $testimonial_meta_boxes as $testimonial_meta_box ) {
		if (isset($_POST[ $testimonial_meta_box[ 'name' ] ])) {
			$data[ $testimonial_meta_box[ 'name' ] ] = $_POST[ $testimonial_meta_box[ 'name' ] ];
		}
	}
 
	if (!isset($_POST[ $key . '_wpnonce' ])) 
		return $post_id;

	if ( !wp_verify_nonce( $_POST[ $key . '_wpnonce' ], plugin_basename(__FILE__) ) )
		return $post_id;
 
	if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;
 
	update_post_meta( $post_id, $key, $data );
}
 
add_action( 'admin_menu', 'braven_create_testimonial_meta_box' );
add_action( 'save_post', 'braven_save_testimonial_meta_box' );



/**
 * END TESTIMONIALS
 */


/**
 * Init custom post type for Partners
 */

add_action( 'init', 'braven_register_cpt_partners' );



function braven_register_cpt_partners() {

	$plabels = array( 
		'name' => _x( 'Partners', 'partner' ),
		'singular_name' => _x( 'partner', 'partner' ),
		'add_new' => _x( 'Add New', 'partner' ),
		'add_new_item' => _x( 'Add New Partner', 'partner' ),
		'edit_item' => _x( 'Edit partner', 'partner' ),
		'new_item' => _x( 'New partner', 'partner' ),
		'view_item' => _x( 'View partner', 'partner' ),
		'search_items' => _x( 'Search Partners', 'partner' ),
		'not_found' => _x( 'No partners found', 'partner' ),
		'not_found_in_trash' => _x( 'No partners found in Trash', 'partner' ),
		'parent_item_colon' => _x( 'Parent Partner:', 'partner' ),
		'menu_name' => _x( 'Braven Partners', 'partner' ),
	);

	$pargs = array( 
		'labels' => $plabels,
		'hierarchical' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail' ),
		'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_menu' => true,     
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post'
	);

	register_post_type( 'partner', $pargs );
}

$pkey = "partner";
$pmeta_boxes = array(
	"pcompany-name" => array(
		"name" => "pcompany-name",
		"title" => "Company Name",
		"description" => "Enter the name of the company who supports Braven."
	),
	"plink" => array(
		"name" => "plink",
		"title" => "Partner Link",
		"description" => "Enter the link to company's site."
	)
);
 
function braven_create_partner_meta_box() {
	global $pkey;
	 
	if( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'p-meta-boxes', ucfirst( $pkey ) . ' Company Information', 'display_partner_meta_box', 'partner', 'normal', 'high' );
	}
}
 
function display_partner_meta_box() {
	global $post, $pmeta_boxes, $pkey;
?>
<div class="form-wrap">
	<?php
wp_nonce_field( plugin_basename( __FILE__ ), $pkey . '_wpnonce', false, true );
 
foreach($pmeta_boxes as $pmeta_box) {
$pdata = get_post_meta($post->ID, $pkey, true);
?>
	<div class="form-field form-required">
		<label for="<?php echo $pmeta_box[ 'name' ]; ?>"><?php echo $pmeta_box[ 'title' ]; ?></label>
		<input type="text" name="<?php echo $pmeta_box[ 'name' ]; ?>" value="<?php echo (isset($pdata[ $pmeta_box[ 'name' ] ]) ? htmlspecialchars( $pdata[ $pmeta_box[ 'name' ] ] ) : ''); ?>" />
		<p><?php echo $pmeta_box[ 'description' ]; ?></p>
	</div>
	<?php } ?>
</div>
<?php
}
 
function braven_save_partner_meta_box( $post_id ) {
global $post, $pmeta_boxes, $pkey;
 
foreach( $pmeta_boxes as $pmeta_box ) {
if (isset($_POST[ $pmeta_box[ 'name' ] ])) {
$pdata[ $pmeta_box[ 'name' ] ] = $_POST[ $pmeta_box[ 'name' ] ];
}
}
 
if (!isset($_POST[ $pkey . '_wpnonce' ])) 
return $post_id;

if ( !wp_verify_nonce( $_POST[ $pkey . '_wpnonce' ], plugin_basename(__FILE__) ) )
return $post_id;
 
if ( !current_user_can( 'edit_post', $post_id ))
return $post_id;
 
update_post_meta( $post_id, $pkey, $pdata );
}
 
add_action( 'admin_menu', 'braven_create_partner_meta_box' );
add_action( 'save_post', 'braven_save_partner_meta_box' );


add_action( 'init', 'create_partner_taxonomies', 0 );

function create_partner_taxonomies() {
    register_taxonomy(
        'partner_categories',
        'partner',
        array(
            'labels' => array(
                'name' => 'Partner Category',
                'add_new_item' => 'Add New Category',
                'new_item_name' => "New Partner Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
    );
}

/*
 * END Custom Post Types 
 /////////////////////////////////////////////////////////////////////////////////////////
 */

//Custom Breadcrumbs

function braven_the_breadcrumb() {
    global $post;
	 echo '<div id="breadcrumb-wrapper">'.
				'<div class="breadcrumb-inner">'.
					'<ul id="breadcrumbs">';
    if (!is_home()) {
        echo '<li class="hico"><a class="home_icon" href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo '</a></li><li class="separator"> &raquo; </li>';
        if (is_category() || is_single()) {
            echo '<li>';
            the_category(' </li><li class="separator"> &raquo; </li><li> ');
            if (is_single()) {
                echo '</li><li class="separator"> &raquo; </li><li>';
                the_title();
                echo '</li>';
            }
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {
                    $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">&raquo;</li>';
                }
                echo $output;
                echo $title;
            } else {
                echo '<li><strong> '.get_the_title().'</strong></li>';
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
    elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
    elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
    elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
    elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
    echo 		'</ul>'.
	 			'</div>'.
			'</div>';
}


// Enable sort By Popular Post

function braven_set_post_views($postID) {
    $count_key = 'braven_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
//To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// Custom Excerpt - allow length limit as a parameter.

function braven_excerpt($num) {
    $limit = $num+1;
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    array_pop($excerpt);
    $excerpt = implode(' ',$excerpt).'... <br />&nbsp;<br /><a class="rmore" href="' .get_permalink() .'">Read more</a>';
    echo $excerpt;
}



// Retrieve first image from blog post
// DONT THINK THIS IS USED ANYWHERE, REMOVE IF YOU SEE THIS AFTER 2015-10-20... - EHUD
/*
function catch_that_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches[1][0];

  if(empty($first_img)) {
    $first_img = "/wp-content/uploads/2015/08/braven_profile.jpg";
  }
  return $first_img;
}

*/


/**
 * Custom Pagination
 */

function pagination($pages = '', $range = 4)
{  
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\">";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function braven_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'braven_sectionid',
			__( 'Pull Quote For Page', 'braven_textdomain' ),
			'braven_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'braven_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function braven_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'braven_save_meta_box_data', 'braven_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

	echo '<label for="braven_new_field">';
	_e( 'Pull Quote Area for Page', 'braven_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="braven_new_field" name="braven_new_field" value="' . esc_attr( $value ) . '" size="25" />';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function braven_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['braven_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['braven_meta_box_nonce'], 'braven_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['braven_new_field'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['braven_new_field'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}
add_action( 'save_post', 'braven_save_meta_box_data' );





/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Single or multiple authors.
 * 2. Active widgets in the sidebar to change the layout and spacing.
 * 3. When avatars are disabled in discussion settings.
 *

 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function braven_body_class( $classes ) {
	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_active_sidebar( 'sidebar-2' ) && ! is_attachment() && ! is_404() )
		$classes[] = 'sidebar';

	if ( ! get_option( 'show_avatars' ) )
		$classes[] = 'no-avatars';

	return $classes;
}
add_filter( 'body_class', 'braven_body_class' );


/**
 * Add postMessage support for site title and description for the Customizer.
 *

 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function braven_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'braven_customize_register' );

/**
 * Enqueue Javascript postMessage handlers for the Customizer.
 *
 * Binds JavaScript handlers to make the Customizer preview
 * reload changes asynchronously.
 */
function braven_customize_preview_js() {
	wp_enqueue_script( 'braven-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20141120', true );
}
add_action( 'customize_preview_init', 'braven_customize_preview_js' );

/**
 * Load Staff Shortcode Plugin
 */

add_shortcode('bravenstaff', 'braven_staff_list');

function braven_staff_list( $atts ) {
	ob_start();
	// define attributes and their defaults
	extract( 
		shortcode_atts( 
			array (
				'type' => 'post',
				'order' => '',
				'orderby' => '',
				'posts' => '',
				'staff_categories' => '',
				'category' => '',
				'posts_per_page' => '',
			), 
		$atts) 
	);
	
	// define query parameters based on attributes
	$staff_args = array(
		'post_type' => $type,
		'order' => 'ASC',
		'orderby' => 'title',
		'posts_per_page' => '-1',
		'staff_categories' => $staff_categories,
		'category_name' => $category
	);
	
	$count = 1;
	$query = new WP_Query( $staff_args );
	$braven_global_orderby = ''; 
	if ( $query->have_posts() ) : ?>
		<div class="team-section">
			<?php 
			while ( $query->have_posts() ) : $query->the_post();
				$staffdata = get_post_meta( $query->post->ID, 'staff', true ); ?>
				<div class="fstaff one_quarter">
					<div class="pic pic-3d">
						<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail('staff-thumb');
						} else { ?>
							<img src="/wp-content/uploads/2015/08/braven_profile.jpg" alt="<?php the_title(); ?>" />
						<?php } ?>
						<div class="pic-caption left-to-right">
							<div class="staffboard-title">
								<h1><?php echo $staffdata[ 'staff-first-name' ]; ?> <?php echo $staffdata[ 'staff-last-name' ]; ?></h1>
								<p><?php echo $staffdata[ 'staff-position' ]; ?></p>
							</div>
							<a class="btn-success" href="#openModal-<?php echo $count?>">Read More</a>
						</div><!--.pic-caption-->
					</div><!--.pic-->
				</div><!--.fstaff-->
				<div id="openModal-<?php echo $count?>" class="modalDialog">
					<div>
						<a href="#close" title="Close" class="close">X</a>
						<span class="modal-pic">
							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail('staff-thumb');
							} else { ?>
								<img src="https://bebraven.org/wp-content/uploads/2015/08/braven_profile.jpg" alt="<?php the_title(); ?>" />
							<?php } ?>
						</span>
						<div class="modal-tag">
							<strong><?php echo $staffdata[ 'staff-first-name' ]; ?> <?php echo $staffdata[ 'staff-last-name' ]; ?></strong>
							<?php echo $staffdata[ 'staff-position' ]; ?><br />
							<span><b>Hometown:</b></span> <?php echo $staffdata[ 'staff-hometown' ]; ?><br />
							<?php if ($staffdata[ 'staff-link' ]) { ?>
								<p class="education"><b>E-mail:</b> <a href="mailto:<?php echo $staffdata[ 'staff-link' ]; ?>"><?php echo $staffdata[ 'staff-link' ]; ?></a></p>
							<?php }?>
						</div>
						<hr />
						<div class="modal-bio">
							<?php the_content();?>
						</div>
					</div>
				</div><!--#openModal-xxx-->
				<?php $count++; ?>
			<?php endwhile;?>
		</div><!-- .team-section -->
		<?php 
		wp_reset_postdata();
		$myvariable = ob_get_clean();
		return $myvariable;
	endif; 
	{
		$myvariable = ob_get_clean();
		return $myvariable;
	}
} // End staff list shortcode

// fixing a bug with sharify plugin:
$twitter_btn_icon_align;
