<?php
/*
Plugin Name: Wocommerce PLugin
Description: Extension request an approval from the author.
Version: 1.0.0
Author: Shyftlabs
Author URI: http://yourdomain.com/
Developer: Your Name
Developer URI: http://yourdomain.com/
Text Domain: my-extension
Domain Path: /languages
Woo: 12345:342928dfsfhsf8429842374wdf4234sfd
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
function spiderman_plugin_add_menu() {
    add_menu_page(
        'Woocommerce Data',          // Page title
        'Spiderman',          // Menu title
        'manage_options',            // Capability required to access the menu item
        'spiderman-plugin-settings', // Menu slug (unique identifier)
        'spiderman_plugin_page',     // Callback function to display the page content
        'dashicons-admin-plugins',   // Icon for the menu item
        99                           // Menu position
    );

    // Add a submenu under the top-level menu item
    add_submenu_page(
        'spiderman-plugin-settings', // Parent menu slug (unique identifier of the top-level menu item)
        'Authorization',              // Page title
        'Authorization',              // Menu title
        'manage_options',            // Capability required to access the menu item
        'spiderman-submenu-page',    // Menu slug (unique identifier)
        'spiderman_submenu_page'     // Callback function to display the page content
    );
}
add_action('admin_menu', 'spiderman_plugin_add_menu');

function spiderman_plugin_page() {
    echo '<h1>Spiderman Plugin</h1>';
    echo '<p>Welcome to the Spiderman Plugin! This is the main page.</p>';
}

function spiderman_submenu_page() {
    echo '<h1>Authorization Request</h1>';
    echo '<p>Please click on approve to authorize</p>';

    // Make API call
    $response = wp_remote_post(
        'https://dev-sm-api.illuminz.io/woo/auth',
        array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body'    => json_encode(array(
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
            $auth_url = $response_data['authUrl']; // Get the authUrl from the response data

            // Output the auth URL in an iframe
            echo '<iframe src="' . esc_url($auth_url) . '" style="width: 100%; height: 400px;"></iframe>';
        } else {
            echo '<p>Invalid API Response: Missing authUrl</p>';
        }
    } else {
        echo '<p>API Error: ' . $response->get_error_message() . '</p>';
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
