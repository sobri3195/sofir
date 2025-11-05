# SOFIR Mobile Support - Implementation Examples

This document provides practical, ready-to-use code examples for implementing SOFIR mobile features in your WordPress website.

## Table of Contents

1. [Basic Setup](#basic-setup)
2. [Restaurant Directory](#restaurant-directory)
3. [Business Directory](#business-directory)
4. [Job Board](#job-board)
5. [Real Estate Listings](#real-estate-listings)
6. [Event Directory](#event-directory)
7. [Advanced Customizations](#advanced-customizations)

---

## Basic Setup

### Register Mobile Menu in Theme

```php
// functions.php
function my_theme_setup() {
    register_nav_menus([
        'primary' => __('Desktop Menu', 'mytheme'),
        'mobile'  => __('Mobile Menu', 'mytheme'),
    ]);
}
add_action('after_setup_theme', 'my_theme_setup');
```

### Enable Mobile Support Programmatically

```php
// Auto-enable mobile support on theme activation
function my_theme_activate() {
    update_option('sofir_directory_mobile_settings', [
        'enabled' => true,
        'menu_id' => 0, // Use theme location
        'show_bottom_nav' => true,
        'breakpoint' => 768,
    ]);
}
add_action('after_switch_theme', 'my_theme_activate');
```

---

## Restaurant Directory

### Bottom Nav for Restaurant Listings

```php
// functions.php

// Add custom nav items for restaurant directory
add_action('sofir/mobile/bottom_nav_item', 'restaurant_mobile_nav', 10, 1);

function restaurant_mobile_nav($item) {
    switch ($item) {
        case 'restaurants':
            echo '<a href="/restaurants" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🍽️</span>';
            echo '<span class="sofir-nav-label">Restaurants</span>';
            echo '</a>';
            break;
            
        case 'map':
            echo '<a href="/restaurant-map" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🗺️</span>';
            echo '<span class="sofir-nav-label">Map</span>';
            echo '</a>';
            break;
            
        case 'reservations':
            if (is_user_logged_in()) {
                echo '<a href="/my-reservations" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">📅</span>';
                echo '<span class="sofir-nav-label">Bookings</span>';
                echo '</a>';
            }
            break;
            
        case 'favorites':
            if (is_user_logged_in()) {
                $count = count(get_user_meta(get_current_user_id(), 'favorite_restaurants', true) ?: []);
                echo '<a href="/favorites" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">❤️</span>';
                if ($count > 0) {
                    echo '<span class="sofir-nav-badge">' . esc_html($count) . '</span>';
                }
                echo '<span class="sofir-nav-label">Favorites</span>';
                echo '</a>';
            }
            break;
    }
}

// Custom CSS for restaurant theme
add_action('wp_head', 'restaurant_mobile_styles');

function restaurant_mobile_styles() {
    ?>
    <style>
    .sofir-bottom-navbar {
        background: #fff;
        border-top: 2px solid #e74c3c;
    }
    
    .sofir-bottom-nav-item.is-current {
        color: #e74c3c;
    }
    
    .sofir-nav-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #e74c3c;
        color: #fff;
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
        border-radius: 10px;
        font-weight: 600;
    }
    </style>
    <?php
}
```

### Usage in Template

```php
<?php
/**
 * Template Name: Restaurant Directory
 */
get_header();
?>

<div class="restaurant-directory">
    <h1>Find Restaurants</h1>
    
    <!-- Map -->
    <?php echo do_shortcode('[sofir_directory_map post_type="listing"]'); ?>
    
    <!-- Filters -->
    <?php echo do_shortcode('[sofir_directory_filters post_type="listing"]'); ?>
    
    <!-- Listings -->
    <?php echo do_shortcode('[sofir_post_feed post_type="listing" columns="2"]'); ?>
</div>

<!-- Bottom nav with custom items -->
<?php echo do_shortcode('[sofir_bottom_navbar items="restaurants,map,search,favorites,profile"]'); ?>

<?php get_footer(); ?>
```

---

## Business Directory

### Multi-Category Bottom Nav

```php
// functions.php

add_action('sofir/mobile/bottom_nav_item', 'business_mobile_nav', 10, 1);

function business_mobile_nav($item) {
    switch ($item) {
        case 'directory':
            echo '<a href="/business-directory" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🏢</span>';
            echo '<span class="sofir-nav-label">Directory</span>';
            echo '</a>';
            break;
            
        case 'categories':
            echo '<a href="/categories" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">📂</span>';
            echo '<span class="sofir-nav-label">Categories</span>';
            echo '</a>';
            break;
            
        case 'add-business':
            if (current_user_can('publish_posts')) {
                echo '<a href="/submit-business" class="sofir-bottom-nav-item sofir-nav-primary">';
                echo '<span class="sofir-nav-icon">➕</span>';
                echo '<span class="sofir-nav-label">Add Business</span>';
                echo '</a>';
            }
            break;
            
        case 'my-listings':
            if (is_user_logged_in()) {
                $user_posts = count_user_posts(get_current_user_id(), 'listing');
                echo '<a href="/my-listings" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">📋</span>';
                if ($user_posts > 0) {
                    echo '<span class="sofir-nav-badge">' . esc_html($user_posts) . '</span>';
                }
                echo '<span class="sofir-nav-label">My Listings</span>';
                echo '</a>';
            }
            break;
    }
}
```

### Category Browser Mobile Menu

```php
// Add category browser to mobile menu
add_action('wp_footer', 'business_mobile_menu_categories');

function business_mobile_menu_categories() {
    if (!wp_is_mobile()) {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        var categories = <?php echo json_encode(get_business_categories()); ?>;
        var categoryHtml = '<div class="sofir-mobile-categories">';
        categoryHtml += '<h4>Browse by Category</h4>';
        categoryHtml += '<ul>';
        
        categories.forEach(function(cat) {
            categoryHtml += '<li>';
            categoryHtml += '<a href="' + cat.url + '">';
            categoryHtml += cat.icon + ' ' + cat.name;
            categoryHtml += '<span class="count">(' + cat.count + ')</span>';
            categoryHtml += '</a>';
            categoryHtml += '</li>';
        });
        
        categoryHtml += '</ul>';
        categoryHtml += '</div>';
        
        $('.sofir-mobile-menu-content').append(categoryHtml);
    });
    </script>
    <?php
}

function get_business_categories() {
    $terms = get_terms([
        'taxonomy' => 'listing_category',
        'hide_empty' => true,
    ]);
    
    $categories = [];
    
    foreach ($terms as $term) {
        $categories[] = [
            'name' => $term->name,
            'url' => get_term_link($term),
            'count' => $term->count,
            'icon' => get_term_meta($term->term_id, 'icon', true) ?: '📁',
        ];
    }
    
    return $categories;
}
```

---

## Job Board

### Job Search Bottom Nav

```php
add_action('sofir/mobile/bottom_nav_item', 'job_board_mobile_nav', 10, 1);

function job_board_mobile_nav($item) {
    switch ($item) {
        case 'jobs':
            echo '<a href="/jobs" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">💼</span>';
            echo '<span class="sofir-nav-label">Jobs</span>';
            echo '</a>';
            break;
            
        case 'applications':
            if (is_user_logged_in()) {
                $applications = get_user_applications(get_current_user_id());
                echo '<a href="/my-applications" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">📄</span>';
                if (count($applications) > 0) {
                    echo '<span class="sofir-nav-badge">' . count($applications) . '</span>';
                }
                echo '<span class="sofir-nav-label">Applications</span>';
                echo '</a>';
            }
            break;
            
        case 'saved-jobs':
            if (is_user_logged_in()) {
                $saved = get_user_meta(get_current_user_id(), 'saved_jobs', true) ?: [];
                echo '<a href="/saved-jobs" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">⭐</span>';
                if (count($saved) > 0) {
                    echo '<span class="sofir-nav-badge">' . count($saved) . '</span>';
                }
                echo '<span class="sofir-nav-label">Saved</span>';
                echo '</a>';
            }
            break;
            
        case 'alerts':
            if (is_user_logged_in()) {
                $alerts = get_user_alerts(get_current_user_id());
                echo '<a href="/job-alerts" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">🔔</span>';
                if ($alerts['unread'] > 0) {
                    echo '<span class="sofir-nav-badge">' . $alerts['unread'] . '</span>';
                }
                echo '<span class="sofir-nav-label">Alerts</span>';
                echo '</a>';
            }
            break;
    }
}

function get_user_applications($user_id) {
    return get_posts([
        'post_type' => 'application',
        'author' => $user_id,
        'posts_per_page' => -1,
    ]);
}

function get_user_alerts($user_id) {
    return [
        'total' => 5,
        'unread' => 2,
    ];
}
```

---

## Real Estate Listings

### Property Search Bottom Nav

```php
add_action('sofir/mobile/bottom_nav_item', 'realestate_mobile_nav', 10, 1);

function realestate_mobile_nav($item) {
    switch ($item) {
        case 'properties':
            echo '<a href="/properties" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🏠</span>';
            echo '<span class="sofir-nav-label">Properties</span>';
            echo '</a>';
            break;
            
        case 'map-search':
            echo '<a href="/property-map" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🗺️</span>';
            echo '<span class="sofir-nav-label">Map</span>';
            echo '</a>';
            break;
            
        case 'filter':
            echo '<button type="button" class="sofir-bottom-nav-item" onclick="openFilterModal()">';
            echo '<span class="sofir-nav-icon">🔍</span>';
            echo '<span class="sofir-nav-label">Filter</span>';
            echo '</button>';
            break;
            
        case 'saved-searches':
            if (is_user_logged_in()) {
                $searches = count(get_user_meta(get_current_user_id(), 'saved_searches', true) ?: []);
                echo '<a href="/saved-searches" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">💾</span>';
                if ($searches > 0) {
                    echo '<span class="sofir-nav-badge">' . esc_html($searches) . '</span>';
                }
                echo '<span class="sofir-nav-label">Saved</span>';
                echo '</a>';
            }
            break;
            
        case 'favorites':
            if (is_user_logged_in()) {
                $favorites = count(get_user_meta(get_current_user_id(), 'favorite_properties', true) ?: []);
                echo '<a href="/favorites" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">❤️</span>';
                if ($favorites > 0) {
                    echo '<span class="sofir-nav-badge">' . esc_html($favorites) . '</span>';
                }
                echo '<span class="sofir-nav-label">Favorites</span>';
                echo '</a>';
            }
            break;
    }
}

// Add filter modal trigger
add_action('wp_footer', 'realestate_filter_modal_script');

function realestate_filter_modal_script() {
    ?>
    <script>
    function openFilterModal() {
        jQuery('#property-filter-modal').addClass('is-active');
    }
    </script>
    <?php
}
```

---

## Event Directory

### Event Calendar Bottom Nav

```php
add_action('sofir/mobile/bottom_nav_item', 'events_mobile_nav', 10, 1);

function events_mobile_nav($item) {
    switch ($item) {
        case 'events':
            echo '<a href="/events" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🎉</span>';
            echo '<span class="sofir-nav-label">Events</span>';
            echo '</a>';
            break;
            
        case 'calendar':
            echo '<a href="/calendar" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">📅</span>';
            echo '<span class="sofir-nav-label">Calendar</span>';
            echo '</a>';
            break;
            
        case 'my-tickets':
            if (is_user_logged_in()) {
                $tickets = count_user_tickets(get_current_user_id());
                echo '<a href="/my-tickets" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">🎫</span>';
                if ($tickets['upcoming'] > 0) {
                    echo '<span class="sofir-nav-badge">' . $tickets['upcoming'] . '</span>';
                }
                echo '<span class="sofir-nav-label">Tickets</span>';
                echo '</a>';
            }
            break;
            
        case 'notifications':
            if (is_user_logged_in()) {
                $notifications = get_user_notifications(get_current_user_id());
                echo '<a href="/notifications" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">🔔</span>';
                if ($notifications['unread'] > 0) {
                    echo '<span class="sofir-nav-badge">' . $notifications['unread'] . '</span>';
                }
                echo '<span class="sofir-nav-label">Alerts</span>';
                echo '</a>';
            }
            break;
    }
}

function count_user_tickets($user_id) {
    $tickets = get_posts([
        'post_type' => 'ticket',
        'author' => $user_id,
        'meta_query' => [
            [
                'key' => 'event_date',
                'value' => date('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ],
        ],
    ]);
    
    return [
        'total' => count($tickets),
        'upcoming' => count($tickets),
    ];
}

function get_user_notifications($user_id) {
    return [
        'total' => 10,
        'unread' => 3,
    ];
}
```

---

## Advanced Customizations

### Dynamic Bottom Nav Based on User Role

```php
add_action('wp_footer', 'dynamic_bottom_nav_by_role');

function dynamic_bottom_nav_by_role() {
    if (!is_user_logged_in()) {
        echo do_shortcode('[sofir_bottom_navbar items="home,search,profile"]');
        return;
    }
    
    $user = wp_get_current_user();
    
    if (in_array('administrator', $user->roles)) {
        echo do_shortcode('[sofir_bottom_navbar items="home,search,add,dashboard,profile"]');
    } elseif (in_array('business_owner', $user->roles)) {
        echo do_shortcode('[sofir_bottom_navbar items="home,my-listings,add-business,messages,profile"]');
    } elseif (in_array('customer', $user->roles)) {
        echo do_shortcode('[sofir_bottom_navbar items="home,search,favorites,messages,profile"]');
    } else {
        echo do_shortcode('[sofir_bottom_navbar items="home,search,add,messages,profile"]');
    }
}
```

### Animated Tab Indicator

```css
/* Add to your theme's CSS */
.sofir-bottom-navbar {
    position: relative;
}

.sofir-bottom-navbar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 20%;
    height: 3px;
    background: #0073aa;
    transition: left 0.3s ease;
}

.sofir-bottom-nav-item:nth-child(1).is-current ~ .sofir-bottom-navbar::before {
    left: 0%;
}

.sofir-bottom-nav-item:nth-child(2).is-current ~ .sofir-bottom-navbar::before {
    left: 20%;
}

.sofir-bottom-nav-item:nth-child(3).is-current ~ .sofir-bottom-navbar::before {
    left: 40%;
}

.sofir-bottom-nav-item:nth-child(4).is-current ~ .sofir-bottom-navbar::before {
    left: 60%;
}

.sofir-bottom-nav-item:nth-child(5).is-current ~ .sofir-bottom-navbar::before {
    left: 80%;
}
```

### Swipe Gesture Support

```javascript
// Add to your theme's JavaScript
(function($) {
    var startX = 0;
    var startY = 0;
    
    $(document).on('touchstart', function(e) {
        startX = e.touches[0].pageX;
        startY = e.touches[0].pageY;
    });
    
    $(document).on('touchend', function(e) {
        var endX = e.changedTouches[0].pageX;
        var endY = e.changedTouches[0].pageY;
        
        var diffX = endX - startX;
        var diffY = endY - startY;
        
        // Swipe from left to open menu
        if (diffX > 100 && Math.abs(diffY) < 50 && startX < 50) {
            $('#sofir-mobile-menu').addClass('is-active');
            $('body').addClass('sofir-mobile-menu-open');
        }
        
        // Swipe right to close menu
        if (diffX > 100 && $('#sofir-mobile-menu').hasClass('is-active')) {
            $('#sofir-mobile-menu').removeClass('is-active');
            $('body').removeClass('sofir-mobile-menu-open');
        }
    });
})(jQuery);
```

### Location-Based Bottom Nav

```php
// Show different nav items based on current page
add_action('wp_footer', 'location_based_bottom_nav');

function location_based_bottom_nav() {
    $items = 'home,search,profile'; // Default
    
    if (is_singular('listing')) {
        $items = 'back,map,favorite,share,contact';
    } elseif (is_post_type_archive('listing')) {
        $items = 'directory,map,search,filter,profile';
    } elseif (is_page('checkout')) {
        $items = 'cart,back,home';
    } elseif (is_user_logged_in() && is_author()) {
        $items = 'dashboard,my-listings,add,messages,profile';
    }
    
    echo do_shortcode('[sofir_bottom_navbar items="' . $items . '"]');
}

// Register custom nav items
add_action('sofir/mobile/bottom_nav_item', 'custom_location_nav_items', 10, 1);

function custom_location_nav_items($item) {
    switch ($item) {
        case 'back':
            echo '<a href="javascript:history.back()" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">⬅️</span>';
            echo '<span class="sofir-nav-label">Back</span>';
            echo '</a>';
            break;
            
        case 'share':
            echo '<button type="button" class="sofir-bottom-nav-item" onclick="shareCurrentPage()">';
            echo '<span class="sofir-nav-icon">📤</span>';
            echo '<span class="sofir-nav-label">Share</span>';
            echo '</button>';
            break;
            
        case 'contact':
            echo '<a href="tel:' . get_post_meta(get_the_ID(), 'sofir_contact_phone', true) . '" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">📞</span>';
            echo '<span class="sofir-nav-label">Call</span>';
            echo '</a>';
            break;
    }
}
```

### Progressive Web App (PWA) Integration

```php
// Add install prompt to mobile menu
add_action('wp_footer', 'pwa_install_prompt');

function pwa_install_prompt() {
    ?>
    <script>
    var deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        
        var installButton = jQuery('<div class="sofir-mobile-install">')
            .html('<button class="button button-primary">Install App</button>')
            .appendTo('.sofir-mobile-menu-content');
        
        installButton.on('click', 'button', function() {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                }
                deferredPrompt = null;
            });
        });
    });
    </script>
    <?php
}
```

---

## Testing Checklist

- [ ] Test on real iOS devices (Safari)
- [ ] Test on real Android devices (Chrome)
- [ ] Test menu toggle button visibility
- [ ] Test bottom nav auto-hide on scroll
- [ ] Test active state highlighting
- [ ] Test ESC key to close menu
- [ ] Test tap outside to close menu
- [ ] Test click on nav link closes menu
- [ ] Test responsive breakpoints
- [ ] Test landscape orientation
- [ ] Test with WordPress admin bar
- [ ] Test logged in vs logged out states
- [ ] Test notification badges
- [ ] Test custom nav items

---

**Need Help?**

Refer to the main documentation:
- [MOBILE_SUPPORT.md](./MOBILE_SUPPORT.md) - Full English documentation
- [DUKUNGAN_MOBILE.md](./DUKUNGAN_MOBILE.md) - Dokumentasi bahasa Indonesia

**Report Issues**: Open an issue in the SOFIR plugin repository
