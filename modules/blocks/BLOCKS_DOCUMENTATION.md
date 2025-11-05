# SOFIR Gutenberg Blocks Documentation

Complete documentation for all 40 SOFIR Gutenberg blocks.

## Table of Contents

1. [Core Blocks](#core-blocks) (28 blocks)
2. [Extended Blocks](#extended-blocks) (12 blocks)
3. [Block Attributes Reference](#block-attributes-reference)
4. [Usage Examples](#usage-examples)
5. [Styling and Customization](#styling-and-customization)

---

## Core Blocks

### 1. Action Block (`sofir/action`)

Creates customizable call-to-action buttons.

**Attributes:**
- `actionType` (string): Button type - default: 'button'
- `actionLabel` (string): Button text - default: 'Click Me'
- `actionUrl` (string): Target URL - default: ''
- `actionClass` (string): Custom CSS class - default: ''

**Usage:**
```html
<!-- wp:sofir/action {"actionLabel":"Get Started","actionUrl":"/signup","actionClass":"primary-button"} /-->
```

**Frontend Output:**
```html
<div class="sofir-action-block">
    <a href="/signup" class="sofir-action-button primary-button">Get Started</a>
</div>
```

---

### 2. Cart Summary Block (`sofir/cart-summary`)

Displays shopping cart summary with items and total.

**Attributes:** None (dynamic content)

**Usage:**
```html
<!-- wp:sofir/cart-summary /-->
```

**Frontend Output:**
```html
<div class="sofir-cart-summary">
    <h3>Cart Summary</h3>
    <div class="sofir-cart-items" id="sofir-cart-items"></div>
    <div class="sofir-cart-total"><strong>Total:</strong> <span id="sofir-cart-total-amount">0</span></div>
</div>
```

---

### 3. Countdown Block (`sofir/countdown`)

Countdown timer to a specific date and time.

**Attributes:**
- `targetDate` (string): Target date in ISO format - default: ''
- `format` (string): Display format (dhms, hms, ms) - default: 'dhms'

**Usage:**
```html
<!-- wp:sofir/countdown {"targetDate":"2024-12-31T23:59:59","format":"dhms"} /-->
```

---

### 4. Create Post Block (`sofir/create-post`)

Frontend form to create posts (requires user login).

**Attributes:**
- `postType` (string): Post type to create - default: 'post'
- `buttonLabel` (string): Submit button text - default: 'Create Post'

**Usage:**
```html
<!-- wp:sofir/create-post {"postType":"article","buttonLabel":"Submit Article"} /-->
```

---

### 5. Dashboard Block (`sofir/dashboard`)

User dashboard with stats and recent activity.

**Attributes:**
- `title` (string): Dashboard title - default: 'Dashboard'
- `showStats` (boolean): Show user statistics - default: true
- `showRecent` (boolean): Show recent posts - default: true

**Usage:**
```html
<!-- wp:sofir/dashboard {"title":"My Dashboard","showStats":true,"showRecent":true} /-->
```

---

### 6. Gallery Block (`sofir/gallery`)

Image gallery with responsive grid layout.

**Attributes:**
- `imageIds` (array): Array of attachment IDs - default: []
- `columns` (number): Number of columns - default: 3

**Usage:**
```html
<!-- wp:sofir/gallery {"imageIds":[123,456,789],"columns":4} /-->
```

---

### 7. Login & Register Block (`sofir/login-register`)

Combined login and registration form.

**Attributes:**
- `showRegister` (boolean): Show registration tab - default: true
- `redirectUrl` (string): Redirect after login - default: ''

**Usage:**
```html
<!-- wp:sofir/login-register {"showRegister":true,"redirectUrl":"/dashboard"} /-->
```

**Features:**
- Tab switcher between login and register
- Phone number support for registration
- Customizable redirect URL

---

### 8. Map Block (`sofir/map`)

Interactive map with location markers.

**Attributes:**
- `postType` (string): Post type for locations - default: 'listing'
- `zoom` (number): Map zoom level - default: 12
- `height` (string): Map height - default: '400px'

**Usage:**
```html
<!-- wp:sofir/map {"postType":"listing","zoom":14,"height":"500px"} /-->
```

---

### 9. Messages Block (`sofir/messages`)

User messaging system (requires login).

**Attributes:** None (dynamic content)

**Usage:**
```html
<!-- wp:sofir/messages /-->
```

---

### 10. Navbar Block (`sofir/navbar`)

Responsive navigation menu.

**Attributes:**
- `menuId` (number): WordPress menu ID - default: 0
- `mobileBreakpoint` (number): Mobile breakpoint in pixels - default: 768

**Usage:**
```html
<!-- wp:sofir/navbar {"menuId":5,"mobileBreakpoint":768} /-->
```

---

### 11. Order Block (`sofir/order`)

Display order details (requires login).

**Attributes:**
- `orderId` (number): Order ID to display - default: 0

**Usage:**
```html
<!-- wp:sofir/order {"orderId":12345} /-->
```

---

### 12. Popup Kit Block (`sofir/popup-kit`)

Customizable popup modal with trigger button.

**Attributes:**
- `triggerText` (string): Button text - default: 'Open Popup'
- `popupTitle` (string): Popup title - default: ''
- `popupContent` (string): Popup content HTML - default: ''

**Usage:**
```html
<!-- wp:sofir/popup-kit {"triggerText":"View Details","popupTitle":"Product Details","popupContent":"<p>Product information here</p>"} /-->
```

---

### 13. Post Feed Block (`sofir/post-feed`)

Display posts in grid or list layout.

**Attributes:**
- `postType` (string): Post type - default: 'post'
- `postsPerPage` (number): Number of posts - default: 10
- `layout` (string): Layout style (grid, list) - default: 'grid'

**Usage:**
```html
<!-- wp:sofir/post-feed {"postType":"article","postsPerPage":6,"layout":"grid"} /-->
```

---

### 14. Print Template Block (`sofir/print-template`)

Add print button to page.

**Attributes:**
- `templateId` (number): Template ID - default: 0

**Usage:**
```html
<!-- wp:sofir/print-template /-->
```

---

### 15. Product Form Block (`sofir/product-form`)

Frontend product submission form.

**Attributes:** None

**Usage:**
```html
<!-- wp:sofir/product-form /-->
```

---

### 16. Product Price Block (`sofir/product-price`)

Display product price with currency.

**Attributes:**
- `productId` (number): Product post ID - default: 0
- `showCurrency` (boolean): Show currency symbol - default: true

**Usage:**
```html
<!-- wp:sofir/product-price {"productId":456,"showCurrency":true} /-->
```

---

### 17. Quick Search Block (`sofir/quick-search`)

AJAX-powered instant search.

**Attributes:**
- `postType` (string): Post type to search - default: 'post'
- `placeholder` (string): Search placeholder - default: 'Search...'

**Usage:**
```html
<!-- wp:sofir/quick-search {"postType":"listing","placeholder":"Search listings..."} /-->
```

---

### 18. Review Stats Block (`sofir/review-stats`)

Display average rating and review count.

**Attributes:**
- `postId` (number): Post ID - default: 0 (current post)

**Usage:**
```html
<!-- wp:sofir/review-stats {"postId":789} /-->
```

---

### 19. Ring Chart Block (`sofir/ring-chart`)

Doughnut chart visualization.

**Attributes:**
- `data` (array): Chart data array - default: []
- `title` (string): Chart title - default: ''

**Usage:**
```html
<!-- wp:sofir/ring-chart {"data":[{"label":"Sales","value":45},{"label":"Revenue","value":55}],"title":"Performance"} /-->
```

---

### 20. Sales Chart Block (`sofir/sales-chart`)

Line chart for sales data.

**Attributes:**
- `period` (string): Time period (week, month, year) - default: 'month'
- `title` (string): Chart title - default: 'Sales Chart'

**Usage:**
```html
<!-- wp:sofir/sales-chart {"period":"month","title":"Monthly Sales"} /-->
```

---

### 21. Search Form Block (`sofir/search-form`)

Advanced search form with filters.

**Attributes:**
- `postType` (string): Post type to search - default: 'post'
- `advancedFilters` (boolean): Show taxonomy filters - default: false

**Usage:**
```html
<!-- wp:sofir/search-form {"postType":"listing","advancedFilters":true} /-->
```

---

### 22. Slider Block (`sofir/slider`)

Image slider with autoplay.

**Attributes:**
- `slides` (array): Array of slide objects - default: []
- `autoplay` (boolean): Enable autoplay - default: true
- `interval` (number): Autoplay interval in ms - default: 5000

**Usage:**
```html
<!-- wp:sofir/slider {"slides":[{"image":123,"caption":"Slide 1"},{"image":456,"caption":"Slide 2"}],"autoplay":true,"interval":5000} /-->
```

---

### 23. Term Feed Block (`sofir/term-feed`)

Display taxonomy terms.

**Attributes:**
- `taxonomy` (string): Taxonomy name - default: 'category'
- `limit` (number): Number of terms - default: 10

**Usage:**
```html
<!-- wp:sofir/term-feed {"taxonomy":"category","limit":15} /-->
```

---

### 24. Timeline Block (`sofir/timeline`)

Vertical or horizontal timeline.

**Attributes:**
- `items` (array): Timeline items - default: []
- `orientation` (string): Layout (vertical, horizontal) - default: 'vertical'

**Usage:**
```html
<!-- wp:sofir/timeline {"items":[{"date":"2024","title":"Founded","content":"Company started"}],"orientation":"vertical"} /-->
```

---

### 25. Timeline Style Kit Block (`sofir/timeline-style-kit`)

Timeline styling presets.

**Attributes:**
- `stylePreset` (string): Style preset - default: 'modern'
- `colorScheme` (string): Color scheme - default: 'blue'

**Usage:**
```html
<!-- wp:sofir/timeline-style-kit {"stylePreset":"modern","colorScheme":"blue"} /-->
```

---

### 26. User Bar Block (`sofir/user-bar`)

User profile bar with login/logout.

**Attributes:** None (dynamic content)

**Usage:**
```html
<!-- wp:sofir/user-bar /-->
```

---

### 27. Visit Chart Block (`sofir/visit-chart`)

Bar chart for visitor statistics.

**Attributes:**
- `period` (string): Time period - default: 'week'
- `title` (string): Chart title - default: 'Visitor Statistics'

**Usage:**
```html
<!-- wp:sofir/visit-chart {"period":"month","title":"Monthly Visitors"} /-->
```

---

### 28. Work Hours Block (`sofir/work-hours`)

Display business hours with open/closed status.

**Attributes:**
- `postId` (number): Post ID - default: 0 (current post)
- `showStatus` (boolean): Show open/closed status - default: true

**Usage:**
```html
<!-- wp:sofir/work-hours {"postId":123,"showStatus":true} /-->
```

**Data Structure:**
Reads from `sofir_work_hours` meta field:
```php
[
    'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
    'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
    // ...
]
```

---

## Extended Blocks

### 29. Testimonial Slider Block (`sofir/testimonial-slider`)

Carousel of testimonials with ratings.

**Attributes:**
- `autoplay` (boolean): Enable autoplay - default: true
- `interval` (number): Autoplay interval - default: 5000
- `showRating` (boolean): Show star ratings - default: true
- `postType` (string): Post type - default: 'testimonial'
- `numberOfItems` (number): Number of items - default: 6

**Usage:**
```html
<!-- wp:sofir/testimonial-slider {"autoplay":true,"interval":6000,"showRating":true,"numberOfItems":10} /-->
```

**Custom Fields:**
- `sofir_rating` - Star rating (1-5)
- `sofir_author` - Author name
- `sofir_position` - Author position/title

---

### 30. Pricing Table Block (`sofir/pricing-table`)

Pricing plans comparison table.

**Attributes:**
- `columns` (number): Number of columns - default: 3
- `postType` (string): Post type - default: 'pricing'
- `showFeatures` (boolean): Show feature list - default: true
- `highlightBest` (boolean): Highlight featured plan - default: true

**Usage:**
```html
<!-- wp:sofir/pricing-table {"columns":3,"showFeatures":true,"highlightBest":true} /-->
```

**Custom Fields:**
- `sofir_price` - Price amount
- `sofir_period` - Billing period (month, year)
- `sofir_features` - Array of features
- `sofir_button_text` - CTA button text
- `sofir_button_url` - CTA button URL
- `sofir_featured` - Featured plan flag

---

### 31. Team Grid Block (`sofir/team-grid`)

Team members grid with photos and social links.

**Attributes:**
- `columns` (number): Grid columns - default: 3
- `postType` (string): Post type - default: 'team_member'
- `numberOfItems` (number): Number of members - default: 6
- `showSocial` (boolean): Show social links - default: true

**Usage:**
```html
<!-- wp:sofir/team-grid {"columns":4,"numberOfItems":8,"showSocial":true} /-->
```

**Custom Fields:**
- `sofir_position` - Job title
- `sofir_twitter` - Twitter URL
- `sofir_linkedin` - LinkedIn URL
- `sofir_email` - Email address

---

### 32. FAQ Accordion Block (`sofir/faq-accordion`)

Collapsible FAQ accordion.

**Attributes:**
- `postType` (string): Post type - default: 'faq'
- `numberOfItems` (number): Number of FAQs - default: 10
- `expandFirst` (boolean): Expand first item - default: true

**Usage:**
```html
<!-- wp:sofir/faq-accordion {"numberOfItems":15,"expandFirst":true} /-->
```

**Post Structure:**
- Title = Question
- Content = Answer

---

### 33. CTA Banner Block (`sofir/cta-banner`)

Call-to-action banner with gradient background.

**Attributes:**
- `title` (string): Banner title - default: 'Ready to Get Started?'
- `description` (string): Description text - default: ''
- `buttonText` (string): Button text - default: 'Get Started'
- `buttonUrl` (string): Button URL - default: '#'
- `backgroundColor` (string): Background color - default: '#0073aa'
- `textColor` (string): Text color - default: '#ffffff'
- `alignment` (string): Text alignment - default: 'center'

**Usage:**
```html
<!-- wp:sofir/cta-banner {"title":"Join Us Today","description":"Start your journey","buttonText":"Sign Up","buttonUrl":"/register","backgroundColor":"#667eea","alignment":"center"} /-->
```

---

### 34. Feature Box Block (`sofir/feature-box`)

Feature highlight with icon and description.

**Attributes:**
- `icon` (string): Icon emoji or HTML - default: '⭐'
- `title` (string): Feature title - default: 'Feature Title'
- `description` (string): Feature description - default: 'Feature description goes here.'
- `iconPosition` (string): Icon position (top, left) - default: 'top'
- `alignment` (string): Text alignment - default: 'center'

**Usage:**
```html
<!-- wp:sofir/feature-box {"icon":"🚀","title":"Fast Performance","description":"Lightning fast loading times","iconPosition":"top","alignment":"center"} /-->
```

---

### 35. Contact Form Block (`sofir/contact-form`)

Contact form with validation.

**Attributes:**
- `title` (string): Form title - default: 'Contact Us'
- `showSubject` (boolean): Show subject field - default: true
- `showPhone` (boolean): Show phone field - default: false
- `submitText` (string): Submit button text - default: 'Send Message'

**Usage:**
```html
<!-- wp:sofir/contact-form {"title":"Get in Touch","showSubject":true,"showPhone":true,"submitText":"Send Message"} /-->
```

**Form Fields:**
- Name (required)
- Email (required)
- Phone (optional, if enabled)
- Subject (optional, if enabled)
- Message (required)

**AJAX Handler:** `sofir_contact_form` action

---

### 36. Social Share Block (`sofir/social-share`)

Social media share buttons.

**Attributes:**
- `title` (string): Share title - default: 'Share this:'
- `platforms` (array): Enabled platforms - default: ['facebook','twitter','linkedin','whatsapp']
- `layout` (string): Layout style (horizontal, vertical) - default: 'horizontal'

**Usage:**
```html
<!-- wp:sofir/social-share {"title":"Share this article","platforms":["facebook","twitter","linkedin","pinterest"],"layout":"horizontal"} /-->
```

**Supported Platforms:**
- Facebook
- Twitter
- LinkedIn
- WhatsApp
- Pinterest
- Email

---

### 37. Breadcrumb Block (`sofir/breadcrumb`)

Breadcrumb navigation trail.

**Attributes:**
- `showHome` (boolean): Show home link - default: true
- `separator` (string): Separator character - default: '/'
- `customClass` (string): Custom CSS class - default: ''

**Usage:**
```html
<!-- wp:sofir/breadcrumb {"showHome":true,"separator":"›","customClass":"my-breadcrumb"} /-->
```

---

### 38. Progress Bar Block (`sofir/progress-bar`)

Animated progress bar.

**Attributes:**
- `label` (string): Progress label - default: 'Progress'
- `value` (number): Progress value (0-100) - default: 50
- `color` (string): Bar color - default: '#007cba'
- `showPercentage` (boolean): Show percentage - default: true

**Usage:**
```html
<!-- wp:sofir/progress-bar {"label":"Completion","value":75,"color":"#28a745","showPercentage":true} /-->
```

---

### 39. Appointment Booking Block (`sofir/appointment-booking`)

Appointment booking form with calendar.

**Attributes:**
- `serviceType` (string): Service type - default: 'general'
- `showCalendar` (boolean): Show calendar picker - default: true
- `minDuration` (number): Min duration in minutes - default: 30

**Usage:**
```html
<!-- wp:sofir/appointment-booking {"serviceType":"consultation","showCalendar":true,"minDuration":60} /-->
```

**Form Fields:**
- Name (required)
- Email (required)
- Phone (optional)
- Date & Time (required)
- Duration (required)
- Notes (optional)

**AJAX Handler:** `sofir_book_appointment` action

**Custom Post Type:** `appointment`

**Appointment Meta:**
- `sofir_appointment_datetime` - ISO 8601 datetime
- `sofir_appointment_duration` - Duration in minutes
- `sofir_appointment_status` - Status (pending, confirmed, completed, cancelled)
- `sofir_appointment_provider` - Provider user ID
- `sofir_appointment_client` - Client user ID

---

### 40. Dynamic Data Block (`sofir/dynamic-data`)

Display dynamic content from various sources.

**Attributes:**
- `source` (string): Data source - default: 'post_meta'
  - Options: `post_meta`, `post_field`, `user_meta`, `user_field`, `site_option`, `cpt_field`
- `key` (string): Field/meta key - default: ''
- `postId` (number): Post ID (for post sources) - default: 0 (current post)
- `userId` (number): User ID (for user sources) - default: 0 (current user)
- `format` (string): Output format - default: 'text'
  - Options: `text`, `html`, `url`, `email`, `phone`, `date`, `number`, `currency`, `image`, `array`, `json`
- `fallback` (string): Default value if empty - default: ''
- `prefix` (string): Text before value - default: ''
- `suffix` (string): Text after value - default: ''
- `dateFormat` (string): PHP date format - default: 'F j, Y'
- `imageSize` (string): Image size - default: 'medium'

**Usage Examples:**

Display post meta:
```html
<!-- wp:sofir/dynamic-data {"source":"post_meta","key":"custom_price","format":"currency","prefix":"$"} /-->
```

Display user email as clickable link:
```html
<!-- wp:sofir/dynamic-data {"source":"user_field","key":"user_email","format":"email"} /-->
```

Display event date from CPT field:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"event_date","format":"date","dateFormat":"l, F j, Y"} /-->
```

Display gallery images:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"gallery","format":"image","imageSize":"large"} /-->
```

Display contact info:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"contact","format":"json"} /-->
```

**Supported CPT Fields:**
- `location` - Address, coordinates
- `hours` - Operating hours
- `rating` - Star rating
- `status` - Operational status
- `price` - Price range
- `contact` - Email, phone, website
- `gallery` - Image array
- `attributes` - Key-value pairs
- `event_date` - Event date/time
- `event_capacity` - Max attendees
- `appointment_datetime` - Appointment time
- `appointment_duration` - Duration
- `appointment_status` - Status
- `appointment_provider` - Provider ID
- `appointment_client` - Client ID

---

## Block Attributes Reference

### Common Attribute Types

1. **String**: Text values
2. **Number**: Numeric values
3. **Boolean**: true/false
4. **Array**: Lists of values
5. **Object**: Complex data structures

### Color Attributes

Most blocks accept standard CSS color values:
- Hex: `#007cba`
- RGB: `rgb(0, 124, 186)`
- Named: `blue`

### Size Attributes

Image sizes for WordPress media:
- `thumbnail` - 150x150
- `medium` - 300x300
- `medium_large` - 768xAuto
- `large` - 1024x1024
- `full` - Original size

---

## Usage Examples

### Building a Complete Landing Page

```html
<!-- wp:sofir/navbar {"menuId":1} /-->

<!-- wp:sofir/slider {"slides":[...],"autoplay":true} /-->

<!-- wp:sofir/feature-box {"icon":"🚀","title":"Fast Performance"} /-->
<!-- wp:sofir/feature-box {"icon":"🔒","title":"Secure Platform"} /-->
<!-- wp:sofir/feature-box {"icon":"📊","title":"Analytics Dashboard"} /-->

<!-- wp:sofir/pricing-table {"columns":3} /-->

<!-- wp:sofir/testimonial-slider {"numberOfItems":5} /-->

<!-- wp:sofir/cta-banner {"title":"Ready to Start?","buttonUrl":"/signup"} /-->

<!-- wp:sofir/contact-form /-->
```

### Building a Directory Page

```html
<!-- wp:sofir/search-form {"postType":"listing","advancedFilters":true} /-->

<!-- wp:sofir/map {"postType":"listing","zoom":12,"height":"600px"} /-->

<!-- wp:sofir/post-feed {"postType":"listing","layout":"grid","postsPerPage":12} /-->

<!-- wp:sofir/term-feed {"taxonomy":"listing_category","limit":20} /-->
```

### Building a User Dashboard

```html
<!-- wp:sofir/user-bar /-->

<!-- wp:sofir/dashboard {"showStats":true,"showRecent":true} /-->

<!-- wp:sofir/sales-chart {"period":"month"} /-->

<!-- wp:sofir/visit-chart {"period":"week"} /-->

<!-- wp:sofir/create-post {"postType":"listing"} /-->
```

### Building a Product Page

```html
<!-- wp:sofir/gallery {"imageIds":[123,456,789],"columns":4} /-->

<!-- wp:sofir/product-price {"showCurrency":true} /-->

<!-- wp:sofir/review-stats /-->

<!-- wp:sofir/work-hours {"showStatus":true} /-->

<!-- wp:sofir/social-share {"platforms":["facebook","twitter","pinterest"]} /-->

<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"location","format":"text"} /-->

<!-- wp:sofir/appointment-booking {"serviceType":"consultation"} /-->
```

---

## Styling and Customization

### Custom CSS Classes

All blocks support custom CSS classes via block settings:
1. Select the block
2. Open "Advanced" panel in block settings
3. Add custom CSS class

### Theme Integration

Blocks automatically inherit theme styles for:
- Typography (font family, sizes, weights)
- Colors (primary, secondary, accent)
- Spacing (margins, padding)
- Breakpoints (responsive behavior)

### Override Styles

To override block styles in your theme:

```css
/* Target specific block */
.sofir-pricing-table {
    gap: 3em;
}

/* Target block variant */
.sofir-cta-banner.gradient {
    background: linear-gradient(45deg, #your-colors);
}

/* Target nested elements */
.sofir-team-member .sofir-team-photo {
    border-radius: 50%;
}
```

### JavaScript Customization

Extend block functionality:

```javascript
document.addEventListener('sofir:block:updated', function(e) {
    // Handle block updates
    console.log('Block updated:', e.detail.block);
});

// Custom slider speed
jQuery('.sofir-slider').attr('data-interval', 3000);
```

---

## Block Compatibility

### Templately Integration

All SOFIR blocks are fully compatible with Templately templates. Import/export works seamlessly.

### Full Site Editing (FSE)

Blocks work in:
- Post editor
- Page editor
- Template editor
- Widget editor

### Theme Compatibility

Tested with:
- Twenty Twenty-Four
- Astra
- GeneratePress
- Kadence
- Block themes

### Plugin Compatibility

Compatible with:
- Yoast SEO
- Rank Math
- WooCommerce (payment blocks)
- Contact Form 7 (can replace with sofir/contact-form)
- Advanced Custom Fields (dynamic-data block)

---

## Performance Optimization

### Lazy Loading

Images in these blocks support lazy loading:
- `sofir/gallery`
- `sofir/slider`
- `sofir/testimonial-slider`
- `sofir/team-grid`

### AJAX Loading

These blocks use AJAX for better performance:
- `sofir/quick-search`
- `sofir/cart-summary`
- `sofir/post-feed` (with infinite scroll)
- `sofir/appointment-booking`

### Caching

Blocks support WordPress transients caching:
- Chart data cached for 1 hour
- Feed queries cached based on post updates
- Map markers cached per post type

---

## Accessibility

All blocks follow WCAG 2.1 AA guidelines:

- Semantic HTML structure
- ARIA labels and roles
- Keyboard navigation support
- Screen reader friendly
- Color contrast compliance
- Focus indicators

---

## Troubleshooting

### Block Not Appearing

1. Check block is registered: Look for `sofir/block-name`
2. Clear WordPress cache
3. Regenerate block assets

### Styling Issues

1. Check theme compatibility
2. Enqueue block styles: `wp_enqueue_style('sofir-blocks')`
3. Clear browser cache

### AJAX Not Working

1. Verify nonce: `sofir_blocks` nonce
2. Check AJAX URL: `sofirBlocks.ajaxUrl`
3. Enable WP_DEBUG to see errors

---

## Hooks and Filters

### Actions

```php
// After block registration
do_action('sofir/blocks/registered');

// After appointment booked
do_action('sofir/appointment/booked', $appointment_id);

// After contact form submitted
do_action('sofir/contact/submitted', $form_data);
```

### Filters

```php
// Modify block attributes
add_filter('sofir/block/attributes', function($attributes, $block_name) {
    return $attributes;
}, 10, 2);

// Customize block output
add_filter('sofir/block/output', function($output, $block_name, $attributes) {
    return $output;
}, 10, 3);

// Modify chart data
add_filter('sofir/chart/data', function($data, $chart_type) {
    return $data;
}, 10, 2);
```

---

## Developer API

### Registering Custom Block Variations

```php
add_action('init', function() {
    register_block_variation('sofir/action', [
        'name' => 'sofir-primary-action',
        'title' => __('Primary Action', 'sofir'),
        'attributes' => [
            'actionClass' => 'button-primary',
        ],
    ]);
});
```

### Extending Block with Custom Meta

```php
add_filter('sofir/block/meta_fields', function($fields, $post_type) {
    $fields['custom_field'] = [
        'type' => 'string',
        'label' => 'Custom Field',
        'default' => '',
    ];
    return $fields;
}, 10, 2);
```

---

## Support

For issues or questions:
- GitHub Issues: [Your repo URL]
- Documentation: [Your docs URL]
- Support Forum: [Your support URL]

---

**Last Updated:** 2024
**Version:** 1.0.0
**Plugin:** SOFIR WordPress Plugin
**Blocks:** 40 total
