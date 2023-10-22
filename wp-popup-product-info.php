<?php
/**
 * Plugin Name: WP Popup Product Info
 * Description: Adds a popup window to WooCommerce products with additional information.
 * Version: 1.0
 * Author: BuyReadySite.com
 * Author URI: https://buyreadysite.com
 * Tested up to: 5.9
 */

// Add meta box to enable/disable popup on WooCommerce product pages
function add_popup_meta_box() {
    add_meta_box('enable-popup-meta-box', 'Enable Popup', 'render_popup_meta_box', 'product', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_popup_meta_box');

// Render meta box content
function render_popup_meta_box($post) {
    $enable_popup = get_post_meta($post->ID, 'enable_popup', true) ?: 'yes';  // Default to 'yes'
    ?>
    <input type="radio" name="enable_popup" value="yes" <?php checked($enable_popup, 'yes'); ?>> Enable
    <input type="radio" name="enable_popup" value="no" <?php checked($enable_popup, 'no'); ?>> Disable
    <?php
}

// Save meta box content
function save_popup_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $enable_popup = $_POST['enable_popup'] ?? 'yes';  // Default to 'yes'
    update_post_meta($post_id, 'enable_popup', sanitize_text_field($enable_popup));
}
add_action('save_post', 'save_popup_meta_box');

// The rest of your code remains unchanged


// Add frontend styles and scripts
function add_popup_assets() {
    if (is_product()) {
        wp_enqueue_style('wp-popup-product-info', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
        wp_enqueue_script('wp-popup-product-info', plugin_dir_url(__FILE__) . 'assets/js/scripts.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'add_popup_assets');


// Add the popup on the frontend
function add_popup_html() {
    global $product;
	
	if (!$product || !($product instanceof WC_Product)) {
        return;
    }
	
	if ( !wp_is_mobile() ) { // Проверка, не мобильное ли это устройство
    $product_id = $product->get_id();
    $enable_popup = get_post_meta($product_id, 'enable_popup', true);

    if ('yes' === $enable_popup) {
        $custom_rating = get_post_meta($product_id, 'custom_rating', true);
        $percentage = ($custom_rating / 5) * 100;
        $reviews_number = get_post_meta($product_id, 'reviews_number', true);
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'medium'); // или 'large', или 'full'

        // Open .popup-container
        echo '<div class="popup-container" style="display: none;">';
        
        // Image
        echo '<div class="popup-image"><img src="' . esc_url($image[0]) . '" alt="' . esc_attr($product->get_name()) . '"></div>';
        
        // Product Info
        echo '<div class="popup-info">';
        echo '<h2>' . esc_html($product->get_name()) . '</h2>';
        
        // Custom Rating and Reviews
        echo '<div class="woocommerce-product-rating"><div class="star-rating star-rating--inline" role="img" aria-label="Rated ' . esc_attr($custom_rating) . ' out of 5">';
        echo '<span style="width:' . esc_attr($percentage) . '%">Rated <strong class="rating">' . esc_html($custom_rating) . '</strong> out of 5</span></div>';
        echo '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(Based on <strong class="rating">' . esc_html($reviews_number) . '</strong> customer ratings)</a></div>';
        echo '</div>';  // End of .popup-info

        // Product Price
        echo '<div class="wp-popup-product-price-wrapper">';  // New wrapper class
        echo '<div class="product-price-top" style="font-size: 25px; color: black; font-weight: 800;">';  // Existing class
        if ($product->is_on_sale()) {
            echo '<span class="total-price" style="text-decoration: line-through; color: #747474; margin-right: 10px;">' . strip_tags(wc_price($product->get_regular_price())) . '</span>';
        }
        echo '<span class="discounted-price">' . strip_tags(wc_price($product->get_price())) . '</span>';
        echo '</div>';
        echo '</div>';  // End of new wrapper class

        // Add to Cart Button
        echo '<button type="button" class="wp-popup-add-to-cart">Add to cart</button>';    
        
        echo '</div>';  // End of .popup-container
    }
	}
}
add_action('wp_footer', 'add_popup_html');


function add_mobile_popup_html() {
    global $product;
    if ($product && $product instanceof WC_Product && wp_is_mobile()) {
        $product_id = $product->get_id();
        $enable_popup = get_post_meta($product_id, 'enable_popup', true);

        if ('yes' === $enable_popup) {
            $custom_rating = get_post_meta($product_id, 'custom_rating', true);
            $percentage = ($custom_rating / 5) * 100;
            $reviews_number = get_post_meta($product_id, 'reviews_number', true);
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'medium'); // или 'large', или 'full'

            echo '<div class="mobile-popup-container" style="display: none;">';

            echo '<div class="mobile-popup-image"><img src="' . esc_url($image[0]) . '" alt="' . esc_attr($product->get_name()) . '"></div>';
            
            echo '<div class="mobile-popup-content">';
            
            echo '<div class="mobile-popup-info">';
            /* echo '<h2>' . esc_html($product->get_name()) . '</h2>'; */
			/* echo '<h2>' . esc_html(substr($product->get_name(), 0, 35)) . (strlen($product->get_name()) > 35 ? '...' : '') . '</h2>'; */
			echo '<h2 class="mobile-popup-title">' . esc_html($product->get_name()) . '</h2>';

            echo '<div class="woocommerce-product-rating"><div class="star-rating star-rating--inline" role="img" aria-label="Rated ' . esc_attr($custom_rating) . ' out of 5">';
            echo '<span style="width:' . esc_attr($percentage) . '%">Rated <strong class="rating">' . esc_html($custom_rating) . '</strong> out of 5</span></div>';
            echo '<a href="#reviews" class="woocommerce-review-link" rel="nofollow"><strong class="rating">' . esc_html($reviews_number) . '</strong> customer ratings</a></div>';
            echo '</div>';
            
			// Product Price and Add to Cart Button
			echo '<div class="mobile-popup-product-action">';  // New wrapper class for two columns
				// First Column for Price
				echo '<div class="mobile-popup-product-price">';  // Existing wrapper class
				echo '<div class="product-price-top" style="font-size: 14px;">';  // Reduce font size to 14px
				if ($product->is_on_sale()) {
					echo '<span class="total-price" style="text-decoration: line-through; color: #747474; margin-right: 10px;">' . strip_tags(wc_price($product->get_regular_price())) . '</span>';
				}
				echo '<span class="discounted-price">' . strip_tags(wc_price($product->get_price())) . '</span>';
				echo '</div>';
				echo '</div>';  // End of first column

				// Second Column for Add to Cart Button
				echo '<div class="mobile-popup-add-to-cart-wrapper">';  // New wrapper class for Add to Cart Button
				echo '<button type="button" class="mobile-popup-add-to-cart">Add to cart</button>';
				echo '</div>';  // End of second column
			echo '</div>';  // End of new wrapper class

						
            echo '</div>'; // End of .mobile-popup-content
            echo '</div>'; // End of .mobile-popup-container
        }
    }
}
add_action('wp_footer', 'add_mobile_popup_html'); 





/* // Add the popup on the frontend
function add_popup_html() {
    global $product;
    $product_id = $product->get_id();
    $enable_popup = get_post_meta($product_id, 'enable_popup', true);

    if ('yes' === $enable_popup) {
        $custom_rating = get_post_meta($product_id, 'custom_rating', true);
        $percentage = ($custom_rating / 5) * 100;
        $reviews_number = get_post_meta($product_id, 'reviews_number', true);
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'medium'); // или 'large', или 'full'

        // Open .popup-container
        echo '<div class="popup-container">';
        
        // Image
        echo '<div class="popup-image"><img src="' . esc_url($image[0]) . '" alt="' . esc_attr($product->get_name()) . '"></div>';
        
        // Product Info
        echo '<div class="popup-info">';
        echo '<h2>' . esc_html($product->get_name()) . '</h2>';
        
        // Custom Rating and Reviews
        echo '<div class="woocommerce-product-rating"><div class="star-rating star-rating--inline" role="img" aria-label="Rated ' . esc_attr($custom_rating) . ' out of 5">';
        echo '<span style="width:' . esc_attr($percentage) . '%">Rated <strong class="rating">' . esc_html($custom_rating) . '</strong> out of 5</span></div>';
        echo '<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(Based on <strong class="rating">' . esc_html($reviews_number) . '</strong> customer ratings)</a></div>';
        echo '</div>';  // End of .popup-info

        // Product Price
        echo '<div class="wp-popup-product-price-wrapper">';  // New wrapper class
        echo '<div class="wp-popup-product-price">';
        if ($product->is_on_sale()) {
            echo '<span class="wp-popup-total-price">' . strip_tags(wc_price($product->get_regular_price())) . '</span>';
        }
        echo '<span class="wp-popup-discounted-price">' . strip_tags(wc_price($product->get_price())) . '</span>';
        echo '</div>';
        echo '</div>';  // End of new wrapper class

        // Add to Cart Button
        echo '<button type="button" class="wp-popup-add-to-cart">Add to cart</button>';    
        
        echo '</div>';  // End of .popup-container
    }
}
add_action('wp_footer', 'add_popup_html');
 */
