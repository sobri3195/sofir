# SOFIR Elementor Template Library

Professional template library for Elementor, inspired by Slider Revolution's design system.

## Overview

The SOFIR Elementor Template Library provides 40+ professional, pre-designed templates that can be easily imported into any Elementor page. These templates cover all common use cases including popups, cards, pages, single posts, archives, headers, and footers.

## Features

### 🎨 Template Categories

1. **Popups (6 templates)**
   - Newsletter Subscription
   - Promotional Offer with Countdown
   - Exit Intent Offer
   - Video Presentation
   - Announcement Banner
   - Quick Contact Form

2. **Cards (8 templates)**
   - Modern Post Card
   - Premium Product Card
   - Team Member Card
   - Minimal Service Card
   - Creative Pricing Card
   - Testimonial Card
   - Event Card
   - Portfolio Card

3. **Pages (8 templates)**
   - Hero Slider Landing
   - About Company
   - Services Showcase
   - Modern Contact Page
   - Portfolio Gallery
   - Pricing Plans
   - FAQ Page
   - Coming Soon

4. **Single Templates (4 templates)**
   - Modern Single Post
   - Magazine Style Post
   - Product Single Layout
   - Portfolio Item

5. **Archive Templates (5 templates)**
   - Blog Archive Grid
   - Blog Archive Masonry
   - Shop Archive
   - Portfolio Archive
   - Directory Listing Archive

6. **Headers (3 templates)**
   - Transparent Header
   - Header with Top Bar
   - Centered Header

7. **Footers (3 templates)**
   - Footer 4 Columns
   - Footer Minimal
   - Footer with CTA

### ✨ Key Features

- **One-Click Import**: Import any template with a single click
- **Beautiful Preview**: Large preview images for each template
- **Search & Filter**: Quickly find the right template
- **Tag System**: Templates are tagged for easy discovery
- **Responsive Design**: All templates are fully responsive
- **Professional Design**: Inspired by Slider Revolution
- **Gradient Backgrounds**: Modern gradient color schemes
- **Hover Effects**: Smooth animations and transitions
- **Icon Integration**: Full Font Awesome support

## How to Use

### Accessing the Template Library

1. Open any page in Elementor editor
2. Look for the SOFIR Templates button in the Elementor panel (folder icon)
3. Click the button to open the template library

### Browsing Templates

1. **Browse by Category**: Click on tabs (Popups, Cards, Pages, etc.)
2. **Search**: Use the search box to find specific templates
3. **Filter by Tags**: Click on tags to find related templates

### Importing Templates

1. Hover over any template card
2. Click the "Insert" button to import immediately
3. Or click "Preview" to see a larger preview first
4. The template will be added to your current page

### Customizing Templates

After importing:

1. All elements are fully editable in Elementor
2. Change colors, fonts, images, and text
3. Add or remove sections as needed
4. Adjust spacing and layout
5. Save as your own template for reuse

## Template Structure

### Popup Templates

Popup templates include:
- Eye-catching backgrounds (gradients)
- Clear call-to-action buttons
- Countdown timers (promotional popups)
- Form integrations
- Close buttons
- Privacy notices

### Card Templates

Card templates feature:
- Hover effects and animations
- Image/icon sections
- Title and description areas
- Action buttons
- Meta information (dates, authors, etc.)
- Tags and categories

### Page Templates

Full page templates include:
- Hero sections with sliders
- Feature sections
- Service/product showcases
- Testimonials
- Call-to-action sections
- Footer areas

### Single Post Templates

Single post templates include:
- Featured image headers
- Post meta (author, date, category)
- Content area
- Author bio box
- Social sharing buttons
- Related posts section
- Comments section

### Archive Templates

Archive templates feature:
- Header/title sections
- Post grid/masonry layouts
- Sidebar with widgets
- Search functionality
- Category filters
- Pagination

## Design Principles

### Color Schemes

Default gradient: `#667eea` → `#764ba2` (purple gradient)

Other gradients used:
- `#fa709a` → `#fee140` (pink to yellow)
- `#f093fb` → `#f5576c` (purple to red)

### Typography

- **Headings**: Bold, large font sizes (900 weight)
- **Body**: Clean, readable fonts (16-18px)
- **Buttons**: Bold, uppercase, with hover effects

### Spacing

- **Cards**: 20-25px border radius
- **Sections**: 80px vertical padding
- **Elements**: 15-30px gaps

## Technical Details

### File Structure

```
modules/elementor/
├── templates-manager.php          # Main manager class
├── templates/
│   ├── library.php                # Template definitions
│   └── data/                      # JSON template data
│       ├── newsletter-popup.json
│       ├── promo-popup.json
│       ├── post-card-modern.json
│       ├── product-card-premium.json
│       ├── hero-slider-page.json
│       ├── blog-archive-grid.json
│       ├── single-post-modern.json
│       └── ...
assets/
├── css/
│   └── elementor-templates.css    # Template library styles
└── js/
    └── elementor-templates.js     # Template library JavaScript
```

### PHP Class: Templates_Manager

**Location**: `modules/elementor/templates-manager.php`

**Methods**:
- `boot()` - Initialize hooks
- `register_templates()` - Localize template data
- `enqueue_assets()` - Load CSS and JS
- `import_template()` - Handle AJAX import
- `get_all_templates()` - Get template library
- `get_template_data()` - Load template JSON

### JavaScript: SofirTemplateLibrary

**Location**: `assets/js/elementor-templates.js`

**Methods**:
- `init()` - Initialize library
- `openTemplateLibrary()` - Show modal
- `renderTemplates()` - Display template cards
- `insertTemplate()` - Import template via AJAX
- `previewTemplate()` - Show full preview
- `filterTemplates()` - Search functionality

## Customization

### Adding New Templates

1. Create a new JSON file in `modules/elementor/templates/data/`
2. Add template definition to `modules/elementor/templates/library.php`
3. Add preview image to `assets/images/elementor-templates/`

Example template definition:

```php
[
    'id'          => 'my-template',
    'title'       => __( 'My Template', 'sofir' ),
    'description' => __( 'Template description', 'sofir' ),
    'preview'     => SOFIR_PLUGIN_URL . 'assets/images/elementor-templates/my-template.jpg',
    'type'        => 'card',
    'pro'         => false,
    'tags'        => [ 'business', 'creative' ],
]
```

### Modifying Styles

Edit `assets/css/elementor-templates.css`:

- `.sofir-template-modal` - Main modal container
- `.sofir-template-card` - Template card styles
- `.sofir-template-overlay` - Hover overlay
- `.sofir-modal-tabs` - Category tabs

### Changing Colors

Update gradient in `library.php` and CSS:

```css
background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
```

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance

- Templates are lazy-loaded
- Preview images are optimized
- AJAX import for fast loading
- Minimal JavaScript bundle size

## Support

For issues or questions:
- Check template JSON syntax
- Verify Elementor is active
- Clear browser cache
- Test with default WordPress theme

## Inspiration

Design inspired by:
- Slider Revolution template system
- Modern gradient trends
- Professional web design standards
- User-friendly interfaces

## Future Enhancements

Planned features:
- More template categories
- Template packs
- PRO templates
- Template sync with cloud
- Custom template builder
- Export/import templates
- Template ratings
- Community templates

## Changelog

### Version 1.0.0
- Initial release
- 40+ professional templates
- 7 template categories
- Search and filter
- One-click import
- Preview functionality
- Responsive design
- Modern gradient UI

## Credits

- Design System: Inspired by Slider Revolution
- Icons: Font Awesome
- Color Schemes: Modern web design trends
- Layout: Professional template standards
