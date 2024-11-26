# Custom Login URL Plugin

## Description

The **Custom Login URL** plugin enhances the security of your WordPress site by replacing the default login URL (`wp-login.php`) with a custom, user-defined slug. This makes it harder for attackers to access the login page through the standard WordPress login URL. Unauthorized attempts to access `wp-admin` or `wp-login.php` are redirected to a 404 page.

## Features

- **Custom Login URL**: Set a custom login URL slug to replace the default `wp-login.php`.
- **Security Enhancement**: Redirects unauthorized access to the login page and admin panel to a 404 page.
- **Admin Settings Page**: Allows easy configuration of the custom login slug from the WordPress admin dashboard.
- **Logout Redirect**: Users who log out are redirected to the custom login page.

## Security and Best Practices

### 1. **Protecting `wp-login.php` and `wp-admin`**
   - **Custom Login Slug**: By defining a custom login URL (e.g., `/my-secret-login/`), the plugin prevents unauthorized users from accessing the default login page (`wp-login.php`) or the admin panel (`/wp-admin`).
   - **Redirect to 404 Page**: If an unauthorized user tries to access either `wp-login.php` or `wp-admin` without being logged in, they are redirected to a 404 error page, thus minimizing the risk of brute-force attacks.
   
### 2. **Configuration Options**
   - The plugin provides a settings page in the WordPress admin dashboard, allowing administrators to define a custom login slug. This allows users to choose a login URL that is difficult to guess or predict.
   - The custom slug can be configured through the `Custom Login URL` settings page under **Settings** → **Custom Login URL**.

### 3. **Logout Redirect**
   - After logging out, users are redirected to the custom login URL, ensuring they cannot easily access sensitive pages without logging back in.

### 4. **Use of WordPress Functions and Best Practices**
   - **Sanitization**: The custom login slug is sanitized using `sanitize_text_field` to ensure the input is safe and secure.
   - **Safe Redirects**: The plugin uses `wp_safe_redirect()` for secure redirects, ensuring that only valid URLs are used, preventing potential open-redirect vulnerabilities.
   - **Security through `ABSPATH`**: The plugin checks if the script is being accessed directly using `defined('ABSPATH')` to ensure that it cannot be executed outside of the WordPress environment.
   
### 5. **AJAX and POST Requests Handling**
   - The plugin ensures that `wp-login.php` is accessible through valid POST requests (for login form submission) and AJAX calls, preventing redirection during normal login attempts.
   
### 6. **Filter Login URL**
   - The plugin alters the default `login_url` filter to return the custom login URL, making it consistent throughout the site.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/custom-login-url/` directory, or install the plugin through the WordPress admin panel.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings** → **Custom Login URL** to configure the custom login slug.

## Usage

After activation, you can define your custom login slug by going to the settings page. For example:
- Set the login URL to `/my-secret-login/` instead of the default `/wp-login.php`.

Once set, accessing `wp-login.php` or `wp-admin` will redirect unauthorized users to a 404 page, enhancing the site's security.