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
                    'posts_per_page' => '2',
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
          echo '<button id="load_more">Load More</button>'; 
 
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

// creating review form

function review_form(){
   
    if(isset($_POST['submit'])){
    	 global $wpdb;
    	 echo "adarsh";
    	 $wpdb->insert("wp_user_review",
            array("title"=>$_POST['u_title'],
                   "rating"=>$_POST['u_rating'],
                "email"=>$_POST['u_email'],
                "query"=>$_POST['u_query'])
                );
    }
             ?>
	<div class="container">
		<form action="" method="post">
		  <h2>Write a Riview</h2>
		  <div class="form-group">
		  	 <label for="u_title">Riview Title</label>
		  	 <input type="text" name="u_title" placeholder="Riview Title">
		  </div>
		  <div class="form-group">
		   	 <h3>Rating Review</h3>
		   	 <div class="rating-wraper">
		   	    <input type="radio" name="u_rating" value="5" id="star-5" ><label for="star-5"></label>
		   	 	<input type="radio" name="u_rating" value="4" id="star-4" ><label for="star-4"></label>
		   	 	<input type="radio" name="u_rating" value="3" id="star-3" ><label for="star-3"></label>
		   	 	<input type="radio" name="u_rating" value="2" id="star-2" ><label for="star-2"></label>
		   	 	<input type="radio" name="u_rating" value="1" id="star-1" ><label for="star-1"></label>
   	        </div>
          </div>
		  <div class="form-group">
		     <textarea placeholder="Ask Your Query" name="u_query"></textarea>
		  </div>
		  <button type="submit" name="submit">Submit</button>
	    </form>
	</div>
	<style type="">
		@import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css);
        form{
        	margin-top: 1rem;
        }
		.form-group{
			margin-bottom: 1rem;
		}
		.rating-wraper{
			direction: rtl;
			margin-top: -30px;
			margin-bottom: 50px;
			margin-right:260px;
		}
		.rating-wraper input{
			display: none;
		}
		.rating-wraper label{
			display: inline-block;
			width: 50px;
			position: relative;
			cursor: pointer;
		}
		.rating-wraper label:before{
			content: '\2605';
			position: absolute;
			font-size: 40px;
			display: inline-block;
			top:0;
			left: 0;
		}
		.rating-wraper label:after{
			content: '\2605';
			position: absolute;
			font-size: 40px;
			display: inline-block;
			top:0;
			left: 0;
			color: yellow;
			opacity: 0; 
		}
		.rating-wraper label:hover:after,
		.rating-wraper label:hover~label:after,
		.rating-wraper input:checked~label:after{
			opacity: 1;
		}
	</style>
	</style>
	<?php
}

add_shortcode('form','review_form');
//generating table
function user_review_table(){
	global $wpdb;
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	if(count($wpdb->get_var('SHOW TABLES LIKE "wp_user_review"'))==0){
		$sql_query_to_create_table="CREATE TABLE `wp_user_review` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `title` varchar(150) NOT NULL,
			 `rating` int(11) NOT NULL,
			 `email` varchar(150) NOT NULL,
			 `query` text NOT NULL,
			 PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1
			";
		dbDelta($sql_query_to_create_table);
	}
}
register_activation_hook(__FILE__,'user_review_table');

// deactivate table
function user_review_drop_table(){
	global $wpdb;
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    $wpdb->query('DROP table if Exists wp_user_review');

    //step-1: we get the id of the post page
    //delete the page from table

    $the_post_id=get_option("plugin_page"); // getting the id of the post name (plugin_page)
    if(!empty($the_post_id)){
    	wp_delete_post($the_post_id, true);
    }
}
register_deactivation_hook(__FILE__,"user_review_drop_table");



//load more button
add_action( 'wp_footer', 'my_action_javascript' ); // Write our JS below here

function my_action_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		var page_count='<?php echo ceil(wp_count_posts('post')->publish/2); ?>';
		var ajaxurl='<?php echo admin_url('admin-ajax.php');?>';
		var page=2;
        jQuery('#load_more').click(function(){
		var data = {
			'action': 'my_action',
			'whatever': page,
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.book-item').append(response);
			if(page_count==page){
				jQuery('#load_more').hide();
			}
			page=page + 1;
		});
	});
   });
	</script> <?php
}
add_action( 'wp_ajax_my_action', 'my_action' );
add_action( 'wp_ajax_nopriv_my_action', 'my_action' );
function my_action() {
	global $wpdb; // this is how you get access to the database
        $args=array(
   'post_type'=>'books',
   'paged'=>$_POST['page'],
   );
	$the_query = new WP_Query( $args );
	 
	// The Loop
	if ( $the_query->have_posts() ) {
	    while ( $the_query->have_posts() ) {
	        $the_query->the_post();
	        echo '<li>' . get_the_title() . '</li>';
	    }
	} else {
	    // no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	wp_die(); 
}
