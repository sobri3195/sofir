# SOFIR

**Smart Optimized Framework for Integrated Rendering**

A modular, all-in-one WordPress plugin that provides comprehensive site management capabilities with native Gutenberg and Full Site Editing (FSE) support.

## Authors

**SOFIR** = **Sobri**, **Firman**

## Description

SOFIR is a powerful WordPress plugin that bootstraps via a custom autoloader and central loader singleton. It seamlessly integrates multiple modules into WordPress hooks to deliver a complete site toolkit including:

- **Custom Post Types & Taxonomies Management** - Dynamic CPT creation with rich metadata
- **Template Management** - Gutenberg block patterns and one-click template imports
- **SEO Engine** - Complete SEO management with schema, redirects, and analytics
- **Directory & Listings** - Filterable maps with Mapbox/Google Maps integration
- **Membership System** - Plan management, Stripe integration, and content protection
- **AI Integration** - Intelligent content enhancement capabilities
- **Performance Enhancement** - Resource optimization and security features
- **REST API Extensions** - Enhanced API endpoints for content management
- **Blocks & Patterns** - Custom Gutenberg blocks and reusable patterns

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
- Analytics dashboard

### Custom Post Type Manager
- Seed metadata (location, hours, rating, etc.)
- REST API query filters
- "Open now" scheduling support
- Taxonomy management

### Template System
- Ships with Gutenberg block patterns
- One-click page and FSE template import
- AJAX-powered template installation
- Multiple template categories (blog, directory, landing, profile)

### Directory Tools
- Shortcodes and blocks for listings
- Filterable map views (Mapbox/Google Maps)
- Review rating synchronization
- Front-end listings with vanilla JavaScript
- Location-based search

### Membership Features
- Plan and role storage
- Stripe payment integration
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
- Login throttling and security
- Honeypot spam protection
- Performance tweaks
- Resource hints (preconnect, prefetch)
- Frontend user dashboard

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/sofir/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Run the setup wizard from the SOFIR admin menu

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

## API Integration

SOFIR supports integration with:
- **Mapbox API** - For interactive maps
- **Google Maps API** - Alternative mapping solution
- **Stripe API** - Payment processing for memberships

## Support

For issues, questions, or contributions, please contact the SOFIR Team.

## License

This plugin is licensed under the same terms as WordPress itself.

## Version

Current version: **0.1.0**

---

Built with ❤️ by Sobri and Firman
