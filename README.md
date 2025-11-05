# SOFIR

**Smart Optimized Framework for Integrated Rendering**

A comprehensive, modular WordPress plugin that provides complete site management capabilities with native Gutenberg and Full Site Editing (FSE) support.

[![Version](https://img.shields.io/badge/version-0.1.0-blue.svg)](https://github.com/sofir/sofir)
[![WordPress](https://img.shields.io/badge/wordpress-6.3%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/php-8.0%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](LICENSE)

---

## 🎯 Overview

SOFIR is an all-in-one WordPress solution built from the ground up with modern PHP 8.0+ architecture. It combines powerful site-building capabilities with advanced features like directory management, membership systems, multiple payment gateways, webhooks integration, loyalty programs, and 28 custom Gutenberg blocks.

### Authors

**SOFIR** = **Sobri** + **Firman**

---

## ✨ Key Highlights

- 🧩 **28 Custom Gutenberg Blocks** - Complete set of production-ready blocks
- 📱 **Mobile-First Design** - Responsive menu, bottom navbar, touch-optimized
- 💳 **Indonesian Payment Gateways** - Duitku, Xendit, Midtrans + Manual payments
- 🔗 **Bit Integration Compatible** - Full webhooks support
- 🎁 **Loyalty Rewards Program** - Points-based rewards system
- 📞 **Phone-Only Registration** - Users can register with just phone number
- 🗺️ **Interactive Maps** - Mapbox & Google Maps integration
- 🤖 **AI-Powered Builder** - Intelligent content generation
- 🎨 **Template Library** - One-click import of pre-designed templates
- 🚀 **Performance Optimized** - Lightweight, no build process required

---

## 📋 Requirements

- **WordPress:** 6.3 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 5.7 or higher
- **HTTPS:** Recommended for payment processing

---

## 🎯 Complete Feature List

### 1. 🧩 Gutenberg Blocks (28 Elements)

Complete set of custom Gutenberg blocks ready to use:

| Block Name | Description |
|-----------|-------------|
| **Action** | Customizable action buttons with URL and styling |
| **Cart Summary** | Shopping cart display with real-time updates |
| **Countdown** | Countdown timer for events and promotions |
| **Create Post** | Frontend post creation form for users |
| **Dashboard** | User dashboard widget with stats and activity |
| **Gallery** | Image gallery with lightbox support |
| **Login/Register** | Complete authentication forms |
| **Map** | Interactive maps (Mapbox/Google Maps) |
| **Messages** | Direct messaging interface between users |
| **Navbar** | Customizable navigation menu |
| **Order** | Order management interface |
| **Popup Kit** | Modal/popup creator with triggers |
| **Post Feed** | Custom post feed display with filters |
| **Print Template** | Printable template renderer |
| **Product Form** | Product submission form |
| **Product Price** | Price display widget with variations |
| **Quick Search** | AJAX-powered instant search |
| **Review Stats** | Review statistics and ratings display |
| **Ring Chart** | Donut/ring chart for data visualization |
| **Sales Chart** | Sales data visualization with trends |
| **Search Form** | Advanced search form with filters |
| **Slider** | Content slider/carousel |
| **Term Feed** | Taxonomy term display and filtering |
| **Timeline** | Event timeline with milestones |
| **Timeline Style Kit** | Timeline styling options and themes |
| **User Bar** | User info display bar with actions |
| **Visit Chart** | Visit analytics and statistics chart |
| **Work Hours** | Business hours display with "Open Now" status |

### 2. 📝 Custom Post Types & Taxonomies

**Dynamic CPT Management:**
- Create custom post types with rich metadata
- Custom taxonomy creation and management
- Custom field definitions (location, hours, rating, price, etc.)
- REST API query filters for advanced queries
- Event tracking and statistics per CPT
- Template assignment per post type
- "Open now" scheduling support for businesses
- Location-based search capabilities
- Rating and review integration

### 3. 🎨 Template System

**One-Click Template Import:**
- Ships with professional Gutenberg block patterns
- One-click page template import
- FSE (Full Site Editing) template support
- AJAX-powered installation without page reload
- Per-page import with automatic link generation
- **Clickable preview images** with live template preview
- **Copy pattern** feature for headers/footers (one-click to clipboard)
- Multiple template categories:
  - **Landing** (7 templates) - Startup, agency, restaurant, real estate, event, course, SaaS
  - **Directory** (6 templates) - City directory, healthcare, fitness, hotel, job board, lawyer
  - **Blog** (5 templates) - Magazine, tech news, personal, recipe, travel
  - **Profile** (5 templates) - Business, freelancer, agency, resume, photography
  - **Ecommerce** (2 templates) - Product catalog, checkout page
  - **Membership** (2 templates) - Member dashboard, pricing plans
  - **Header** (4 templates) - Modern, minimal, business, centered designs
  - **Footer** (4 templates) - Multi-column, simple, business, newsletter designs
- Custom templates per CPT
- Import queue management
- Template preview before installation
- **Gutenberg-ready header/footer templates** - Ready-to-use, copyable block patterns

### 4. 🗺️ Directory & Listings

**Complete Directory Solution:**
- Shortcodes and Gutenberg blocks for listings
- Filterable map views (Mapbox & Google Maps)
- Location-based search with radius
- Review and rating system
- Rating synchronization with WordPress comments
- Front-end listings with vanilla JavaScript
- Advanced filtering (category, location, price, rating)
- Mobile-responsive design
- **Mobile menu toggle** for better UX
- **Bottom navigation bar** for mobile devices
- Dashboard with analytics and charts
- "Open Now" status for businesses
- Contact information display
- Business hours integration

### 5. 👥 Membership System

**Complete Membership Platform:**
- Membership plan management (Free, Basic, Pro, etc.)
- Role-based access control
- Protected content shortcodes
- Pricing blocks for Gutenberg
- Member-only REST endpoints
- Subscription management
- Plan upgrades and downgrades
- Stripe payment integration
- Multi-vendor support capabilities
- Member dashboard
- Expiration management
- Email notifications

### 6. 💳 Payment Processing

**Multiple Payment Gateways:**

#### Indonesian Payment Gateways:
- **Duitku** - Complete integration with all payment channels
- **Xendit** - Credit cards, e-wallets, virtual accounts
- **Midtrans** - Full payment gateway support

#### Additional Methods:
- **Manual Payments** - Bank transfer, cash, etc.
- **Stripe** - International credit card processing

**Features:**
- Order management and tracking
- Payment status tracking (pending, completed, failed)
- Payment webhooks for automatic updates
- Receipt generation
- Payment history
- Refund support
- Multiple currency support
- Secure payment processing
- Test mode for development

### 7. 🔗 Webhooks Integration

**Bit Integration Compatible:**
- Full REST API for webhook management
- Connect with 200+ external services via Bit Integration
- **10 Comprehensive Triggers**
- **3 Action Endpoints**
- Visual integration builder
- No coding required

**Webhook Triggers:**
- User registration (with phone, name, email, roles)
- User profile updates
- User login (with timestamp)
- Payment completed (all gateways: Manual, Duitku, Xendit, Midtrans)
- Post publishing
- Comment submissions
- Form submissions
- Membership changes
- Appointment created/updated
- Custom events

**SOFIR Actions (callable from external apps):**
- Create User (with phone support)
- Update User (profile fields)
- Create Post (any post type)

**Features:**
- Test webhook functionality
- Webhook activity logging
- Retry mechanism for failed webhooks
- Custom webhook endpoints
- JSON payload support
- Authentication support
- Field mapping support
- Conditional logic support

**Popular Integrations:**
- 📧 Email Marketing: Mailchimp, ActiveCampaign, ConvertKit
- 💬 Messaging: Slack, Telegram, WhatsApp, Discord
- 📊 Spreadsheets: Google Sheets, Airtable, Excel Online
- 🔧 CRM: HubSpot, Salesforce, Zoho CRM
- 🎯 Analytics: Google Analytics, Mixpanel
- And 200+ more services via Bit Integration

**Documentation:**
- See [BIT_INTEGRATION_GUIDE.md](BIT_INTEGRATION_GUIDE.md) for Indonesian guide
- See [BIT_INTEGRATION_README.md](BIT_INTEGRATION_README.md) for English guide
- See [BIT_INTEGRATION_TEST.md](BIT_INTEGRATION_TEST.md) for testing checklist

### 8. 🎁 Loyalty & Rewards Program

**Points-Based System:**
- Automatic point allocation
- Configurable point values
- Point tracking per user
- Point history and transactions (up to 50 entries)

**Reward Events:**
- **Signup Rewards** - Points for new user registration (default: 100 points)
- **Login Rewards** - Daily login bonuses (default: 10 points, 1x per day)
- **Comment Rewards** - Points for approved comments (default: 5 points)
- **Post Rewards** - Points for publishing posts (default: 20 points)
- **Purchase Rewards** - Points per transaction (default: 1 point per currency unit)

**Features:**
- Point redemption system
- Reward catalog with customizable items
- REST API endpoints for points and rewards
- Shortcodes: `[sofir_loyalty_points]` and `[sofir_loyalty_rewards]`
- Event hooks for customization
- Admin UI for configuration

**REST API Endpoints:**
- `GET /wp-json/sofir/v1/loyalty/points/{user_id}` - Get user points
- `GET /wp-json/sofir/v1/loyalty/history/{user_id}` - Get points history
- `POST /wp-json/sofir/v1/loyalty/redeem` - Redeem rewards
- `GET /wp-json/sofir/v1/loyalty/rewards` - Get available rewards

**Documentation:**
- See [LOYALTY_PROGRAM_GUIDE.md](LOYALTY_PROGRAM_GUIDE.md) for Indonesian guide
- See [LOYALTY_PROGRAM_DOCUMENTATION.md](LOYALTY_PROGRAM_DOCUMENTATION.md) for English documentation

### 9. 🔐 User Authentication & Security

**Phone-Only Registration:**
- Users can register with **just phone number** (no email required)
- Phone-based login support
- Auto-generated username and secure password
- SMS/OTP verification integration ready
- Alternative email/username/password login

**Shortcodes:**
```
[sofir_register_form phone_only="true" redirect="/dashboard"]
[sofir_login_form]
[sofir_logout_link]
```

**Gutenberg Blocks:**
- SOFIR Register Form (with phone-only toggle)
- SOFIR Login Form
- User authentication blocks

**REST API Endpoints:**
- `/wp-json/sofir/v1/auth/register` - User registration
- `/wp-json/sofir/v1/auth/phone-login` - Phone number login

**Security Features:**
- Login throttling and rate limiting
- Honeypot spam protection
- CSRF protection via nonces
- Password strength validation
- Brute force protection
- Session management
- Secure cookie handling
- Auto-generated secure passwords

**Documentation:**
- See [PHONE_REGISTRATION_GUIDE.md](PHONE_REGISTRATION_GUIDE.md) for Indonesian guide
- See [PHONE_REGISTRATION_DOCUMENTATION.md](PHONE_REGISTRATION_DOCUMENTATION.md) for English documentation

### 10. 🔍 SEO Engine

**Complete SEO Suite:**
- Per-post meta fields (title, description, keywords)
- Schema markup generation (JSON-LD)
- Redirect management (301, 302, 307)
- AMP link support
- XML sitemap generation
- Robots.txt management
- Canonical URL support
- Open Graph tags
- Twitter Cards
- Breadcrumb navigation
- Lightweight analytics with JS tracker
- Page speed optimization hints

### 11. 🤖 AI Integration

**AI-Powered Content Builder:**
- Intelligent content generation
- Content enhancement capabilities
- Smart suggestions for writing
- SEO optimization suggestions
- Tone and style adjustments
- Content structure recommendations

### 12. 📱 Mobile Support

**Mobile-First Features:**
- Fully responsive layouts
- Touch-optimized controls
- **Mobile menu system** with smooth animations
- **Bottom navigation bar** for mobile devices
- Mobile-specific features and layouts
- Adaptive breakpoints
- Mobile user detection
- Swipe gestures support
- Mobile-optimized directory listings
- Touch-friendly forms

### 13. ⚡ Performance Enhancements

**Speed Optimization:**
- Resource hints (preconnect, prefetch, dns-prefetch)
- Optimized asset loading
- Lazy loading support
- Minimal dependencies
- Plain ES5 JavaScript (no build process needed)
- CSS optimization
- Database query optimization
- Caching recommendations
- CDN support

### 14. 🎛️ Admin Dashboard

**Comprehensive Admin Panel:**
- **Content Management** - Custom post types and taxonomies
- **Template Catalog** - Browse and import templates
- **SEO Settings** - Complete SEO management
- **User Management** - Members and permissions
- **Setup Wizard** - Quick configuration for new sites
- **Analytics Dashboard** - Charts and statistics
- **Payment Management** - Orders and transactions
- **Webhook Management** - Configure integrations
- **Loyalty Settings** - Points and rewards configuration

### 15. 🔌 REST API Extensions

**Enhanced API Endpoints:**
- Custom REST endpoints for all modules
- Authentication support (JWT ready)
- Rate limiting for security
- Webhook endpoints
- Extended WP REST API filters
- Custom query parameters
- Pagination support
- Error handling
- API documentation

### 16. 🌐 Internationalization

**Translation Ready:**
- Text domain: `sofir`
- Full .pot file included
- Multiple language support
- RTL support ready
- Date/time localization
- Currency localization
- Number formatting

### 17. 🛠️ Configuration Checker

**Automatic Issue Detection:**
- Detects wp-config.php issues
- Admin notices for duplicate constant definitions
- "Headers already sent" error detection
- Guidance for fixing common problems
- Links to detailed troubleshooting documentation

---

## 🚀 Installation

### Standard Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/sofir/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Run the setup wizard from the SOFIR admin menu
5. Configure your settings and start building!

### Via WP-CLI

```bash
wp plugin install sofir.zip --activate
```

### First Time Setup

1. **Run Setup Wizard** - Navigate to SOFIR → Setup Wizard
2. **Configure Basics** - Set site type, currency, timezone
3. **Choose Features** - Enable modules you need
4. **Import Templates** - Select and import starter templates
5. **Configure Payment** - Set up payment gateways (if needed)
6. **Test Your Site** - Create test pages and check functionality

---

## 📂 Directory Structure

```
sofir/
├── assets/                    # Frontend assets (CSS, JS)
│   ├── css/                  # Stylesheets
│   │   ├── admin.css        # Admin panel styles
│   │   ├── blocks.css       # Gutenberg blocks styles
│   │   ├── directory.css    # Directory styles
│   │   └── mobile.css       # Mobile-specific styles
│   └── js/                   # JavaScript files (ES5)
│       ├── admin.js         # Admin panel scripts
│       ├── blocks.js        # Block functionality
│       ├── directory.js     # Directory features
│       └── mobile.js        # Mobile enhancements
│
├── includes/                  # Core plugin files and classes
│   ├── class-admin-*.php    # Admin panel classes
│   ├── class-config-checker.php
│   ├── class-rest-router.php
│   ├── sofir-cpt-manager.php
│   ├── sofir-loader.php      # Central loader singleton
│   ├── sofir-seo-engine.php
│   └── sofir-*.php          # Core utilities
│
├── modules/                   # Feature modules
│   ├── ai/                   # AI integration
│   │   └── builder.php      # AI content builder
│   ├── blocks/               # Gutenberg blocks
│   │   └── elements.php     # 28 block definitions
│   ├── directory/            # Directory and listings
│   │   ├── manager.php      # Directory manager
│   │   └── mobile.php       # Mobile support
│   ├── enhancement/          # Performance and security
│   │   ├── auth.php         # Authentication (phone registration)
│   │   ├── dashboard.php    # User dashboard
│   │   ├── performance.php  # Performance tweaks
│   │   ├── security.php     # Security features
│   │   └── web.php          # Web enhancements
│   ├── loyalty/              # Loyalty program
│   │   └── manager.php      # Rewards system
│   ├── membership/           # Membership system
│   │   └── manager.php      # Plans and subscriptions
│   ├── payments/             # Payment gateways
│   │   └── manager.php      # Payment processing
│   └── webhooks/             # Webhooks integration
│       └── manager.php      # Webhook management
│
├── templates/                 # Block patterns and templates
│   ├── blog/                 # Blog templates
│   ├── directory/            # Directory templates
│   ├── landing/              # Landing page templates
│   └── profile/              # Profile templates
│
├── languages/                 # Translation files
├── sofir.php                 # Main plugin file
└── README.md                 # This file
```

---

## 🏗️ Architecture

### Modern PHP 8.0+ Design

SOFIR is built with modern software engineering principles:

**Custom Autoloader:**
- PSR-4 style namespacing
- Automatic class loading
- Kebab-case file naming support
- Multiple file structure patterns
- Module-based organization

**Singleton Loader Pattern:**
- Central loader bootstraps all modules
- Lazy loading for better performance
- Dependency injection ready
- Module discovery system

**Hook-Based Integration:**
- Clean WordPress action/filter hooks
- No global state pollution
- Easy to extend and customize
- Theme agnostic

**Modern PHP Features:**
- PHP 8.0+ with strict typing
- Type hints for parameters and returns
- Named arguments support
- Return type declarations
- Nullable types
- Property promotion

**Code Quality:**
- WordPress coding standards
- PHPDoc blocks for documentation
- No inline comments (clean code)
- Single Responsibility Principle
- DRY (Don't Repeat Yourself)

### Lifecycle Management

**Activation:**
- Database table creation
- Default options setup
- Flush rewrite rules
- Create required directories

**Deactivation:**
- Cleanup temporary data
- Preserve user data
- Flush rewrite rules

---

## 🎬 Ready-to-Use Use Cases

SOFIR provides complete, production-ready solutions for:

### 🏪 Business Directory
Location-based business listings with maps, reviews, and contact information.

### 📅 Appointment Booking
Schedule and manage appointments with calendar integration.

### 🎉 Event Management
Create, manage, and promote events with registration.

### ⭐ Review Platform
User reviews, ratings, and testimonials.

### 📊 Timeline & History
Activity timeline, history tracking, and milestones.

### 👤 Membership Site
Member-only content, subscriptions, and user management.

### 📝 Custom Forms
Form builder with submissions and notifications.

### 📄 Google Sheets Integration
Export data to Google Sheets via webhooks and Bit Integration.

### 🛍️ Multi Vendor Marketplace
Vendor registration, product management, and sales tracking.

### 💬 Community Platform
User profiles, direct messages, and social features.

### 🔍 Advanced Filtering
Filter content by category, location, price, rating, and more.

### 📱 Mobile App Backend
REST API endpoints for mobile app development.

---

## 🔌 API Integrations

SOFIR supports seamless integration with:

- **Mapbox API** - Interactive maps and geocoding
- **Google Maps API** - Alternative mapping solution
- **Stripe API** - International payment processing
- **Duitku API** - Indonesian payment gateway
- **Xendit API** - Indonesian payment gateway
- **Midtrans API** - Indonesian payment gateway
- **Bit Integration** - Connect with 1000+ services (Zapier alternative)
- **Google Sheets** - Data export via webhooks
- **Custom APIs** - Easy to add your own integrations

---

## 🔧 Hooks & Filters

### Actions

```php
// Before plugin initialization
do_action( 'sofir/before_bootstrap' );

// After plugin initialization
do_action( 'sofir/after_bootstrap' );

// Additional module-specific actions available
```

### Filters

```php
// Modify loaded modules
apply_filters( 'sofir/modules', $modules );

// Available filters in individual modules
// See module documentation for details
```

---

## 📖 Usage Examples

### Using Gutenberg Blocks

Simply add any of the 28 blocks from the Gutenberg editor:

1. Click the "+" icon in the editor
2. Search for "SOFIR" blocks
3. Add the block you need
4. Configure block settings in the sidebar
5. Publish your page

### Creating Custom Post Types

```php
// Via admin panel
1. Go to SOFIR → Content
2. Click "Add New Post Type"
3. Configure fields and settings
4. Save and use immediately
```

### Adding Phone Registration

Phone registration is enabled by default in the authentication module. Users can register using:
- Phone number only (no email required)
- Email/username (traditional method)

### Configuring Payment Gateways

```php
1. Go to SOFIR → Settings → Payments
2. Choose your payment gateway (Duitku, Xendit, or Midtrans)
3. Enter API credentials
4. Enable test mode for testing
5. Configure currency and settings
6. Save and start accepting payments
```

### Setting Up Webhooks

```php
1. Install Bit Integration plugin (optional but recommended)
2. Go to SOFIR → Webhooks
3. Create new webhook trigger
4. Select event (user registration, payment, etc.)
5. Configure destination URL
6. Test webhook
7. Activate webhook
```

---

## 🛠️ Troubleshooting

### Common Issues

**"Constant WP_DEBUG already defined" Error:**
- See `WP_CONFIG_FIX_GUIDE.md` for detailed steps
- Reference `wp-config-sample.php` for proper configuration
- SOFIR automatically detects and warns about configuration issues

**"Headers already sent" Error:**
- Check `HEADERS_ALREADY_SENT_FIX.md`
- Remove whitespace before `<?php` in wp-config.php
- Check for BOM (Byte Order Mark) in files

**Blocks Not Showing:**
- Clear browser cache
- Disable other block plugins temporarily
- Check JavaScript console for errors

**Payment Gateway Issues:**
- Verify API credentials
- Check test mode settings
- Review webhook configuration
- Check server firewall rules

**Map Not Loading:**
- Verify Mapbox/Google Maps API key
- Check domain restrictions in API settings
- Review browser console for errors

For more help, see the documentation files included in the plugin.

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow WordPress Coding Standards
- Use PHP 8.0+ features and strict typing
- Add PHPDoc blocks for classes and complex methods
- No inline comments unless absolutely necessary
- Write clean, self-documenting code

---

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 🆘 Support

For issues, questions, or feature requests:

- **Documentation:** See included markdown files
- **Issues:** Report bugs or request features
- **Community:** Join our community forum
- **Email:** Contact the SOFIR Team

---

## 📝 Changelog

### Version 0.1.0 (Initial Release)

**Core Features:**
- ✅ Custom autoloader with PSR-4 support
- ✅ Singleton loader pattern
- ✅ 28 Gutenberg blocks
- ✅ Custom post types & taxonomies
- ✅ Template system with one-click import
- ✅ Directory & listings with maps
- ✅ Membership system
- ✅ Multiple payment gateways (Duitku, Xendit, Midtrans)
- ✅ Webhooks (Bit Integration compatible)
- ✅ Loyalty rewards program
- ✅ Phone-only registration
- ✅ Mobile support (menu + bottom navbar)
- ✅ SEO engine
- ✅ AI content builder
- ✅ REST API extensions
- ✅ Configuration checker

---

## 🙏 Credits

**Developed by:**
- **Sobri** - Lead Developer
- **Firman** - Core Developer

**Special Thanks:**
- WordPress Community
- Gutenberg Team
- All Contributors

---

## 🌟 Why Choose SOFIR?

✅ **All-in-One Solution** - Everything you need in one plugin  
✅ **Modern Architecture** - Built with PHP 8.0+ and modern practices  
✅ **Mobile-First** - Responsive and mobile-optimized  
✅ **Indonesian-Friendly** - Local payment gateways included  
✅ **Performance-Focused** - Lightweight and fast  
✅ **Extensible** - Easy to customize and extend  
✅ **Production-Ready** - Fully tested and stable  
✅ **Developer-Friendly** - Clean code and documentation  

---

**Built with ❤️ by Sobri and Firman**

**Version:** 0.1.0  
**Last Updated:** 2024  
**Status:** Production Ready

---

For detailed documentation on specific features, please refer to:
- `FEATURES.md` - Complete feature documentation
- `WP_CONFIG_FIX_GUIDE.md` - Configuration troubleshooting
- `TEMPLATE_IMPORT_FEATURES.md` - Template system guide
- `IMPLEMENTATION_SUMMARY.md` - Technical implementation details
