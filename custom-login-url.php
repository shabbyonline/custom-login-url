<?php
/*
Plugin Name: Custom Login URL
Description: Replace the default WordPress login URL with a customizable slug, enhancing security by redirecting unauthorized access to wp-admin and wp-login.php to a 404 page.
Version: 1.5
Author: Abdul Rafay
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define the default custom login slug.
define('CUSTOM_LOGIN_SLUG', 'my-secret-login');

/**
 * Redirect unauthorized wp-admin and wp-login.php requests to 404.
 */
function redirect_admin_and_login()
{
    global $pagenow;

    // Handle logout redirect to custom login page.
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        wp_logout();
        wp_safe_redirect(home_url('/' . get_option('custom_login_slug', CUSTOM_LOGIN_SLUG) . '/'));
        exit;
    }

    // Redirect wp-login.php unless it's a valid POST or AJAX request.
    if ($pagenow === 'wp-login.php' && $_SERVER['REQUEST_METHOD'] !== 'POST' && !defined('DOING_AJAX')) {
        wp_safe_redirect(home_url('/404'));
        exit;
    }

    // Redirect wp-admin for non-logged-in users to 404.
    if (strpos($_SERVER['REQUEST_URI'], '/wp-admin') === 0 && !is_user_logged_in()) {
        wp_safe_redirect(home_url('/404'));
        exit;
    }
}
add_action('init', 'redirect_admin_and_login');

/**
 * Handle requests to the custom login slug.
 */
function handle_custom_login_slug()
{
    $custom_slug = get_option('custom_login_slug', CUSTOM_LOGIN_SLUG);

    // Check if the current request matches the custom slug.
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/' . $custom_slug) === 0) {
        global $error, $user_login; // Define globals to suppress warnings.
        $error = '';
        $user_login = '';

        // Mimic a wp-login.php request for WordPress processing.
        $_SERVER['SCRIPT_FILENAME'] = ABSPATH . 'wp-login.php';
        $_SERVER['SCRIPT_NAME'] = '/wp-login.php';
        $_SERVER['PHP_SELF'] = '/wp-login.php';

        require_once ABSPATH . 'wp-login.php';
        exit;
    }
}
add_action('init', 'handle_custom_login_slug');

/**
 * Change the login URL to the custom slug.
*/
function filter_custom_login_url()
{
    $custom_slug = get_option('custom_login_slug', CUSTOM_LOGIN_SLUG);
    return home_url('/' . $custom_slug . '/');
}
add_filter('login_url', 'filter_custom_login_url', 10, 3);

/**
 * Add settings page for configuring the custom login slug.
 */
function add_custom_login_settings_page()
{
    add_options_page(
        'Custom Login URL Settings',
        'Custom Login URL',
        'manage_options',
        'custom-login-url',
        'render_custom_login_settings_page'
    );
}
add_action('admin_menu', 'add_custom_login_settings_page');

/**
 * Register the custom login slug setting.
 */
function register_custom_login_setting()
{
    register_setting('custom_login_settings', 'custom_login_slug', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => CUSTOM_LOGIN_SLUG,
    ]);

    add_settings_section(
        'custom_login_section',
        'Login URL Settings',
        '',
        'custom-login-url'
    );

    add_settings_field(
        'custom_login_slug_field',
        'Custom Login Slug',
        'render_secret_login_slug_field',
        'custom-login-url',
        'custom_login_section'
    );
}
add_action('admin_init', 'register_custom_login_setting');

/**
 * Render the settings page.
 */
function render_custom_login_settings_page()
{
    ?>
    <div class="wrap">
        <h1>Custom Login URL Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_login_settings');
            do_settings_sections('custom-login-url');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Render the custom login slug field.
 */
function render_secret_login_slug_field()
{
    $slug = get_option('custom_login_slug', CUSTOM_LOGIN_SLUG);
    ?>
    <input type="text" name="custom_login_slug" value="<?php echo esc_attr($slug); ?>" />
    <p class="description">Enter a custom slug for the login page (e.g., "my-secret-login").</p>
    <?php
}