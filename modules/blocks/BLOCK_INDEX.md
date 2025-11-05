# SOFIR Blocks Quick Index

Quick reference index for all 40 Gutenberg blocks. For complete documentation, see [BLOCKS_DOCUMENTATION.md](./BLOCKS_DOCUMENTATION.md).

## Index by Category

### 🎨 User Interface (5 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/action` | CTA buttons | `actionLabel`, `actionUrl`, `actionClass` |
| `sofir/navbar` | Navigation menu | `menuId`, `mobileBreakpoint` |
| `sofir/user-bar` | User profile bar | None (dynamic) |
| `sofir/popup-kit` | Modal popups | `triggerText`, `popupTitle`, `popupContent` |
| `sofir/breadcrumb` | Navigation trail | `showHome`, `separator` |

### 📄 Content Display (11 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/post-feed` | Post grid/list | `postType`, `postsPerPage`, `layout` |
| `sofir/term-feed` | Taxonomy terms | `taxonomy`, `limit` |
| `sofir/gallery` | Image gallery | `imageIds`, `columns` |
| `sofir/slider` | Image carousel | `slides`, `autoplay`, `interval` |
| `sofir/timeline` | Event timeline | `items`, `orientation` |
| `sofir/timeline-style-kit` | Timeline styles | `stylePreset`, `colorScheme` |
| `sofir/work-hours` | Business hours | `postId`, `showStatus` |
| `sofir/testimonial-slider` | Testimonials | `numberOfItems`, `showRating`, `autoplay` |
| `sofir/team-grid` | Team members | `columns`, `numberOfItems`, `showSocial` |
| `sofir/faq-accordion` | FAQ accordion | `numberOfItems`, `expandFirst` |
| `sofir/dynamic-data` | Dynamic content | `source`, `key`, `format`, `fallback` |

### 👤 User Features (5 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/login-register` | Login/register form | `showRegister`, `redirectUrl` |
| `sofir/dashboard` | User dashboard | `title`, `showStats`, `showRecent` |
| `sofir/create-post` | Frontend submission | `postType`, `buttonLabel` |
| `sofir/messages` | User messaging | None (dynamic) |
| `sofir/appointment-booking` | Appointment form | `serviceType`, `showCalendar`, `minDuration` |

### 🛒 E-Commerce (4 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/cart-summary` | Shopping cart | None (dynamic) |
| `sofir/order` | Order details | `orderId` |
| `sofir/product-form` | Product submission | None |
| `sofir/product-price` | Price display | `productId`, `showCurrency` |

### 📊 Data Visualization (4 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/sales-chart` | Sales line chart | `period`, `title` |
| `sofir/visit-chart` | Visitor bar chart | `period`, `title` |
| `sofir/ring-chart` | Doughnut chart | `data`, `title` |
| `sofir/review-stats` | Rating display | `postId` |

### 🔍 Search & Discovery (3 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/quick-search` | AJAX search | `postType`, `placeholder` |
| `sofir/search-form` | Advanced search | `postType`, `advancedFilters` |
| `sofir/map` | Location map | `postType`, `zoom`, `height` |

### 🎯 Marketing (4 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/cta-banner` | CTA banner | `title`, `description`, `buttonText`, `buttonUrl` |
| `sofir/feature-box` | Feature highlights | `icon`, `title`, `description` |
| `sofir/pricing-table` | Pricing plans | `columns`, `showFeatures`, `highlightBest` |
| `sofir/social-share` | Social sharing | `platforms`, `layout` |

### 🔧 Utility (4 blocks)
| Block | Purpose | Key Attributes |
|-------|---------|----------------|
| `sofir/countdown` | Countdown timer | `targetDate`, `format` |
| `sofir/print-template` | Print button | `templateId` |
| `sofir/contact-form` | Contact form | `title`, `showSubject`, `showPhone` |
| `sofir/progress-bar` | Progress indicator | `label`, `value`, `color` |

---

## Index by Use Case

### Landing Page
```
sofir/navbar
sofir/slider
sofir/feature-box
sofir/pricing-table
sofir/testimonial-slider
sofir/cta-banner
sofir/contact-form
```

### Directory Listing
```
sofir/quick-search
sofir/search-form
sofir/map
sofir/post-feed
sofir/term-feed
```

### User Dashboard
```
sofir/user-bar
sofir/dashboard
sofir/sales-chart
sofir/visit-chart
sofir/create-post
sofir/messages
```

### Product/Listing Detail
```
sofir/breadcrumb
sofir/gallery
sofir/product-price
sofir/review-stats
sofir/work-hours
sofir/dynamic-data
sofir/appointment-booking
sofir/social-share
```

### About/Team Page
```
sofir/timeline
sofir/team-grid
sofir/testimonial-slider
sofir/cta-banner
```

### FAQ/Support Page
```
sofir/faq-accordion
sofir/contact-form
sofir/quick-search
```

---

## Index by Data Source

### Post Data
```
sofir/post-feed         - Post listing
sofir/gallery           - Post attachments
sofir/product-price     - Post meta
sofir/review-stats      - Post meta + comments
sofir/work-hours        - Post meta
sofir/dynamic-data      - Post meta/fields
```

### User Data
```
sofir/user-bar          - Current user
sofir/dashboard         - User posts/stats
sofir/messages          - User messages
sofir/login-register    - User auth
sofir/dynamic-data      - User meta/fields
```

### Taxonomy Data
```
sofir/term-feed         - Taxonomy terms
sofir/search-form       - Taxonomy filters
```

### Custom Post Type Data
```
sofir/map               - CPT with location
sofir/post-feed         - Any CPT
sofir/appointment-booking - Appointment CPT
sofir/dynamic-data      - CPT fields
```

---

## Index by AJAX Feature

### AJAX-Enabled Blocks
```
sofir/quick-search      - Instant search
sofir/cart-summary      - Dynamic cart
sofir/messages          - Live messaging
sofir/create-post       - Frontend submit
sofir/contact-form      - Form submission
sofir/appointment-booking - Booking submit
```

---

## Index by Custom Post Type

### Requires Specific CPT
```
sofir/appointment-booking → appointment CPT
sofir/testimonial-slider  → testimonial CPT (configurable)
sofir/pricing-table       → pricing CPT (configurable)
sofir/team-grid          → team_member CPT (configurable)
sofir/faq-accordion      → faq CPT (configurable)
```

### Works with Any CPT
```
sofir/post-feed
sofir/map
sofir/quick-search
sofir/search-form
sofir/create-post
sofir/dynamic-data
```

---

## Index by Required Meta Fields

### Location Fields
```
sofir/map               → sofir_location
sofir/work-hours        → sofir_work_hours
sofir/dynamic-data      → location field
```

### Rating Fields
```
sofir/review-stats      → sofir_review_average
sofir/testimonial-slider → sofir_rating
```

### Contact Fields
```
sofir/team-grid         → sofir_email, sofir_twitter, sofir_linkedin
sofir/dynamic-data      → contact field
```

### Date/Time Fields
```
sofir/countdown         → targetDate attribute
sofir/work-hours        → sofir_work_hours
sofir/appointment-booking → sofir_appointment_datetime
sofir/dynamic-data      → event_date, appointment_datetime
```

### Price Fields
```
sofir/product-price     → sofir_product_price
sofir/pricing-table     → sofir_price, sofir_period, sofir_features
```

---

## Index by Authentication

### Requires Login
```
sofir/dashboard         - User dashboard
sofir/messages          - User messages
sofir/create-post       - Post submission
sofir/order             - Order details
```

### Shows Different Content
```
sofir/user-bar          - Login button vs user info
sofir/login-register    - Hidden if logged in
```

### No Auth Required
```
All other blocks work without login
```

---

## Index by Interactive Features

### Click/Hover Interactions
```
sofir/action            - Button hover
sofir/popup-kit         - Click to open
sofir/faq-accordion     - Click to expand
sofir/slider            - Click nav buttons
sofir/gallery           - Hover scale
```

### Form Inputs
```
sofir/login-register    - Login/register forms
sofir/create-post       - Post creation form
sofir/contact-form      - Contact form
sofir/search-form       - Search inputs
sofir/quick-search      - Search input
sofir/appointment-booking - Booking form
```

### Real-time Updates
```
sofir/countdown         - Live countdown
sofir/cart-summary      - Dynamic cart
sofir/messages          - Live messages
sofir/quick-search      - Instant results
```

---

## Index by Styling

### Gradient Backgrounds
```
sofir/cta-banner        - Default gradient
```

### Grid Layouts
```
sofir/gallery           - Image grid
sofir/post-feed         - Post grid
sofir/pricing-table     - Plan grid
sofir/team-grid         - Member grid
sofir/feature-box       - Feature grid
```

### Slider/Carousel
```
sofir/slider            - Image slider
sofir/testimonial-slider - Testimonial carousel
```

### Accordion
```
sofir/faq-accordion     - Expandable items
```

---

## Index by Responsive Features

### Mobile-Optimized
```
sofir/navbar            - Mobile toggle
sofir/map               - Touch-friendly
sofir/slider            - Swipe support
sofir/gallery           - Responsive grid
sofir/pricing-table     - Stacked on mobile
```

---

## Index by Animation

### Animated Elements
```
sofir/countdown         - Live timer
sofir/progress-bar      - Progress animation
sofir/slider            - Slide transitions
sofir/gallery           - Hover effects
sofir/action            - Hover transform
```

---

## Index by Chart Type

### Chart Blocks
```
sofir/ring-chart        - Doughnut chart
sofir/sales-chart       - Line chart
sofir/visit-chart       - Bar chart
```

---

## Attribute Type Reference

### String Attributes
```
title, description, label, placeholder, format, postType, taxonomy,
source, key, separator, alignment, color, url, icon, stylePreset
```

### Number Attributes
```
postId, userId, orderId, columns, limit, zoom, interval, value,
numberOfItems, postsPerPage, mobileBreakpoint, minDuration
```

### Boolean Attributes
```
showStats, showRecent, showRegister, showStatus, showFeatures,
showRating, showSocial, showSubject, showPhone, showHome,
showCurrency, showPercentage, autoplay, expandFirst, highlightBest,
advancedFilters, showCalendar
```

### Array Attributes
```
imageIds, slides, items, data, platforms, features
```

### Object Attributes
```
hours (work schedule)
contact (email, phone, website)
location (address, coordinates)
```

---

## Dynamic Data Sources

### Available Sources
1. `post_meta` - WordPress post meta
2. `post_field` - Native post fields (title, content, date, author, etc.)
3. `user_meta` - WordPress user meta
4. `user_field` - Native user fields (display_name, email, etc.)
5. `site_option` - WordPress options
6. `cpt_field` - SOFIR CPT fields (location, hours, rating, etc.)

### Format Options
1. `text` - Plain text
2. `html` - HTML content
3. `url` - URL link
4. `email` - Email link
5. `phone` - Phone link
6. `date` - Formatted date
7. `number` - Formatted number
8. `currency` - Currency format
9. `image` - Image display
10. `array` - Comma-separated list
11. `json` - JSON output

---

## Block Compatibility Matrix

| Block | FSE | Classic | Widgets | Templates |
|-------|-----|---------|---------|-----------|
| All 40 blocks | ✅ | ✅ | ✅ | ✅ |

---

## Performance Notes

### Heavy Blocks (Cache Recommended)
```
sofir/sales-chart       - Database queries
sofir/visit-chart       - Database queries
sofir/ring-chart        - Data processing
sofir/post-feed         - Post queries
sofir/term-feed         - Term queries
sofir/map               - Location queries
```

### Lightweight Blocks
```
sofir/action
sofir/breadcrumb
sofir/progress-bar
sofir/countdown
sofir/print-template
sofir/user-bar
```

---

## Common Combinations

### Hero Section
```html
<!-- wp:sofir/navbar /-->
<!-- wp:sofir/slider /-->
<!-- wp:sofir/cta-banner /-->
```

### Features Section
```html
<!-- wp:sofir/feature-box /-->
<!-- wp:sofir/feature-box /-->
<!-- wp:sofir/feature-box /-->
```

### Testimonial Section
```html
<!-- wp:sofir/testimonial-slider /-->
<!-- wp:sofir/review-stats /-->
```

### Contact Section
```html
<!-- wp:sofir/contact-form /-->
<!-- wp:sofir/map /-->
<!-- wp:sofir/social-share /-->
```

---

## Quick Start Examples

### Simple Button
```html
<!-- wp:sofir/action {"actionLabel":"Click Here","actionUrl":"#"} /-->
```

### User Dashboard
```html
<!-- wp:sofir/dashboard {"showStats":true} /-->
```

### Product Gallery
```html
<!-- wp:sofir/gallery {"imageIds":[1,2,3],"columns":3} /-->
```

### Contact Form
```html
<!-- wp:sofir/contact-form {"showPhone":true} /-->
```

### Search Bar
```html
<!-- wp:sofir/quick-search {"postType":"post"} /-->
```

---

**Total Blocks:** 40
**Documentation:** See BLOCKS_DOCUMENTATION.md or PANDUAN_BLOK.md
**Version:** 1.0.0
