# Template Usage Examples

Panduan lengkap dan contoh penggunaan template header dan footer SOFIR untuk berbagai skenario.

---

## 📚 Table of Contents

1. [Basic Usage](#basic-usage)
2. [FSE Integration](#fse-integration)
3. [Classic Theme Integration](#classic-theme-integration)
4. [Customization Examples](#customization-examples)
5. [Brand Integration](#brand-integration)
6. [Advanced Patterns](#advanced-patterns)

---

## Basic Usage

### Menggunakan Copy Pattern di Gutenberg

```bash
# Step 1: Akses Template Panel
WordPress Admin → SOFIR → Templates → Header Designs

# Step 2: Click Preview
Klik gambar preview untuk melihat tampilan template

# Step 3: Copy Pattern
Klik tombol "Copy Pattern"

# Step 4: Paste di Editor
Buka halaman/post → Paste (Ctrl+V)
```

### Programmatic Access (Developer)

```php
<?php
// Get template content
$template_manager = \Sofir\Templates\Manager::instance();
$header = $template_manager->get_template( 'modern-header' );
$content = $template_manager->get_template_content( $header );

// Insert ke post
wp_insert_post([
    'post_title' => 'My Page',
    'post_content' => $content,
    'post_type' => 'page',
    'post_status' => 'publish',
]);
```

---

## FSE Integration

### Creating Custom Template Part (Block Theme)

```php
<?php
/**
 * File: themes/my-theme/parts/header.html
 * Gunakan pattern SOFIR sebagai template part
 */

// 1. Copy pattern modern-header
// 2. Save sebagai template part di FSE
// 3. Assign ke template

// Or programmatic:
$manager = \Sofir\Templates\Manager::instance();
$template = $manager->get_template( 'modern-header' );
$manager->import_to_fse_template( $template, 'my-theme' );
```

### Dynamic Header Switcher

```php
<?php
/**
 * Switch header based on page context
 */
add_filter( 'sofir_header_template', function( $default_slug ) {
    if ( is_front_page() ) {
        return 'modern-header';
    } elseif ( is_page( 'about' ) ) {
        return 'minimal-header';
    } elseif ( is_post_type_archive( 'listing' ) ) {
        return 'business-header';
    }
    
    return $default_slug;
});
```

---

## Classic Theme Integration

### Replace Header with SOFIR Pattern

```php
<?php
/**
 * File: themes/my-theme/header.php
 * Replace dengan SOFIR header pattern
 */

get_header(); // Default WordPress header

// Insert SOFIR header
echo do_blocks( get_option( 'sofir_header_pattern' ) );

// Or load specific header
$manager = \Sofir\Templates\Manager::instance();
$header = $manager->get_template( 'modern-header' );
echo do_blocks( $manager->get_template_content( $header ) );
?>
```

### Footer Replacement

```php
<?php
/**
 * File: themes/my-theme/footer.php
 */

// SOFIR footer
$manager = \Sofir\Templates\Manager::instance();
$footer = $manager->get_template( 'multi-column-footer' );
echo do_blocks( $manager->get_template_content( $footer ) );

wp_footer();
?>
</body>
</html>
```

---

## Customization Examples

### 1. Change Header Colors

```php
<?php
// Original pattern
$content = '<!-- wp:group {"backgroundColor":"white","textColor":"black"} -->';

// Custom colors
$content = str_replace(
    '"backgroundColor":"white"',
    '"backgroundColor":"primary"',
    $content
);

$content = str_replace(
    '"textColor":"black"',
    '"textColor":"white"',
    $content
);
```

### 2. Add Custom Logo Size

```html
<!-- Original -->
<!-- wp:site-logo {"width":48} /-->

<!-- Custom - Larger logo -->
<!-- wp:site-logo {"width":80} /-->
```

### 3. Custom CTA Button

```html
<!-- Original Modern Header Button -->
<!-- wp:button {"style":{"border":{"radius":"50px"}}} -->
<div class="wp-block-button">
    <a class="wp-block-button__link wp-element-button" style="border-radius:50px">
        Get Started
    </a>
</div>
<!-- /wp:button -->

<!-- Custom - With URL and Custom Color -->
<!-- wp:button {"backgroundColor":"accent","style":{"border":{"radius":"50px"}}} -->
<div class="wp-block-button">
    <a href="https://example.com/signup" class="wp-block-button__link wp-element-button has-accent-background-color has-background" style="border-radius:50px">
        Start Free Trial
    </a>
</div>
<!-- /wp:button -->
```

### 4. Multi-language Footer

```php
<?php
/**
 * Dynamic footer berdasarkan language (WPML/Polylang)
 */
add_filter( 'sofir_footer_content', function( $content ) {
    $lang = apply_filters( 'wpml_current_language', null );
    
    if ( $lang === 'id' ) {
        $content = str_replace( 'About Us', 'Tentang Kami', $content );
        $content = str_replace( 'Contact', 'Kontak', $content );
    }
    
    return $content;
});
```

---

## Brand Integration

### Complete Brand Customization

```php
<?php
/**
 * Customize SOFIR header untuk brand colors
 */
function customize_sofir_header() {
    $manager = \Sofir\Templates\Manager::instance();
    $header = $manager->get_template( 'modern-header' );
    $content = $manager->get_template_content( $header );
    
    // Brand colors
    $brand_primary = '#FF6B35';
    $brand_secondary = '#004E89';
    
    // Replace colors
    $content = preg_replace(
        '/"backgroundColor":"[^"]*"/',
        '"backgroundColor":"custom"',
        $content
    );
    
    // Add custom CSS variables
    $custom_css = "
    <style>
    :root {
        --brand-primary: {$brand_primary};
        --brand-secondary: {$brand_secondary};
    }
    .has-custom-background-color {
        background-color: var(--brand-primary) !important;
    }
    </style>
    ";
    
    return $custom_css . do_blocks( $content );
}
```

### Dynamic Company Info in Footer

```php
<?php
/**
 * Replace placeholder dengan company info dari options
 */
add_filter( 'the_content', function( $content ) {
    if ( ! is_singular() && has_block( 'sofir/footer' ) ) {
        $company_name = get_option( 'company_name', 'Your Company' );
        $company_email = get_option( 'company_email', 'info@example.com' );
        $company_phone = get_option( 'company_phone', '+62 123 4567' );
        
        $content = str_replace( 'Your Company', $company_name, $content );
        $content = str_replace( 'info@example.com', $company_email, $content );
        $content = str_replace( '+62 123 4567', $company_phone, $content );
    }
    
    return $content;
});
```

---

## Advanced Patterns

### 1. A/B Testing Headers

```php
<?php
/**
 * Random header untuk A/B testing
 */
function get_ab_test_header() {
    $headers = [ 'modern-header', 'minimal-header' ];
    $user_id = get_current_user_id();
    
    // Consistent header per user
    $index = $user_id % count( $headers );
    $slug = $headers[ $index ];
    
    // Track impression
    do_action( 'ab_test_impression', 'header', $slug, $user_id );
    
    $manager = \Sofir\Templates\Manager::instance();
    $template = $manager->get_template( $slug );
    return $manager->get_template_content( $template );
}
```

### 2. Conditional Footer Widgets

```php
<?php
/**
 * Show/hide footer sections based on page
 */
add_filter( 'render_block', function( $block_content, $block ) {
    if ( $block['blockName'] === 'core/column' ) {
        // Hide newsletter di checkout page
        if ( is_page( 'checkout' ) && strpos( $block_content, 'newsletter' ) !== false ) {
            return '';
        }
    }
    
    return $block_content;
}, 10, 2 );
```

### 3. Dynamic Navigation Menu

```php
<?php
/**
 * Replace navigation block dengan custom menu per role
 */
add_filter( 'render_block', function( $block_content, $block ) {
    if ( $block['blockName'] === 'core/navigation' ) {
        if ( is_user_logged_in() ) {
            // Show user menu
            $block_content = do_blocks('<!-- wp:navigation {"menuId":2} /-->');
        } else {
            // Show guest menu
            $block_content = do_blocks('<!-- wp:navigation {"menuId":1} /-->');
        }
    }
    
    return $block_content;
}, 10, 2 );
```

### 4. Sticky Header Implementation

```php
<?php
/**
 * Add sticky behavior ke SOFIR header
 */
add_action( 'wp_footer', function() {
    ?>
    <script>
    (function() {
        var header = document.querySelector('.wp-block-group.alignfull:first-child');
        if (header) {
            header.style.position = 'sticky';
            header.style.top = '0';
            header.style.zIndex = '999';
            header.style.transition = 'box-shadow 0.3s ease';
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    header.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                } else {
                    header.style.boxShadow = 'none';
                }
            });
        }
    })();
    </script>
    <?php
});
```

### 5. Mobile-Specific Footer

```php
<?php
/**
 * Show different footer di mobile
 */
add_filter( 'sofir_footer_template', function( $default_slug ) {
    if ( wp_is_mobile() ) {
        return 'simple-footer'; // Simplified footer for mobile
    }
    
    return 'multi-column-footer'; // Full footer for desktop
});
```

---

## REST API Integration

### Get Templates via API

```javascript
// Fetch available templates
fetch('/wp-json/sofir/v1/templates')
    .then(response => response.json())
    .then(data => {
        const headers = data.filter(t => t.category === 'header');
        console.log('Available headers:', headers);
    });

// Get specific template content
fetch('/wp-json/sofir/v1/templates/modern-header')
    .then(response => response.json())
    .then(data => {
        console.log('Template content:', data.content);
    });
```

### Dynamic Template Switching

```javascript
// Change header via AJAX
jQuery(document).ready(function($) {
    $('#header-selector').on('change', function() {
        const slug = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'sofir_switch_header',
                slug: slug,
                nonce: SOFIR_DATA.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    });
});
```

---

## Performance Tips

### 1. Cache Template Output

```php
<?php
function get_cached_header() {
    $cache_key = 'sofir_header_modern';
    $cached = get_transient( $cache_key );
    
    if ( false === $cached ) {
        $manager = \Sofir\Templates\Manager::instance();
        $template = $manager->get_template( 'modern-header' );
        $cached = $manager->get_template_content( $template );
        
        set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
    }
    
    return do_blocks( $cached );
}
```

### 2. Lazy Load Footer

```php
<?php
add_action( 'wp_footer', function() {
    ?>
    <script>
    // Load footer via intersection observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Footer visible, load heavy content
                loadFooterWidgets();
            }
        });
    });
    
    observer.observe(document.querySelector('footer'));
    </script>
    <?php
});
```

---

## Security Considerations

### Sanitize Custom Content

```php
<?php
// Always sanitize when modifying template content
function safe_customize_header( $content, $custom_text ) {
    $safe_text = sanitize_text_field( $custom_text );
    return str_replace( 'Get Started', $safe_text, $content );
}
```

### Validate Before Import

```php
<?php
add_filter( 'sofir_before_import', function( $template ) {
    // Validate template structure
    if ( empty( $template['slug'] ) || empty( $template['path'] ) ) {
        wp_die( 'Invalid template structure' );
    }
    
    // Check file exists and is readable
    if ( ! file_exists( $template['path'] ) || ! is_readable( $template['path'] ) ) {
        wp_die( 'Template file not accessible' );
    }
    
    return $template;
});
```

---

## Troubleshooting

### Debug Template Loading

```php
<?php
add_action( 'wp_footer', function() {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $manager = \Sofir\Templates\Manager::instance();
        echo '<!-- SOFIR Templates Debug -->';
        echo '<!-- Available: ' . count( $manager->get_templates_flat() ) . ' -->';
    }
});
```

### Check Pattern Registration

```php
<?php
add_action( 'init', function() {
    $patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
    $sofir_patterns = array_filter( $patterns, function( $pattern ) {
        return strpos( $pattern['name'], 'sofir/' ) === 0;
    });
    
    error_log( 'SOFIR Patterns: ' . count( $sofir_patterns ) );
}, 999 );
```

---

## Further Reading

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Full Site Editing Documentation](https://developer.wordpress.org/block-editor/how-to-guides/themes/block-theme-overview/)
- [Block Pattern Directory](https://wordpress.org/patterns/)
- [SOFIR Main Documentation](../README.md)
- [Header Footer Templates Guide](./HEADER_FOOTER_TEMPLATES.md)

---

**Last Updated:** 2024  
**Plugin Version:** 1.0.0  
**Compatibility:** WordPress 6.3+
