<?php
/**
 * Plugin Name: WP Countdown Timer
 * Description: A simple countdown timer that redirects when finished
 * Version: 1.0.0
 * Author: Jon Jackson
 * Text Domain: wp-countdown-timer
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_COUNTDOWN_TIMER_VERSION', '1.0.0');
define('WP_COUNTDOWN_TIMER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_COUNTDOWN_TIMER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Register activation hook
register_activation_hook(__FILE__, 'wp_countdown_timer_activate');

function wp_countdown_timer_activate() {
    add_option('wp_countdown_timer_target_date', '2025-05-16T00:00:00');
    add_option('wp_countdown_timer_redirect_url', home_url());
}

// Add admin menu
add_action('admin_menu', 'wp_countdown_timer_admin_menu');

function wp_countdown_timer_admin_menu() {
    add_menu_page(
        'Countdown Timer Settings',
        'Countdown Timer',
        'manage_options',
        'wp-countdown-timer',
        'wp_countdown_timer_admin_page',
        'dashicons-clock',
        30
    );
}

// Register settings
add_action('admin_init', 'wp_countdown_timer_register_settings');

function wp_countdown_timer_register_settings() {
    register_setting('wp_countdown_timer_settings', 'wp_countdown_timer_target_date');
    register_setting('wp_countdown_timer_settings', 'wp_countdown_timer_redirect_url');
}

// Admin page content
function wp_countdown_timer_admin_page() {
    ?>
    <div class="wrap">
        <h1>Countdown Timer Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wp_countdown_timer_settings'); ?>
            <?php do_settings_sections('wp_countdown_timer_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Target Date</th>
                    <td>
                        <input type="datetime-local"
                               name="wp_countdown_timer_target_date"
                               value="<?php echo esc_attr(get_option('wp_countdown_timer_target_date')); ?>"
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Redirect URL</th>
                    <td>
                        <input type="url"
                               name="wp_countdown_timer_redirect_url"
                               value="<?php echo esc_url(get_option('wp_countdown_timer_redirect_url')); ?>"
                               class="regular-text">
                        <p class="description">Where to redirect when the countdown ends</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'wp_countdown_timer_enqueue_scripts');

function wp_countdown_timer_enqueue_scripts() {
    global $post;
    if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'wp_countdown_timer')) {
        return;
    }

    wp_enqueue_script(
        'wp-countdown-timer',
        WP_COUNTDOWN_TIMER_PLUGIN_URL . 'dist/assets/main.js',
        array(),
        WP_COUNTDOWN_TIMER_VERSION,
        true
    );

    $css_file = WP_COUNTDOWN_TIMER_PLUGIN_DIR . 'dist/assets/index.css';
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'wp-countdown-timer',
            WP_COUNTDOWN_TIMER_PLUGIN_URL . 'dist/assets/index.css',
            array(),
            WP_COUNTDOWN_TIMER_VERSION
        );
    }

    $redirect_url = get_option('wp_countdown_timer_redirect_url', home_url());
    if (!preg_match('/^https?:\/\//i', $redirect_url)) {
        $redirect_url = home_url($redirect_url);
    }

    wp_localize_script('wp-countdown-timer', 'wpCountdownTimer', array(
        'targetDate' => get_option('wp_countdown_timer_target_date'),
        'redirectUrl' => esc_url(rtrim($redirect_url, '/'))
    ));
}

// Add shortcode
add_shortcode('wp_countdown_timer', 'wp_countdown_timer_shortcode');

function wp_countdown_timer_shortcode() {
    return '<div id="wp-countdown-timer-root" class="wp-countdown-timer"></div>';
}