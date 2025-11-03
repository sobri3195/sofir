# SOFIR

**Smart Optimized Framework for Integrated Rendering**

A modular, all-in-one WordPress plugin that provides comprehensive site management capabilities with native Gutenberg and Full Site Editing (FSE) support.

## Authors

**SOFIR** = **Sobri**, **Firman**

## Description

SOFIR is a comprehensive WordPress plugin that bootstraps via a custom autoloader and central loader singleton. It seamlessly integrates multiple modules into WordPress hooks to deliver a complete site toolkit including:

- **Custom Post Types & Taxonomies Management** - Dynamic CPT creation with rich metadata, custom fields, and filters
- **Template Management** - Gutenberg block patterns, one-click imports, and per-page link generation
- **SEO Engine** - Complete SEO management with schema, redirects, and analytics
- **Directory & Listings** - Filterable maps with Mapbox/Google Maps, mobile support, reviews, and timeline
- **Membership System** - Plan management, multi-vendor support, and content protection
- **Payment Gateways** - Manual payments and Indonesian gateways (Duitku, Xendit, Midtrans)
- **Webhooks Integration** - Compatible with Bit Integration for external service connections
- **Loyalty Program** - Points-based rewards for signup, login, and purchases
- **Phone Registration** - Allow users to register with phone number only
- **Mobile Support** - Responsive mobile menu and bottom navigation bar
- **28 Gutenberg Blocks** - Complete set including dashboard, forms, charts, maps, and more
- **AI Integration** - Intelligent content enhancement capabilities
- **Performance Enhancement** - Resource optimization and security features
- **REST API Extensions** - Enhanced API endpoints for content management

## Requirements

- **WordPress:** 6.3 or higher
- **PHP:** 8.0 or higher

## Features

### Admin Dashboard
- Content management with custom post types and taxonomies
- Template catalog browser and importer
- SEO settings and redirect management
- User and membership management
- Setup wizard for quick configuration
- Analytics dashboard with charts and statistics

### Custom Post Type Manager
- Create custom post types with metadata (location, hours, rating, etc.)
- Custom taxonomy creation and management
- Custom field definitions
- REST API query filters
- "Open now" scheduling support
- Event tracking and statistics

### Template System
- Ships with Gutenberg block patterns
- One-click page and FSE template import
- AJAX-powered template installation
- Multiple template categories (blog, directory, landing, profile)
- Per-page import with link generation
- Custom templates per CPT

### Directory Tools
- Shortcodes and blocks for listings
- Filterable map views (Mapbox/Google Maps)
- Review rating synchronization
- Front-end listings with vanilla JavaScript
- Location-based search
- Mobile-responsive design with mobile menu
- Bottom navigation bar for mobile devices
- Dashboard with charts and analytics

### Membership Features
- Plan and role storage
- Stripe payment integration
- Manual payment support
- Local payment gateways (Duitku, Xendit, Midtrans)
- Protected content shortcodes
- Pricing blocks for Gutenberg
- Member-only REST endpoints

### SEO Engine
- Per-post meta fields
- Schema markup generation
- Redirect management (301, 302, 307)
- AMP link support
- XML sitemap generation
- Lightweight analytics with JS tracker

### Enhancement Module
- Authentication shortcodes
- Phone-only registration support
- Login throttling and security
- Honeypot spam protection
- Performance tweaks
- Resource hints (preconnect, prefetch)
- Frontend user dashboard

### Payment System
- Manual payment processing
- Duitku payment gateway integration
- Xendit payment gateway integration
- Midtrans payment gateway integration
- Order management and tracking
- Payment webhooks

### Webhooks Integration
- Compatible with Bit Integration plugin
- User registration, login, and profile update triggers
- Payment status change triggers
- Post publishing triggers
- Comment submission triggers
- Form submission triggers
- REST API for webhook management

### Loyalty Program
- Points-based rewards system
- Signup rewards
- Login rewards
- Purchase rewards
- Point tracking and redemption

### Gutenberg Blocks (28 Elements)
Complete set of custom Gutenberg blocks:
- Action button
- Cart summary
- Countdown timer
- Create post form
- Dashboard widget
- Gallery
- Login/Register forms
- Interactive maps
- Messages/Direct messaging
- Navigation bar
- Order management
- Popup kit
- Post feed
- Print template
- Product form
- Product price display
- Quick search
- Review statistics
- Ring chart
- Sales chart
- Search form
- Slider
- Term feed
- Timeline
- Timeline style kit
- User bar
- Visit chart
- Work hours display

### Configuration Checker
- Automatic detection of wp-config.php issues
- Admin notices for duplicate constant definitions
- Guidance for fixing "headers already sent" errors
- Links to detailed troubleshooting documentation

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/sofir/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Run the setup wizard from the SOFIR admin menu

### Troubleshooting

If you encounter "Constant WP_DEBUG already defined" errors:
- See `WP_CONFIG_FIX_GUIDE.md` for detailed troubleshooting steps
- Reference `wp-config-sample.php` for proper configuration examples
- SOFIR will automatically detect and warn about common configuration issues

## Directory Structure

```
sofir/
├── assets/              # Frontend assets (CSS, JS)
│   ├── css/            # Stylesheets
│   └── js/             # JavaScript files (ES5)
├── includes/           # Core plugin files and classes
├── modules/            # Feature modules
│   ├── ai/            # AI integration
│   ├── directory/     # Directory and listings
│   ├── enhancement/   # Performance and security
│   └── membership/    # Membership system
├── templates/          # Block patterns and templates
│   ├── blog/          # Blog templates
│   ├── directory/     # Directory templates
│   ├── landing/       # Landing page templates
│   └── profile/       # Profile templates
└── sofir.php          # Main plugin file
```

## Architecture

SOFIR uses a modular architecture with:

- **Custom Autoloader** - Automatic class loading with flexible file structure support
- **Singleton Loader** - Central loader that bootstraps all modules
- **Hook-based Integration** - WordPress action/filter hooks for clean integration
- **Namespaced Code** - PHP 8+ with strict typing and modern practices
- **Lifecycle Management** - Activation/deactivation hooks for setup and cleanup

## Development

### Autoloader

The plugin includes a sophisticated autoloader that supports:
- PSR-4 style namespacing
- Kebab-case file naming
- Multiple file structure patterns
- Module-based organization

### Hooks

**Actions:**
- `sofir/before_bootstrap` - Fired before plugin initialization
- `sofir/after_bootstrap` - Fired after plugin initialization

**Filters:**
- Available through individual modules

## Ready-to-Use Features

SOFIR comes with complete, ready-to-use features for:
- **Directory** - Location-based listings with map integration
- **Appointments** - Schedule and manage appointments
- **Events** - Event management with CPT support
- **Reviews** - User reviews and rating system
- **Timeline** - Activity timeline and history
- **Membership** - Member management and subscriptions
- **Forms** - Custom form creation and handling
- **Google Sheets** - Integration capabilities via webhooks
- **Multi Vendor** - Vendor management support
- **Profile** - User profile management
- **Filters** - Advanced filtering for listings
- **Design Templates** - Pre-designed page templates
- **Taxonomy** - Custom taxonomy management
- **Direct Messages** - User-to-user messaging
- **Map Directory** - Interactive map-based directory
- **Dashboard & Charts** - Analytics dashboard with visualizations
- **Orders** - Order management system

## API Integration

SOFIR supports integration with:
- **Mapbox API** - For interactive maps
- **Google Maps API** - Alternative mapping solution
- **Stripe API** - Payment processing for memberships
- **Duitku API** - Indonesian payment gateway
- **Xendit API** - Indonesian payment gateway
- **Midtrans API** - Indonesian payment gateway
- **Bit Integration** - Webhook-based integrations

## Support

For issues, questions, or contributions, please contact the SOFIR Team.

## License

This plugin is licensed under the same terms as WordPress itself.

## Version

Current version: **0.1.0**

---

Built with ❤️ by Sobri and Firman
