<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_user_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Enroll user from a course', 'automatorwp-learndash-integration' ),
            'select_option'     => __( 'Enroll user from <strong>a course</strong>', 'automatorwp-learndash-integration' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Enroll user to %1$s', 'automatorwp-learndash-integration' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Enroll user to %1$s', 'automatorwp-learndash-integration' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp-learndash-integration' ),
                    'option_none_label' => __( 'all courses', 'automatorwp-learndash-integration' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp-learndash' ),
                    'post_type'         => 'sfwd-courses',
                ) ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $course_id = $action_options['post'];

        $courses = array();

        // Check specific course
        if( $course_id !== 'any' ) {

            $course = get_post( $course_id );

            // Bail if course doesn't exists
            if( ! $course ) {
                return;
            }

            $courses = array( $course_id );

        } else if( $course_id === 'any' ) {

            // Get all courses
            $query = new WP_Query( array(
                'post_type'		=> 'sfwd-courses',
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();

        }

        // Enroll user from courses
        foreach( $courses as $course_id ) {
            ld_update_course_access( $user_id, $course_id, false);
        }

    }

}

new AutomatorWP_LearnDash_User_Course();