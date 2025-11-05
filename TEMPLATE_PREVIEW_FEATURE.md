# Template Web Preview & Header/Footer Design Templates

## Overview
This document describes the new features added to the SOFIR WordPress plugin for template management.

## Feature 1: Web Preview Demo

### Description
All templates now have a live web preview functionality that allows users to see how the template looks before importing it.

### Implementation Details

#### Backend (PHP)
- **File**: `includes/sofir-importer.php`
- **AJAX Endpoint**: `sofir_preview_template`
- **Method**: `handle_ajax_preview()`
- Renders template blocks using WordPress `do_blocks()` function
- Returns HTML content and template title

#### Frontend (JavaScript)
- **File**: `assets/js/admin.js`
- **Button Class**: `.sofir-template-preview`
- **Trigger Class**: `.sofir-template-preview-trigger` (clickable preview images)
- Opens full-screen modal with iframe
- Loads template content with theme styles
- Responsive design with keyboard navigation (ESC to close)
- Keyboard support for preview images (Enter/Space keys)

#### Styling (CSS)
- **File**: `assets/css/admin.css`
- **Modal Class**: `.sofir-preview-modal`
- **Preview Trigger Class**: `.sofir-template-preview-trigger`
- Full-screen overlay with 90vh height
- Professional header with close button
- Iframe for isolated preview rendering
- Hover effects with eye icon and scale transform
- Smooth transitions and visual feedback

### Usage
1. Navigate to SOFIR Templates admin panel
2. **Click preview image** or click "Preview" button on any template card
3. View live rendered template in modal
4. Press ESC or click close button to exit

### Enhancements
- **Clickable Preview Images**: Template preview images are now clickable
- **Visual Feedback**: Eye icon appears on hover over preview images
- **Keyboard Support**: Press Enter or Space on focused preview image
- **Accessibility**: Full ARIA labels and keyboard navigation
- **Smooth Animations**: Scale and opacity transitions on hover

## Feature 2: Header & Footer Design Templates

### Description
8 ready-to-use header and footer templates have been added for Gutenberg block editor.

### Templates Added

#### Headers (4 designs)
1. **Modern Header** (`modern-header`)
   - Logo + horizontal navigation + CTA button
   - Modern, clean design

2. **Minimal Header** (`minimal-header`)
   - Three-column layout
   - Logo left, nav center, login links right

3. **Business Header** (`business-header`)
   - Top bar with contact info
   - Main navigation with CTA
   - Professional appearance

4. **Centered Header** (`centered-header`)
   - Logo and title centered
   - Navigation below
   - Great for portfolios

#### Footers (4 designs)
1. **Multi Column Footer** (`multi-column-footer`)
   - 4 columns: Company info, menus, links
   - Social media icons
   - Copyright section

2. **Simple Footer** (`simple-footer`)
   - Three columns: Copyright, nav, socials
   - Minimal black background
   - Clean and straightforward

3. **Business Footer** (`business-footer`)
   - Company info with newsletter form
   - Contact details
   - Professional gray background

4. **Newsletter Footer** (`newsletter-footer`)
   - Gradient background (blue to purple)
   - Focus on newsletter signup
   - Eye-catching design

### Implementation Details

#### Template Files
- **Location**: `/templates/components/`
- **Format**: HTML with WordPress block syntax
- **Blocks Used**: 
  - `wp:site-logo`, `wp:site-title`, `wp:navigation`
  - `wp:group`, `wp:columns`, `wp:buttons`
  - `wp:social-links`, `wp:paragraph`
  - `sofir/contact-form` (for newsletter)

#### Preview Images
- **Location**: `/assets/images/templates/`
- **Format**: SVG placeholders
- **Size**: Optimized for template cards

#### Registration
- **File**: `templates/templates.php`
- Added to catalog with `'header'` and `'footer'` categories
- Context: `['pattern']` (copyable patterns, not importable pages)

#### Copy Pattern Feature
- **AJAX Endpoint**: `sofir_copy_pattern`
- **Button Class**: `.sofir-template-copy`
- Copies pattern code to clipboard
- Fallback textarea for browsers without clipboard API
- Success notification with checkmark feedback

### Usage

#### Method 1: From Admin Panel
1. Go to SOFIR Templates admin panel
2. Scroll to "Header Designs" or "Footer Designs" section
3. Click "Copy Pattern" on desired template
4. Pattern code copied to clipboard automatically
5. Paste into Gutenberg editor

#### Method 2: From Gutenberg
1. Open page/post in Gutenberg editor
2. Click (+) to add block
3. Go to "Patterns" tab
4. Find "SOFIR Headers" or "SOFIR Footers" category
5. Click to insert pattern

### Technical Notes

#### Categories Registered
- Block pattern categories added to WordPress:
  - `sofir-header` - "SOFIR Headers"
  - `sofir-footer` - "SOFIR Footers"
- Also added: `sofir-ecommerce`, `sofir-membership`

#### Context Labels
- `'pattern'` → "Block Pattern" badge
- `'page'` → "Page" badge
- `'template'` → "Full Site Editing" badge

#### Browser Compatibility
- Uses modern Clipboard API when available
- Graceful fallback to manual selection
- Works in all major browsers

## Files Modified

### PHP Files
1. `includes/class-templates-manager.php`
   - Added header/footer categories to block pattern registration

2. `includes/class-admin-templates-panel.php`
   - Added header/footer group labels and descriptions
   - Added "Preview" button to all template cards
   - Made preview images clickable with `.sofir-template-preview-trigger` class
   - Added accessibility attributes (role, tabindex, aria-label)
   - Added "Copy Pattern" button for pattern context
   - Added pattern context label

3. `includes/class-admin-manager.php`
   - Added `themeStyleUrl` to localized script data for preview modal

4. `includes/sofir-importer.php`
   - Added `handle_ajax_preview()` method
   - Added `handle_ajax_copy_pattern()` method
   - Added `render_blocks_for_preview()` helper

5. `templates/templates.php`
   - Added 4 header templates
   - Added 4 footer templates

### JavaScript Files
1. `assets/js/admin.js`
   - Preview modal functionality
   - Clickable preview image handler (`handlePreview` function)
   - Click event delegation for buttons and preview images
   - Keyboard event handler for Enter/Space keys
   - Copy pattern with clipboard API
   - Full keyboard navigation support

### CSS Files
1. `assets/css/admin.css`
   - Preview modal styles
   - Copy modal styles
   - Clickable preview image styles (`.sofir-template-preview-trigger`)
   - Hover effects with eye icon overlay
   - Scale and opacity transitions
   - Responsive design

### New Files Created

#### Template HTML Files (8)
- `templates/components/modern-header.html`
- `templates/components/minimal-header.html`
- `templates/components/business-header.html`
- `templates/components/centered-header.html`
- `templates/components/multi-column-footer.html`
- `templates/components/simple-footer.html`
- `templates/components/business-footer.html`
- `templates/components/newsletter-footer.html`

#### Preview SVG Images (8)
- `assets/images/templates/modern-header.svg`
- `assets/images/templates/minimal-header.svg`
- `assets/images/templates/business-header.svg`
- `assets/images/templates/centered-header.svg`
- `assets/images/templates/multi-column-footer.svg`
- `assets/images/templates/simple-footer.svg`
- `assets/images/templates/business-footer.svg`
- `assets/images/templates/newsletter-footer.svg`

## Benefits

### For Users
1. **Preview Before Import**: See exactly how templates look
2. **Intuitive Preview**: Click on template images to preview instantly
3. **Visual Feedback**: Clear hover effects show what's clickable
4. **Ready-to-Use Headers/Footers**: No need to design from scratch
5. **Quick Copy/Paste**: One click to copy pattern code
6. **Gutenberg Integration**: Patterns available directly in editor
7. **Full Accessibility**: Keyboard navigation and screen reader support
8. **Mobile-Friendly**: All features work on mobile devices

### For Developers
1. **Extensible**: Easy to add more templates
2. **Standard WordPress Blocks**: Uses native Gutenberg blocks
3. **Clean Code**: Follows WordPress coding standards
4. **AJAX-Powered**: Modern, no page reloads
5. **Responsive**: Works on all screen sizes

## Future Enhancements

Potential improvements for future versions:
1. Template search and filtering
2. Template categories in sidebar
3. Favorite/bookmark templates
4. Template customization before import
5. More header/footer variations
6. Video tutorials for each template

## Support

For questions or issues:
- Check plugin documentation
- Review code comments in modified files
- Test in browser developer console for JavaScript errors
- Verify PHP error logs for backend issues
