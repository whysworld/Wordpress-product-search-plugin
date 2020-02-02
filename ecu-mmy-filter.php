<?php
/*
* Plugin Name: ECU MMY Filter
* Description: ECU MMY Filter Shortcode
* Version: 1.0
* Author: Shao
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// enqueue css and js
function ecu_mmy_filter_enqueue_scripts(){
	//style css
	wp_register_style( 'ecu_mmy_filter_css', plugins_url( "/assets/css/style.css", __FILE__ ) );
	wp_enqueue_style( 'ecu_mmy_filter_css');
    //plugin js
    wp_register_script( 'ecu_mmy_filter_enqueue_js', 
    	plugins_url( '/assets/js/function.js', __FILE__ ), 
    	array('jquery') , 
    	null, 
    	true
    );
    wp_enqueue_script( 'ecu_mmy_filter_enqueue_js');
    //data for ajax request
    wp_localize_script('ecu_mmy_filter_enqueue_js', 
    	'ecu_ajax_object', 
    	array(
    		'plugin_url' => plugins_url( '', __FILE__ ),
    		'site_url' => site_url(),
    		// 'nonce' => wp_create_nonce( 'ajax-nonce' ),
    		'ajax_url' => admin_url( 'admin-ajax.php' )
    	)
    );
}
add_action('wp_enqueue_scripts', 'ecu_mmy_filter_enqueue_scripts');
add_action( 'wp_ajax_nopriv_ajaxlogin','ajax_login' );

function ajax_login(){
  //nonce-field is created on page
  check_ajax_referer('ajax-login-nonce','security');
  //CODE
  die();
}
//model/make/year/engine chnage - ajax reqeust
add_action( "wp_ajax_nopriv_change_selector", "ecu_mmy_filter_change_selector" );
add_action( "wp_ajax_change_selector", "ecu_mmy_filter_change_selector" );
function ecu_mmy_filter_change_selector(){
    $parent = $_POST['parent'];
    wp_send_json(json_encode(ecu_mmy_filter_categories($parent)));
	wp_die();
}

// get categories: Model/Make/Year/Engine
function ecu_mmy_filter_categories($parent){
	$args = array(
		'taxonomy'	 => 'product_cat',
		'orderby'    => 'name',
		'order'      => 'asc',
		'hide_empty' => false,
		'hierarchical' => false,
		'parent'	=> (int)$parent,
	);
	return get_terms( $args );
}

function ecu_mmy_filter_shortcode($atts){
	$models = ecu_mmy_filter_categories(0);
	$exclude = array("uncategorized"); //exclude unneccessary categories like the uncategorized
	$props = shortcode_atts( array(
		'mode' => 'block',
		'title' => 'Engine Computers & Auto Modules',
		'desc' => 'Plug and Play Shop by Vehicle',
		'where' => 'sidebar',
		'link' => 'ecu-repair-request'
	), $atts );
	
	if($props["mode"] ==="inline"){
			?>
		<div style="" id='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper' class="ecu-mmy-filter-wrapper ecu-mmy-filter-<?php echo $props['where']  ?>">
			<div style="padding:20px" class='container ecu-wrapper'>
				<div class='ecu-mmy-filter-title'>
					<h3><?php echo $props['title'] ?></h3>
				</div>
				<div class='ecu-mmy-filter-desc'>
					<?php echo $props['desc'] ?>
				</div>
				<div class="row">
					<div class='ecu-select-wrapper col-md-6 col-sm-12'>
						<select class='ecu-mmy-filter-selector ecu-make' name='ecu-make' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
						    <option slug="" value=0>Select Make</option>
							<?php foreach($models as $category): ?>
								<?php if (!in_array($category->slug, $exclude)) : ?>
									<option slug="<?php echo $category->slug ?>" value="<?php echo $category->term_id ?>"><?php echo trim($category->name) ?></option>
								<?php endif;?>
							<?php endforeach; ?>
						</select>
					</div>				
					<div class='ecu-select-wrapper col-md-6 col-sm-12'>
						<select class='ecu-mmy-filter-selector ecu-model' placeholder='Select Model' name='ecu-model' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
							<option slug="" value='0'>Select Model</option>

						</select>
					</div>
				</div>
				<div class="row">
					<div class='col-md-6 col-sm-12 ecu-select-wrapper'>
						<select class='ecu-mmy-filter-selector ecu-engine' name='ecu-engine' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
							<option slug="" value='0'>Select Engine</option>
						</select>
					</div>
					<div class='col-md-6 col-sm-12 ecu-select-wrapper'>
						<select class='ecu-mmy-filter-selector ecu-year' name='ecu-year' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
							<option slug="" value='0'>Select Year</option>
						</select>
					</div>
				</div>
				<div style="margin: 20px 0px 0px;" class='row ecu-mmy-filter-apply'>
					<button parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper' class='ecu-filter-button btn btn-danger red'>SHOW REPAIR SERVICES</button>
				</div>
				<div class="row">
					<div style="margin-top:20px" class='col-md-12 col-sm-12 ecu-select-wrapper center'>
						<a class="ecu-repair-request" href="<?php echo site_url($props["link"]) ?>">Can't find your vehicle?</a>
					</div>
				</div>
			</div>
		  </div>
	  <?php
	}
	else{ 
// ob_start();
	?>
		<div id='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper' class="ecu-mmy-filter-wrapper ecu-mmy-filter-<?php echo $props['where']  ?>">
			<div class='ecu-wrapper'>
				<div class='ecu-mmy-filter-title'>
					<h3><?php echo $props['title'] ?></h3>
				</div>
				<div class='ecu-mmy-filter-desc'>
					<?php echo $props['desc'] ?>
				</div>
				<div class='ecu-select-wrapper'>
					<select class='ecu-mmy-filter-selector ecu-make' name='ecu-make' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
					    <option slug="" value=0>Select Make</option>
						<?php foreach($models as $category): ?>
							<?php if (!in_array($category->slug, $exclude)) : ?>
								<option slug="<?php echo $category->slug ?>" value="<?php echo $category->term_id ?>"><?php echo trim($category->name) ?></option>
							<?php endif;?>
						<?php endforeach; ?>
					</select>
				</div>				
				<div class='ecu-select-wrapper'>
					<select class='ecu-mmy-filter-selector ecu-model' placeholder='Select Model' name='ecu-model' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
						<option slug="" value='0'>Select Model</option>

					</select>
				</div>
				<div class='ecu-select-wrapper'>
					<select class='ecu-mmy-filter-selector ecu-engine' name='ecu-engine' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
						<option slug="" value='0'>Select Engine</option>
					</select>
				</div>
				<div class='ecu-select-wrapper'>
					<select class='ecu-mmy-filter-selector ecu-year' name='ecu-year' parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper'>
						<option slug="" value='0'>Select Year</option>
					</select>
				</div>
				<div class='ecu-mmy-filter-apply'>
					<button parent='ecu-mmy-filter-<?php echo $props["where"]?>-wrapper' class='ecu-filter-button btn btn-danger red'>SHOW REPAIR SERVICES</button>
				</div>
			</div>
		  </div>
	  <?php
	}
	
}
add_shortcode('ecu-mmy-filter', 'ecu_mmy_filter_shortcode');

?>