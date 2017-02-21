<?php

// Sensei Lesson Video
function wss_sensei_lesson_video( $atts ) {
    global $post;

    ob_start();
    $atts = shortcode_atts( array(
        'lesson_id' => get_the_ID()
    ), $atts );

    if ( 'lesson' != get_post_type() && $atts['lesson_id'] == get_the_ID() ) {
        return lesson_video_error;
    }

    $lesson_id = $atts['lesson_id'];
    $sensei_lesson_video = new Sensei_Frontend();
    $sensei_lesson_video->sensei_lesson_video($lesson_id);
    $sensei_lesson_video_html = ob_get_clean();

    return $sensei_lesson_video_html;
} // End sensei_lesson_video()


add_shortcode('wss_sensei_lesson_video', 'wss_sensei_lesson_video');

// Sensei Course Video
function wss_sensei_course_video($atts){
    ob_start();

    if ( 'course' != get_post_type()) {
        return course_video_error;
    }

    $sensei_video = new Sensei_Course();
    $sensei_video->the_course_video();
    $sensei_video_html = ob_get_clean();
    return $sensei_video_html;
}

add_shortcode('wss_sensei_course_video', 'wss_sensei_course_video');

// Sensei Contact Teacher Button
function wss_sensei_message_button($atts) {
    ob_start();



    $atts = shortcode_atts( array(
        'lesson_id' => get_the_ID(),
        'user_id' => get_current_user_id()
    ), $atts );


    if ( 'lesson' != get_post_type() && $atts['lesson_id'] == get_the_ID()) {
        return lesson_contact_error;
    }

    $lesson_id = $atts['lesson_id'];
    $user_id = $atts['user_id'];
    $sensei_message = new Sensei_Messages();
    $sensei_message->send_message_link($lesson_id,$user_id);
    $message = ob_get_clean();
    return $message;
}

add_shortcode('wss_sensei_message_button','wss_sensei_message_button');

// Sensei Breadcrumb
function wss_sensei_breadcrumb( $id = 0 ) {
    // Only output on lesson, quiz and taxonomy (module) pages
    if( ! ( is_tax( 'module' ) || is_singular( 'lesson' ) || is_singular( 'quiz' ) ) ) return;
    if( empty( $id )  ){
        $id = get_the_ID();
    }
    $sensei_breadcrumb_prefix = __( 'Back to: ', 'woothemes-sensei' );
    $separator = apply_filters( 'sensei_breadcrumb_separator', '&gt;' );
    $html = '<section class="sensei-breadcrumb">' . $sensei_breadcrumb_prefix;
    // Lesson
    if ( is_singular( 'lesson' ) && 0 < intval( $id ) ) {
        $course_id = intval( get_post_meta( $id, '_lesson_course', true ) );
        if( ! $course_id ) {
            return;
        }
        $html .= '<a href="' . esc_url( get_permalink( $course_id ) ) . '" title="' . __( 'Back to the course', 'woothemes-sensei' ) . '">' . get_the_title( $course_id ) . '</a>';
    } // End If Statement
    // Quiz
    if ( is_singular( 'quiz' ) && 0 < intval( $id ) ) {
        $lesson_id = intval( get_post_meta( $id, '_quiz_lesson', true ) );
        if( ! $lesson_id ) {
            return;
        }
        $html .= '<a href="' . esc_url( get_permalink( $lesson_id ) ) . '" title="' .  __( 'Back to the lesson', 'woothemes-sensei' ) . '">' . get_the_title( $lesson_id ) . '</a>';
    } // End If Statement
    // Allow other plugins to filter html
    $html = apply_filters ( 'sensei_breadcrumb_output', $html, $separator );
    $html .= '</section>';
    return $html;
} // End sensei_breadcrumb()

add_shortcode('wss_sensei_breadcrumb', 'wss_sensei_breadcrumb');

// Sensei File Attachments
function wss_display_attached_media($atts) {
    global $post;


    $atts = shortcode_atts( array(
        'lesson_id' => $post->ID
    ), $atts );

    if ( 'lesson' != get_post_type() && $atts['lesson_id'] == get_the_ID() ) {
        return lesson_file_attachment_error;
    }

    $lesson_id = $atts['lesson_id'];

    $media = get_post_meta( $lesson_id, '_attached_media', true );

    $html = '';

    $post_type = ucfirst( get_post_type( $post ) );

    if( $media && is_array( $media ) && count( $media ) > 0 ) {
        $html .= '<div id="attached-media">';
        $html .= '<h2>' . sprintf( __( '%s Media', 'sensei_media_attachments' ), $post_type ) . '</h2>';
        $html .= '<ul>';
        foreach( $media as $k => $file ) {
            $file_parts = explode( '/', $file );
            $file_name = array_pop( $file_parts );
            $html .= '<li id="attached_media_' . $k . '"><a href="' . esc_url( $file ) . '" target="_blank">' . esc_html( $file_name ) . '</a></li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
    }

    return $html;
}

add_shortcode('wss_display_attached_media', 'wss_display_attached_media');

// Sensei Lesson Next Prev Buttons
function wss_next_prev_lesson() {
    global $post;
    $nav_id_array = sensei_get_prev_next_lessons( $post->ID );
    $previous_lesson_id = absint( $nav_id_array['prev_lesson'] );
    $next_lesson_id = absint( $nav_id_array['next_lesson'] );
    // Output HTML
    $nav_html = '';
    if ( ( 0 < $previous_lesson_id ) || ( 0 < $next_lesson_id ) ) {
        $prev_id = esc_url( get_permalink( $previous_lesson_id ) );
        $next_id = esc_url( get_permalink( $next_lesson_id ) );
        $prev_title = get_the_title( $previous_lesson_id );
        $next_title = get_the_title( $next_lesson_id );
        $nav_html .= '<nav id="post-entries" class="post-entries fix">';
        if ( 0 < $previous_lesson_id ) {
            $nav_html .= '<div class="nav-prev fl"><a href="'.$prev_id.'" rel="prev"><span class="meta-nav"></span> '.$prev_title.'</a></div>';
        }
        if ( 0 < $next_lesson_id ) {
            $nav_html .= '<div class="nav-next fr"><a href="'.$next_id.'" rel="prev"><span class="meta-nav"></span> '.$next_title.'</a></div>';
        }
        $nav_html .= '</nav>';
    }
    return $nav_html;
}

add_shortcode('wss_next_prev_lesson', 'wss_next_prev_lesson');

// Sensei Lesson Button
function lesson_button($atts) {
    global $woothemes_sensei;


    $atts = shortcode_atts( array(
        'lesson_id' => get_the_ID(),
        'user_id' => get_current_user_id()
    ), $atts );

    if ( 'lesson' != get_post_type() && $atts['lesson_id'] == get_the_ID() ) {
        return lesson_file_attachment_error;
    }

    $post_id = $atts['lesson_id'];
    $user_id = $atts['user_id'];

    $lesson_btn_html = '';
    // Get the prerequisite lesson
    $lesson_prerequisite = get_post_meta( $post_id, '_lesson_prerequisite', true );
    $lesson_course_id = get_post_meta( $post_id, '_lesson_course', true );

    // Lesson Quiz Meta
    $quiz_id = $woothemes_sensei->lesson->lesson_quizzes( $post_id );
    //var_dump($quiz_id);

    $lesson_quizzes = $woothemes_sensei->frontend->lesson->lesson_quizzes( $post_id );
    if(is_array($lesson_quizzes)) {
        foreach ($lesson_quizzes as $quiz_item) {
            $quiz_id = $quiz_item->ID ;
        }
    }

    $has_user_completed_lesson = WooThemes_Sensei_Utils::user_completed_lesson( $post_id, $user_id );
    $show_actions = true;

    if( intval( $lesson_prerequisite ) > 0 ) {
        // Lesson Quiz Meta
        $prereq_lesson_quizzes = $woothemes_sensei->frontend->lesson->lesson_quizzes( $lesson_prerequisite );
        foreach ($prereq_lesson_quizzes as $quiz_item) {
            $prerequisite_quiz_id = $quiz_item->ID ;
        }

        // Get quiz pass setting
        $prereq_pass_required = get_post_meta( $prerequisite_quiz_id, '_pass_required', true );

        if( $prereq_pass_required ) {
            $quiz_grade = intval( WooThemes_Sensei_Utils::sensei_get_activity_value( array( 'post_id' => $prerequisite_quiz_id, 'user_id' => $user_id, 'type' => 'sensei_quiz_grade', 'field' => 'comment_content' ) ) );
            $quiz_passmark = intval( get_post_meta( $prerequisite_quiz_id, '_quiz_passmark', true ) );
            if( $quiz_grade < $quiz_passmark ) {
                $show_actions = false;
            }
        } else {
            $user_lesson_end = WooThemes_Sensei_Utils::sensei_get_activity_value( array( 'post_id' => $lesson_prerequisite, 'user_id' => $user_id, 'type' => 'sensei_lesson_start', 'field' => 'comment_content' ) );
            if ( ! $user_lesson_end || $user_lesson_end == '' || strlen( $user_lesson_end ) == 0 ) {
                $show_actions = false;
            }
        }
    }

    if ( 0 < count($lesson_quizzes) && is_user_logged_in() && sensei_has_user_started_course( $lesson_course_id, $user_id ) ) { ?>
        <?php $no_quiz_count = 0; ?>
        <?php
        $quiz_questions = $woothemes_sensei->frontend->lesson->lesson_quiz_questions( $quiz_id );
        // Display lesson quiz status message
        if ( $has_user_completed_lesson || 0 < count( $quiz_questions ) ) {
            $status = WooThemes_Sensei_Utils::sensei_user_quiz_status_message( $post_id, $user_id, true );
            $lesson_btn_html .= '<div class="sensei-message ' . $status['box_class'] . '">' . $status['message'] . '</div>';
            if( 0 < count( $quiz_questions ) ) {
                $lesson_btn_html .= $status['extra'];
            } // End If Statement
        } // End If Statement
        ?>
    <?php } elseif( $show_actions && 0 < count($lesson_quizzes) && $woothemes_sensei->access_settings() ) { ?>
        <?php
        $quiz_questions = $woothemes_sensei->frontend->lesson->lesson_quiz_questions( $quiz_id );
        if( 0 < count( $quiz_questions ) ) {
            $quiz_id = esc_url( get_permalink( $quiz_id ) );
            $text = esc_attr( apply_filters( 'sensei_view_lesson_quiz_text', __( 'View the Lesson Quiz', 'woothemes-sensei' ) ) );
            $less_text = apply_filters( 'sensei_view_lesson_quiz_text', __( 'View the Lesson Quiz', 'woothemes-sensei' ) );
            $lesson_btn_html .= '<p><a class="button" href="'.$quiz_id.'" title="'.$text.'">'.$less_text.'</a></p>';
        } ?>
    <?php } // End If Statement
    if ( $show_actions && ! $has_user_completed_lesson ) {
        $lesson_btn_html .= wss_complete_lesson_button();
    } elseif( $show_actions ) {
        sensei_reset_lesson_button();
    } // End If Statement

    return $lesson_btn_html;
}

add_shortcode('wss_sensei_lesson_button', 'lesson_button');

function wss_complete_lesson_button() {
    global  $post;

    $lesson_id =  $post->ID;
    $user_id = get_current_user_id();

    $quiz_id = 0;
    //make sure user is taking course
    $course_id = Sensei()->lesson->get_course_id( $lesson_id );
    if( ! Sensei_Utils::user_started_course( $course_id, $user_id ) ){
        return;
    }
    $lesn_btn_form = '';
    $c_noonce = esc_attr( wp_create_nonce( 'woothemes_sensei_complete_lesson_noonce' ) );
    $btn = __( 'Complete Lesson', 'woothemes-sensei' );
    // Lesson quizzes
    $quiz_id = Sensei()->lesson->lesson_quizzes( $post->ID );
    $pass_required = true;
    $action_link = esc_url( get_permalink() );
    if( $quiz_id ) {
        // Get quiz pass setting
        $pass_required = get_post_meta( $quiz_id, '_pass_required', true );
    }
    if( ! $quiz_id || ( $quiz_id && ! $pass_required ) ) {

        $lesn_btn_form .= '<form class="lesson_button_form" method="POST" action="'.$action_link.'">';
        $lesn_btn_form .= '<input type="hidden"
                   name="woothemes_sensei_complete_lesson_noonce"
                   id="woothemes_sensei_complete_lesson_noonce"
                   value="'.$c_noonce.'"
            />';

        $lesn_btn_form .= '<input type="hidden" name="quiz_action" value="lesson-complete" />';

        $lesn_btn_form .= '<input type="submit"
                   name="quiz_complete"
                   class="quiz-submit complete"
                   value="'.$btn.'"/>';

        $lesn_btn_form .= '</form>';

    } // End If Statement
    return $lesn_btn_form;
} // End sensei_complete_lesson_button()

// Sensei Course Start
function start_course($atts) {

    global $post, $current_user;

    $atts = shortcode_atts( array(
        'course_id' => $post->ID,
        'user_id' => $current_user->ID
    ), $atts );

    if ( 'course' != get_post_type() && $atts['course_id'] == get_the_ID() ) {
        return course_start_error;
    }

    $course_id = $atts['course_id'];
    $user_id = $atts['user_id'];

    $link = esc_url( get_permalink() );
    $noonce = esc_attr( 'woothemes_sensei_start_course_noonce' );
    $c_noonce = esc_attr( wp_create_nonce( 'woothemes_sensei_start_course_noonce' ) );
    $text = __( 'Start taking this Course', 'woothemes-sensei' );

    $sensei_start = new Sensei_Frontend();


    // Check if the user is taking the course
    $is_user_taking_course = Sensei_Utils::user_started_course( $course_id, $user_id );
    // Handle user starting the course
    if ( isset( $_POST['course_start'] )
        && wp_verify_nonce( $_POST[ 'woothemes_sensei_start_course_noonce' ], 'woothemes_sensei_start_course_noonce' )
        && !$is_user_taking_course ) {
        // Start the course
        $activity_logged = Sensei_Utils::user_start_course( $user_id, $course_id );
        $sensei_start->data = new stdClass();
        $sensei_start->data->is_user_taking_course = false;
        if ( $activity_logged ) {
            $sensei_start->data->is_user_taking_course = true;
            // Refresh page to avoid re-posting
            ?>

            <script type="text/javascript"> window.location = '<?php echo get_permalink( $course_id ); ?>'; </script>

        <?php
        } // End If Statement
    } // End If Statement

    $prerequisite_complete = sensei_check_prerequisite_course( $course_id );
    //echo die($is_user_taking_course);
    if ( $prerequisite_complete && empty($is_user_taking_course) ) {
        $strt_btn = '<form method="POST" action="'.$link.'">';

        $strt_btn .= '<input type="hidden" name="'.$noonce.'" id="'.$noonce.'" value="'.$c_noonce.'" />';

        $strt_btn .= '<span><input name="course_start" type="submit" class="course-start" value="'.$text.'"/></span>';

        $strt_btn .= '</form>';
    } // End If Statement

    return $strt_btn;
}

function the_progress_statement( $course_id = 0, $user_id = 0 ){
    if( empty( $course_id ) ){
        global $post;
        $course_id = $post->ID;
    }
    $progress = new Sensei_Course();
    if( empty( $user_id ) ){
        $user_id = get_current_user_id();
    }
    return '<span class="progress statement  course-completion-rate">' . $progress->get_progress_statement( $course_id, $user_id  ) . '</span>';
}

add_shortcode('wss_sensei_course_start', 'start_course');

function course_lesson_list($atts) {

    global $post, $wp_query;

    $atts = shortcode_atts( array(
        'course_id' => $post->ID
    ), $atts );

    if ( 'course' != get_post_type() && $atts['course_id'] == get_the_ID() ) {
        return course_lesson_list_error;
    }

    $course_id = $atts['course_id'];

    $html ='';

    $args = array(
        'post_type' => 'lesson',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_lesson_course',
                'value' => intval( $course_id ),
                'compare' => '='
            )
        ),

        'orderby' => 'menu_order',
        'order' => 'ASC',
        'suppress_filters' => 0
    );

    $lessons = get_posts( $args );

    if( count( $lessons ) > 0 ) {

        $html .= '<section class="module-lessons">';

        $html .= '<header><h3>' . __( 'Lessons', 'sensei_modules' ) . '</h3></header>';

        $html .= '<ul>';

        foreach( $lessons as $lesson ) {
            $status = '';
            $lesson_completed = WooThemes_Sensei_Utils::user_completed_lesson( $lesson->ID, get_current_user_ID() );
            $title = esc_attr( get_the_title( intval( $lesson->ID ) ) );

            if( $lesson_completed ) {
                $status = 'completed';
            }

            $html .= '<li class="' . $status . '"><a href="' . esc_url( get_permalink( intval( $lesson->ID ) ) ) . '" title="' . esc_attr( get_the_title( intval( $lesson->ID ) ) ) . '">' . apply_filters( 'sensei_module_lesson_list_title', $title, $lesson->ID ) . '</a></li>';

            // Build array of displayed lesson for exclusion later
            $displayed_lessons[] = $lesson->ID;
        }

        $html .= '</ul>';

        $html .= '</section>';

    }

    return $html;
}

add_shortcode('wss_sensei_course_lesson_list', 'course_lesson_list');

function wss_progressbar() {
    global $post, $woothemes_sensei;
    $wss_progressbar  = new Sensei_Course();

    $per_page = 20;
    if ( isset( Sensei()->settings->settings[ 'my_course_amount' ] )
        && ( 0 < absint( Sensei()->settings->settings[ 'my_course_amount' ] ) ) ) {
        $per_page = absint( Sensei()->settings->settings[ 'my_course_amount' ] );
    }


    $course_statuses = Sensei_Utils::sensei_check_for_activity( array( 'user_id' => get_current_user_ID(), 'type' => 'sensei_course_status' ), true );
    // User may only be on 1 Course
    if ( !is_array($course_statuses) ) {
        $course_statuses = array( $course_statuses );
    }
    $completed_ids = $active_ids = array();
    foreach( $course_statuses as $course_status ) {
        if ( Sensei_Utils::user_completed_course( $course_status, get_current_user_ID() ) ) {
            $completed_ids[] = $course_status->comment_post_ID;
        } else {
            $active_ids[] = $course_status->comment_post_ID;
        }
    }

    $active_courses = array();
    if ( 0 < intval( count( $active_ids ) ) ) {
        $my_courses_section = 'active';
        $active_courses = Sensei()->course->course_query( $per_page, 'usercourses', $active_ids );
        $active_count = count( $active_ids );
    } // End If Statement

    $lesson_count = Sensei()->course->course_lesson_count( absint( get_the_ID() ) );

    $course_lessons =  Sensei()->course->course_lessons( get_the_ID() );
    $lessons_completed = 0;
    foreach ( $course_lessons as $lesson ) {
        if ( Sensei_Utils::user_completed_lesson( $lesson->ID, get_current_user_ID() ) ) {
            ++$lessons_completed;
        }
    }

    $progress_percentage = abs( round( ( doubleval( $lessons_completed ) * 100 ) / ( $lesson_count ), 0 ) );

    // Handle Division by Zero
    if ( 0 == $lesson_count ) {
        $lesson_count = 1;
    } // End If Statement

    return  $wss_progressbar->get_progress_meter($progress_percentage);
}

add_shortcode('wss_progressbar','wss_progressbar');

function wss_course_nav() {

    $nav_course = '<div><nav id="post-entries" class="post-entries fix">';
    $nav_course .= '<div class="nav-prev fl mani" style="postition:relative">'. get_previous_post_link( ).'</div>';
    $nav_course .= '<div class="nav-next fr">'.get_next_post_link().'</div>';
    $nav_course .= '</nav></div>';

    return $nav_course;
}

add_shortcode('wss_course_nav','wss_course_nav');

function wss_modules_course($atts) {
    global $post, $current_user, $woothemes_sensei;



    $atts = shortcode_atts( array(
        'course_id' => $post->ID,
        'user_id' => $current_user->ID
    ), $atts );

    if ( 'course' != get_post_type() && $atts['course_id'] == get_the_ID()) {
        return course_module_error;
    }

    $course_id = $atts['course_id'];
    $user_id = $atts['user_id'];

    $html = '';

    if( has_term( '', 'module', $course_id ) ) {

        do_action( 'sensei_modules_page_before' );

        // Get user data
        get_currentuserinfo();

        // Check if user is taking this course
        $course_status_id = WooThemes_Sensei_Utils::user_started_course( intval( $course_id ), intval( $user_id ) );
        $is_user_taking_course = !empty( $course_status_id ) ? true : false;

        // Get all modules
        $modules = wss_get_course_modules( intval( $course_id ) );

        $displayed_lessons = array();

        $lessons_completed = 0;

        // Start building HTML
        $html .= '<section class="course-lessons">';

        // Display course progress for users who are taking the course
        if ( is_user_logged_in() && $is_user_taking_course ) {

            $course_lessons = $woothemes_sensei->frontend->course->course_lessons( intval( $course_id ) );
            $total_lessons = count( $course_lessons );

            $html .= '<span class="course-completion-rate">' . sprintf( __( 'Currently completed %1$s lesson(s) of %2$s in total', 'sensei_modules' ), '######', $total_lessons ) . '</span>';
            $html .= '<div class="meter+++++"><span style="width: @@@@@%">@@@@@%</span></div>';

        }

        $html .= '<header><h2>' . __( 'Modules', 'sensei_modules' ) . '</h2></header>';

        // Display each module
        foreach( $modules as $module ) {

            $module_url = esc_url( add_query_arg( 'course_id', $course_id, get_term_link( $module, 'module' ) ) );

            $html .= '<article class="post module">';

            $html .= '<header><h2><a href="' . esc_url( $module_url ) . '">' . $module->name . '</a></h2></header>';

            $html .= '<section class="entry">';

            $module_progress = false;
            if( is_user_logged_in() ) {
                global $current_user;
                wp_get_current_user();
                $module_progress = wss_get_user_module_progress( $module->term_id, $course_id, $current_user->ID );
            }

            if( $module_progress && $module_progress > 0 ) {
                $status = __( 'Completed', 'sensei_modules' );
                $class = 'completed';
                if( $module_progress < 100 ) {
                    $status = __( 'In progress', 'sensei_modules' );
                    $class = 'in-progress';
                }
                $html .= '<p class="status module-status ' . esc_attr( $class ) . '">' . $status . '</p>';
            }

            $description = $module->description;

            if( '' != $description ) {
                $html .= '<p class="module-description">' . $description . '</p>';
            }

            $args = array(
                'post_type' => 'lesson',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_lesson_course',
                        'value' => intval( $course_id ),
                        'compare' => '='
                    )
                ),
                'tax_query' => array(
                    array(
                        'taxonomy' => 'module',
                        'field' => 'id',
                        'terms' => intval( $module->term_id )
                    )
                ),
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'suppress_filters' => 0
            );

            if( version_compare( $woothemes_sensei->version, '1.6.0', '>=' ) ) {
                $args['meta_key'] = '_order_module_' . intval( $module->term_id );
                $args['orderby'] = 'meta_value_num date';
            }

            $lessons = get_posts( $args );

            if( count( $lessons ) > 0 ) {

                $html .= '<section class="module-lessons">';

                $html .= '<header><h3>' . __( 'Lessons', 'sensei_modules' ) . '</h3></header>';

                $html .= '<ul>';

                foreach( $lessons as $lesson ) {
                    $status = '';
                    $lesson_completed = WooThemes_Sensei_Utils::user_completed_lesson( $lesson->ID, $current_user->ID );
                    $title = esc_attr( get_the_title( intval( $lesson->ID ) ) );

                    if( $lesson_completed ) {
                        $status = 'completed';
                    }

                    $html .= '<li class="' . $status . '"><a href="' . esc_url( get_permalink( intval( $lesson->ID ) ) ) . '" title="' . esc_attr( get_the_title( intval( $lesson->ID ) ) ) . '">' . apply_filters( 'sensei_module_lesson_list_title', $title, $lesson->ID ) . '</a></li>';

                    // Build array of displayed lesson for exclusion later
                    $displayed_lessons[] = $lesson->ID;
                }

                $html .= '</ul>';

                $html .= '</section>';

            }

            $html .= '</section>';

            $html .= '</article>';

        }

        // Display any lessons that have not already been displayed
        $args = array(
            'post_type' => 'lesson',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_lesson_course',
                    'value' => intval( $course_id ),
                    'compare' => '='
                )
            ),
            'post__not_in' => $displayed_lessons,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'suppress_filters' => 0
        );

        if( version_compare( $woothemes_sensei->version, '1.6.0', '>=' ) ) {
            $args['meta_key'] = '_order_' . intval( $course_id );
            $args['orderby'] = 'meta_value_num date';
        }

        $lessons = get_posts( $args );

        if( 0 < count( $lessons ) ) {

            $html .= '<section class="course-lessons">';

            $html .= '<header><h2>' . __( 'Other Lessons', 'sensei_modules' ) . '</h2></header>';

            $lesson_count = 1;

            foreach( $lessons as $lesson ) {

                // Note if current lesson has been completed
                $single_lesson_complete = false;
                $user_lesson_status = WooThemes_Sensei_Utils::user_lesson_status( intval( $lesson->ID ), intval( $current_user->ID ) );
                if ( WooThemes_Sensei_Utils::user_completed_lesson( $user_lesson_status ) ) {
                    $single_lesson_complete = true;
                }

                // Get Lesson data
                $complexity_array = $woothemes_sensei->frontend->lesson->lesson_complexities();
                $lesson_length = get_post_meta( $lesson->ID, '_lesson_length', true );
                $lesson_complexity = get_post_meta( $lesson->ID, '_lesson_complexity', true );
                if ( '' != $lesson_complexity ) { $lesson_complexity = $complexity_array[$lesson_complexity]; }
                $user_info = get_userdata( absint( $lesson->post_author ) );
                if ( '' != $lesson->post_excerpt ) { $lesson_excerpt = $lesson->post_excerpt; } else { $lesson_excerpt = $lesson->post_content; }
                $title = esc_html( sprintf( __( '%s', 'woothemes-sensei' ), $lesson->post_title ) );

                // Display lesson data
                $html .= '<article class="' . esc_attr( join( ' ', get_post_class( array( 'course', 'post' ), $lesson->ID ) ) ) . '">';

                $html .= '<header>';

                $html .= '<h2><a href="' . esc_url( get_permalink( $lesson->ID ) ) . '" title="' . esc_attr( sprintf( __( 'Start %s', 'woothemes-sensei' ), $lesson->post_title ) ) . '">' . apply_filters( 'sensei_module_lesson_list_title', $title, $lesson->ID ) . '</a></h2>';

                $html .= '<p class="lesson-meta">';

                if ( '' != $lesson_length ) { $html .= '<span class="lesson-length">' . apply_filters( 'sensei_length_text', __( 'Length: ', 'woothemes-sensei' ) ) . $lesson_length . __( ' minutes', 'woothemes-sensei' ) . '</span>'; }
                if ( isset( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) && ( $woothemes_sensei->settings->settings[ 'lesson_author' ] ) ) {
                    $html .= '<span class="lesson-author">' . apply_filters( 'sensei_author_text', __( 'Author: ', 'woothemes-sensei' ) ) . '<a href="' . get_author_posts_url( absint( $lesson->post_author ) ) . '" title="' . esc_attr( $user_info->display_name ) . '">' . esc_html( $user_info->display_name ) . '</a></span>';
                }
                if ( '' != $lesson_complexity ) { $html .= '<span class="lesson-complexity">' . apply_filters( 'sensei_complexity_text', __( 'Complexity: ', 'woothemes-sensei' ) ) . $lesson_complexity .'</span>'; }
                if ( $single_lesson_complete ) {
                    $html .= '<span class="lesson-status complete">' . apply_filters( 'sensei_complete_text', __( 'Complete', 'woothemes-sensei' ) ) .'</span>';
                } elseif ( $user_lesson_status ) {
                    $html .= '<span class="lesson-status in-progress">' . apply_filters( 'sensei_in_progress_text', __( 'In Progress', 'woothemes-sensei' ) ) .'</span>';
                } // End If Statement

                $html .= '</p>';

                $html .= '</header>';

                $html .=  $woothemes_sensei->post_types->lesson->lesson_image( $lesson->ID );

                $html .= '<section class="entry">';

                $html .= '<p class="lesson-excerpt">';

                //$html .= '<span>' . $lesson_excerpt . '</span>';

                $html .= '</p>';

                $html .= '</section>';

                $html .= '</article>';

                $lesson_count++;
            }

            $html .= '</section>';
        }

        $html .= '</section>';

        // Replace place holders in course progress widget
        if ( is_user_logged_in() && $is_user_taking_course ) {

            $lessons_completed = get_comment_meta( $course_status_id, 'complete', true );

            // Add dynamic data to the output
            $html = str_replace( '######', $lessons_completed, $html );
            $progress_percentage = get_comment_meta( $course_status_id, 'percent', true );

            $html = str_replace( '@@@@@', $progress_percentage, $html );
            if ( 50 < $progress_percentage ) { $class = ' green'; } elseif ( 25 <= $progress_percentage && 50 >= $progress_percentage ) { $class = ' orange'; } else { $class = ' red'; }

            $html = str_replace( '+++++', $class, $html );
        }
    }

    // Display output
    return $html;

    if( has_term( '', 'module', $course_id ) ) {
        do_action( 'sensei_modules_page_after' );
    }
}

add_shortcode('wss_modules','wss_modules_course');

function wss_get_course_modules( $course_id = 0 ) {

    $course_id = intval( $course_id );
    if( 0 < $course_id ) {

        // Get modules for course
        $modules = wp_get_post_terms( $course_id, 'module' );

        // Get custom module order for course
        $order = wss_get_course_module_order( $course_id );

        // Sort by custom order if custom order exists
        if( $order ) {
            $ordered_modules = array();
            $unordered_modules = array();
            foreach( $modules as $module ) {
                $order_key = array_search( $module->term_id, $order );
                if( $order_key !== false ) {
                    $ordered_modules[ $order_key ] = $module;
                } else {
                    $unordered_modules[] = $module;
                }
            }

            // Order modules correctly
            ksort( $ordered_modules );

            // Append modules that have not yet been ordered
            if( count( $unordered_modules ) > 0 ) {
                $ordered_modules = array_merge( $ordered_modules, $unordered_modules );
            }

        } else {
            $ordered_modules = $modules;
        }

        return $ordered_modules;
    }
    return false;
}

function wss_get_user_module_progress( $module_id = 0, $course_id = 0, $user_id = 0 ) {
    $module_progress = get_user_meta( intval( $user_id ), '_module_progress_' . intval( $course_id ) . '_' . intval( $module_id ), true );
    if( $module_progress ) {
        return (float) $module_progress;
    }
    return false;
}

function wss_get_course_module_order( $course_id = 0 ) {
    if( $course_id ) {
        $order = get_post_meta( intval( $course_id ), '_module_order', true );
        return $order;
    }
    return false;
}