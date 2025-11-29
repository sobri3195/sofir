# Elementor Template Preview Images

This directory contains preview images for all SOFIR Elementor templates.

## Image Requirements

- **Format**: JPG or PNG
- **Size**: 800x600px (4:3 aspect ratio)
- **Quality**: High quality, optimized for web
- **Naming**: Use template ID as filename (e.g., `newsletter-popup.jpg`)

## Template Image List

### Popups
- `newsletter-popup.jpg` - Newsletter subscription popup
- `promo-popup.jpg` - Promotional offer popup
- `exit-intent-popup.jpg` - Exit intent popup
- `video-popup.jpg` - Video presentation popup
- `announcement-popup.jpg` - Announcement popup
- `contact-popup.jpg` - Contact form popup

### Cards
- `post-card-modern.jpg` - Modern post card
- `product-card-premium.jpg` - Premium product card
- `team-member-card.jpg` - Team member card
- `service-card-minimal.jpg` - Minimal service card
- `pricing-card-creative.jpg` - Creative pricing card
- `testimonial-card.jpg` - Testimonial card
- `event-card.jpg` - Event card
- `portfolio-card.jpg` - Portfolio card

### Pages
- `hero-slider-page.jpg` - Hero slider landing page
- `about-company-page.jpg` - About company page
- `services-showcase.jpg` - Services showcase page
- `contact-page-modern.jpg` - Modern contact page
- `portfolio-gallery-page.jpg` - Portfolio gallery page
- `pricing-plans-page.jpg` - Pricing plans page
- `faq-page.jpg` - FAQ page
- `coming-soon-page.jpg` - Coming soon page

### Single Templates
- `single-post-modern.jpg` - Modern single post
- `single-post-magazine.jpg` - Magazine style post
- `single-product-layout.jpg` - Product single layout
- `single-portfolio-item.jpg` - Portfolio item

### Archive Templates
- `blog-archive-grid.jpg` - Blog archive grid
- `blog-archive-masonry.jpg` - Blog archive masonry
- `shop-archive.jpg` - Shop archive
- `portfolio-archive.jpg` - Portfolio archive
- `listing-archive.jpg` - Listing archive

### Headers
- `header-transparent.jpg` - Transparent header
- `header-with-topbar.jpg` - Header with top bar
- `header-centered.jpg` - Centered header

### Footers
- `footer-4-columns.jpg` - Footer 4 columns
- `footer-minimal.jpg` - Footer minimal
- `footer-cta.jpg` - Footer with CTA

## Creating Preview Images

### Method 1: Screenshots
1. Build the template in Elementor
2. Take a full-page screenshot
3. Resize to 800x600px
4. Optimize for web

### Method 2: Design Tools
1. Use Figma/Photoshop/Sketch
2. Create mockup at 800x600px
3. Export as JPG (quality 85%)
4. Save with template ID as filename

### Method 3: Placeholder Service
For development, use placeholder services:
- https://placehold.co/800x600/667eea/ffffff?text=Template+Name
- Replace with real screenshots in production

## Optimization

Optimize all images before adding:

```bash
# Using ImageMagick
convert input.jpg -resize 800x600 -quality 85 output.jpg

# Using TinyPNG API or online tool
# https://tinypng.com/
```

## Adding New Templates

When adding a new template:

1. Create the template JSON in `modules/elementor/templates/data/`
2. Add preview image to this directory
3. Update the template definition in `library.php` with correct image path
4. Update this README with the new template

## Notes

- All preview images should have consistent styling
- Use actual template content, not generic placeholders
- Ensure images are web-optimized (< 200KB each)
- Use descriptive filenames matching template IDs
