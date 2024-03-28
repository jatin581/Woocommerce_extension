<?php
/*
Plugin Name: Newww
Description: Extension request an approval from the author.
Version: 1.0.0
Author: Shyftlabs
Author URI: http://yourdomain.com/
Developer: update
Developer URI: http://yourdomain.com/
Text Domain: New folder (2)
Domain Path: /languages
Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

function spiderman_plugin_add_menu() {
    add_menu_page(
        'Woocommerce Data',          // Page title
        'Spiderman',                   // Menu title
        'manage_options',            // Capability required to access the menu item
        'spiderman-plugin-settings', // Menu slug (unique identifier)
        'spiderman_plugin_page',     // Callback function to display the page content
        'dashicons-admin-plugins',   // Icon for the menu item
        99                           // Menu position
    );

    // Add a submenu under the top-level menu item
    add_submenu_page(
        'spiderman-plugin-settings', // Parent menu slug (unique identifier of the top-level menu item)
        'Authorization',             // Page title
        'Authorization',             // Menu title
        'manage_options',            // Capability required to access the menu item
        'spiderman-submenu-page',    // Menu slug (unique identifier)
        'spiderman_submenu_page'     // Callback function to display the page content
    );
}
add_action('admin_menu', 'spiderman_plugin_add_menu');

function spiderman_plugin_page() {
    echo '<h1>' . esc_html__('Spiderman Plugin', 'neww') . '</h1>';
    echo '<p>' . esc_html__('Welcome to the Spiderman Plugin! This is the main page.', 'neww') . '</p>';
}

function spiderman_submenu_page() {
    echo '<h1>' . esc_html__('Authorization Request', 'neww') . '</h1>';
    echo '<p>' . esc_html__('Please click on approve to authorize', 'neww') . '</p>';

    // Make API call
    $response = wp_remote_post(
        'https://dev-sm-api.illuminz.io/woo/auth',
        array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body'    => wp_json_encode(array(
                'appName'  => 'Spiderman Vivek 3',
                'storeUrl' => 'https://woo-supernaturally-fried-zorro.wpcomstaging.com',
            )),
        )
    );

    // Check if the API call was successful
    if (!is_wp_error($response)) {
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body, true); // Decode JSON response

        if ($response_data && isset($response_data['authUrl'])) {
            $auth_url = esc_url($response_data['authUrl']); // Escape the authUrl before using it

            // Output the auth URL in an iframe
            echo '<iframe src="' . esc_url($auth_url) . '" style="width: 100%; height: 450px;"></iframe>';
        } else {
            echo '<p>' . esc_html__('Invalid API Response: Missing authUrl', 'neww') . '</p>';
        }
    } else {
        echo '<p>' . esc_html__('API Error: ', 'neww') . esc_html($response->get_error_message()) . '</p>'; // Escape error message
    }

    // JavaScript to set target attribute of all anchor tags inside the iframe
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var iframe = document.querySelector("iframe");
            iframe.contentDocument.querySelectorAll("a").forEach(function(link) {
                link.setAttribute("target", "_self");
            });
        });
    </script>';
}
?>
