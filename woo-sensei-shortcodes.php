<?php
/*
* Plugin Name: Woo Sensei Shortcodes
* Version: 1.3.0
* Plugin URI: https://www.wpexperts.io/
* Description: Sensei Shortcodes in Advanced Layout Builder
* Author: wpexperts.io
* Author URI: https://www.wpexperts.io/
*/

include 'shortcodes.php';

define("course_module_error", "<p><b>Not a course!</b> you must add attribute 'course_id' in shortde for displaying the modules in any other post type</p>");
define("lesson_button_error", "<p><b>Not a Lesson!</b> you must add attribute 'lessin_id' in shortde for displaying the Lesson button in any other post type</p>");
define("course_start_error", "<p><b>Not a course!</b> you must add attribute 'course_id' in shortde for displaying the modules in any other post type</p>");
define("lesson_video_error", "<p><b>Not a Lesson!</b> you must add attribute 'lesson_id' in shortde for displaying the lesson video in any other post type</p>");
define("course_video_error", "<p><b>Not a Course!</b> this shortcode only works on Course Page</p>");
define("lesson_contact_error", "<p><b>Not a Lesson!</b> you must add attribute 'lesson_id' in shortde for displaying the Contact Teacher Button in any other post type</p>");
define("lesson_file_attachment_error", "<p><b>Not a Lesson!</b> you must add attribute 'lesson_id' in shortde for displaying the Attachment Files of the lesson in any other post type</p>");
define("course_lesson_list_error", "<p><b>Not a Lesson!</b> you must add attribute 'lesson_id' in shortde for displaying the lesson video in any other post type</p>");


function wss_sensei_deactive_error() {

    if( !class_exists( 'Sensei_Main' ) ) {

        deactivate_plugins( plugin_basename( __FILE__ ) );

        $class = 'notice notice-error';
        $message = __( 'Error! Woothemes Sensei not Active or installed. Please installed woothemes sensei version 1.9.2 or greater <b> - Woo Sensei Shortcodes Plugin Deactivated </b>', 'wss' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }

}

add_action( 'admin_notices', 'wss_sensei_deactive_error', 20 );


function wss_scripts() {
    wp_enqueue_script( 'wss-script-sensei', plugin_dir_url( __FILE__ ) . 'script.js');
}
add_action( 'wp_enqueue_scripts', 'wss_scripts' );