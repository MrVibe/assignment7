<?php
/*
Plugin Name: custom book
Description: This is a test book plugin,which is used by the students. 
Author: Adarsh Kumar Shah
Text Domain:custom_book-plugin
Version: 1.0
*/
// add_menu_page() is used to create a menu in the admin pannel

// defining constants

define("PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));
define("PLUGIN_URL",plugins_url());

define("PLUGIN_VERSION", '1.0');

// register custom post type book
add_action( 'init', 'create_post_book_type' );
function create_post_book_type() {  // books custom post type
    // set up labels
    $labels = array(
        'name' => 'Books',
        'singular_name' => 'Book Item',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Book Item',
        'edit_item' => 'Edit Book Item',
        'new_item' => 'New Book Item',
        'all_items' => 'All Books',
        'view_item' => 'View Book Item',
        'search_items' => 'Search Books',
        'not_found' =>  'No Books Found',
        'not_found_in_trash' => 'No Books found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Books',
    );
    register_post_type(
        'books',
        array(
            'labels' => $labels,
            'has_archive' => true,
            'public' => true,
            'hierarchical' => true,
            'supports' => array( 'title', 'editor', 'excerpt', 'custom-fields', 'thumbnail','page-attributes' ),
            'taxonomies' => array( 'post_tag', 'category' ),
            'exclude_from_search' => true,
            'capability_type' => 'post',
        )
    );
}
 
// register two taxonomies to go with the post type
add_action( 'init', 'create_taxonomies', 0 );
function create_taxonomies() {
    // color-type taxonomy
    $labels = array(
        'name'              => _x( 'Book-types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Book-type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Book-types' ),
        'all_items'         => __( 'All Book-types' ),
        'parent_item'       => __( 'Parent Book-type' ),
        'parent_item_colon' => __( 'Parent Book-type:' ),
        'edit_item'         => __( 'Edit Book-type' ),
        'update_item'       => __( 'Update Book-type' ),
        'add_new_item'      => __( 'Add New Book-type' ),
        'new_item_name'     => __( 'New Book-type' ),
        'menu_name'         => __( 'Book-types' ),
    );
    register_taxonomy(
        'Book-type',
        'books',
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'query_var' => true,
            'rewrite' => true,
            'show_admin_column' => true
        )
    );
    // fabric taxonomy
    $labels = array(
        'name'              => _x( 'Authors', 'taxonomy general name' ),
        'singular_name'     => _x( 'Author', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Author' ),
        'all_items'         => __( 'All Authors' ),
        'parent_item'       => __( 'Parent Author' ),
        'parent_item_colon' => __( 'Parent Author:' ),
        'edit_item'         => __( 'Edit Author' ),
        'update_item'       => __( 'Update Author' ),
        'add_new_item'      => __( 'Add New Author' ),
        'new_item_name'     => __( 'New Author' ),
        'menu_name'         => __( 'Authors' ),
    );
    register_taxonomy(
        'Author',
        'books',
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'query_var' => true,
            'rewrite' => true,
            'show_admin_column' => true
        )
    );
}

function custum_book_plugin(){
	// css and js file
	wp_enqueue_style("custum_book_plugin_style", // unique name
		PLUGIN_URL."/custom_book/assest/css/style.css", // css file path
	'', // dependency on other file
    PLUGIN_VERSION);

    wp_enqueue_script("custum_book_plugin_script", // unique name
		PLUGIN_URL."/custom_book/assest/js/script.js", // css file path
	'', // dependency on other file
    PLUGIN_VERSION,
    true);
    $object_array=array(
      "Name"=>"Online Solutions",
      "Author"=>"Adarsh",
      "ajaxurl"=>admin_url('admin-ajax')
    );
    wp_localize_script("custum_book_plugin_script","online_book_management" ,$object_array);
}
add_action("init","custum_book_plugin");


function diwp_create_shortcode_movies_post_type(){
 
    $args = array(
                    'post_type'      => 'books',
                    'posts_per_page' => '9',
                    'publish_status' => 'published',
                 );
 
    $query = new WP_Query($args);
 
    if($query->have_posts()) :
 
        while($query->have_posts()) :
 
            $query->the_post() ;
                     
        $result .= '<div class="book-item">';
        $result .= '<div class="book-image">' . get_the_post_thumbnail() . '</div>';
        $result .= '<div class="book-name">' . get_the_title() . '</div>';
        $result .= '<div class="book-desc">' . get_the_content() . '</div>'; 
        $result .= '</div>';
 
        endwhile;
 
        wp_reset_postdata();
 
    endif;    
 
    return $result;            
}
 
add_shortcode( 'book-list', 'diwp_create_shortcode_movies_post_type');


// implementing isotope on book list

add_shortcode('isotope',function($atts,$content=null){
	
	wp_enqueue_script('isotope-js','https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js',array(),true);
	
	$query = new WP_Query(array(
		'post_type'=>'books',
		'posts_per_page'=>9
	));
	
	if($query->have_posts()){
		$posts = [];
		$all_categories=[];
		$all_tags = [];
		while($query->have_posts()){
			$query->the_post();
			global $post;
			$category = wp_get_object_terms($post->ID,'category');
			$tag = wp_get_object_terms($post->ID,'post_tag');
			if(!empty($category)){
				$post->cats=[];
				foreach($category as $cat){
                     $post->cats[]=$cat->slug;
					if(!in_array($cat->term_id,array_keys($all_categories))){
						$all_categories[$cat->term_id]=$cat;
					}
				}
			}
			if(!empty($tag)){
				$post->tags=[];
				foreach($tag as $t){
					$post->tags[] = $t->slug;
					if(!in_array($t->term_id,array_keys($all_tags))){
						$all_tags[$t->term_id]=$t;
					}
				}
			}
			$posts[] = $post;
		}
		wp_reset_postdata();

		echo '<div class="isotope_wrapper"><div>';
		if(!empty($all_categories)){
			?>
			<ul class="post_categories">
			<?php
			 	foreach($all_categories as $category){
					?>
				<li class="grid-selector" data-filter="<?php echo $category->slug; ?>"><?php echo $category->name; ?></li>
				     <?php
				}
			?>
			</ul>
			<?php
		}
		if(!empty($all_tags)){
			?>
			<ul class="post_tags">
			<?php
			 	foreach($all_tags as $category){
					?>
				<li class="grid-subselector" data-filter="<?php echo $category->slug; ?>"><?php echo $category->name; ?></li>
				     <?php
				}
			?>
			</ul>
			<?php
		}
		?>
		</div>
		<div class="grid">
		<?php
		foreach($posts as $post){
			?>
			<div class="grid-item <?php echo empty($post->cats)?'':implode(',',$post->cats); ?> <?php echo empty($post->tags)?'':implode(',',$post->tags); ?>">
				
				<h2>
					<a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a>
				</h2>
			</div>
			<?php
		}
		?>
		</div></div>
		<script>
			window.addEventListener('load',function(){
				var iso = new Isotope( document.querySelector('.grid'), {
				  itemSelector: '.grid-item',
				  layoutMode: 'fitRows'
				});
				document.querySelectorAll('.grid-selector').forEach(function(el){

					el.addEventListener('click',function(){
						
						let sfilter = el.getAttribute('data-filter');

						iso.arrange({
						  filter: function( gridIndex, itemElem ) {
						    return itemElem.classList.contains(sfilter);
						  }
						});
						
					});
				});


				document.querySelectorAll('.grid-subselector').forEach(function(el){

					el.addEventListener('click',function(){
						
						let sfilter = el.getAttribute('data-filter');

						iso.arrange({
						  filter: function( gridIndex, itemElem ) {
						    return itemElem.classList.contains(sfilter);
						  }
						});
						
					});
				});
				
			});
		</script>
		<style>
			.isotope_wrapper {
			    display: flex;
			    flex-direction: column;
			}

			.isotope_wrapper > div {
			    display: flex;
			    flex-direction: row;
			    flex-wrap: wrap;
			    margin: 0 -1rem;
			    justify-content: space-between;
			}

			.isotope_wrapper > div > ul {
			    display: flex;
			    flex-wrap: wrap;
			    margin: 1rem;
			}

			.isotope_wrapper > div>div {
			    padding: 1rem;
			    border: 1px solid #eee;
			    margin: 1rem;
			}

			.isotope_wrapper > div > ul > li {
			    padding: 0.5rem 1rem;
			    background: #eee;
			    margin: 2px;cursor:pointer;
			    border-radius: 4px;
			}
		</style>
		<?php
	}
});
