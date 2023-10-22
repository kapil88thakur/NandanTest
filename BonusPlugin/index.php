<?php
/*

Plugin Name: Bonus Plugin

License:           GNU General Public License v2 or later

License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Text Domain:       bp

Author: Kapil

Version:           0.0.1

*/


/* enqueue scripts and styles */

function register_plugin_styles() {

wp_enqueue_style( 'plugin-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css' );

wp_enqueue_script( 'jslim-script','https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js' );

wp_enqueue_script( 'popper-script','https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js' );

wp_enqueue_script( 'bundle-script','https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js' );

wp_enqueue_style( 'plugin-css', plugins_url('assets/css/style.css',__FILE__) );

wp_enqueue_script( 'plugin-js', plugins_url('assets/js/script.js',__FILE__) );

}

add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );


/* This filter returns the navigations. It checks if profile_name navu=igation title is available then it will change it to loggedin user's first name.*/

function my_dynamic_menu_items( $menu_items ) {

  foreach ( $menu_items as $menu_item ) {

    if ('profile_name' == $menu_item->title ) {

      $user=wp_get_current_user();

      $name=$user->user_firstname; 

      $menu_item->title = $name;

    }

  }

  return $menu_items;

}

add_filter( 'wp_nav_menu_objects', 'my_dynamic_menu_items' );


/* This filter changes the avatar defauult image only for admin on comments, menu, posts, user lists.*/

function set_custom_default_avatar( $avatar, $id_or_email, $size, $default, $alt ) {

  $user = false;

  if ( is_numeric( $id_or_email ) ) {

    // User ID provided

    $user_id = (int) $id_or_email;

    $user = get_user_by( 'id', $user_id );

  } elseif ( is_object( $id_or_email ) ) {

    // User object provided

    if ( ! empty( $id_or_email->user_id ) ) {

      $user_id = (int) $id_or_email->user_id;

      $user = get_user_by( 'id', $user_id );

    }

  } else {

    // Email address provided

    $user = get_user_by( 'email', $id_or_email );

  }

  //$img_url=get_stylesheet_directory_uri().'/paresh.png';
  $img_url=plugins_url('assets/img/paresh.png',__FILE__) ;
  
  if ( $user ) {

    // Check user role and set default avatar accordingly

    if ( in_array( 'administrator', $user->roles ) ) {

      $default_avatar = $img_url;

    } else {

      $default_avatar = $default;

    } 

    $avatar = str_replace( $default, $default_avatar, $avatar );

  }

  return $avatar;

}

add_filter( 'get_avatar', 'set_custom_default_avatar', 10, 5 );


/*Show modal for Tip of the Day */

function show_tip_of_the_day(){

  $val= get_option( 'tip_of_the_day',false);

  ?>

  <div id="myModal" class="modal">

    <div class="modal-content">

      <span class="close">&times;</span>

      <p><?=$val; ?></p>

    </div>

  </div>

  <?php

}

add_action('wp_body_open','show_tip_of_the_day');


/*create admin menu*/

add_action('admin_menu','my_admin_menu');

function my_admin_menu() {

  add_menu_page('Bonus Plugin Setting', 'Bonus Plugin Setting', 'manage_options', '?page=plugin-options', 'myplguin_admin_page', 'dashicons-tickets', 2 );

}

function myplguin_admin_page(){

  $val= get_option( 'tip_of_the_day',false);

  ?>

  <div class="wrap">

  <div id="icon-themes" class="icon32"></div>  

  <h2>Bonus Plugin Settings</h2>  

  <?php settings_errors(); ?>  

  <form method="POST" action="">        

  <input type="text" name="tip" value="<?= $val;?>" placeholder="Enter tip of the day" />         

  <input type="submit" name="save_val" /> 

  </form> 

  </div>

  <?php

  if(isset($_POST['save_val'])){

    update_option( 'tip_of_the_day',$_POST['tip'] );

  }

}


/*Includes Template i page bakend and redering */ 

function my_template_array(){

  $temps=[];

  $temps[plugin_dir_path( __FILE__ ) . 'templates/template-custom.php']='Page Template From Plugin';

  return $temps;

}
add_filter( 'theme_page_templates', 'add_page_template_to_dropdown',10,3 );

function add_page_template_to_dropdown( $page_templates,$theme,$post ){

  $templates=my_template_array();

  foreach($templates as $key=>$val){

    $page_templates[$key]=$val;

  }

return $page_templates;

}

add_filter( 'template_include', 'change_page_template', 99 );

function change_page_template($template){

global $post;

$page_template_slug=get_page_template_slug($post->ID);

$templates=my_template_array();


if(isset($templates[$page_template_slug])){

$template=plugin_dir_path( __FILE__ ) . 'templates/template-custom.php';

}

return $template;
}
?>