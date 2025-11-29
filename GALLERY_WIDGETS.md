# SOFIR Gallery Widgets for Elementor

Complete professional gallery solution inspired by Moment theme and Imagely with 20+ stunning display styles.

## 📸 Gallery Widgets Overview

SOFIR includes 4 powerful Elementor widgets for creating stunning photo galleries, albums, slideshows, and carousels:

### 1. Gallery Widget (`sofir-gallery`)
Professional multi-layout gallery with 7 different display styles.

### 2. Slideshow Widget (`sofir-slideshow`)
Classic slideshow presentation with multiple transition effects.

### 3. Filmstrip Gallery Widget (`sofir-filmstrip-gallery`)
Cinematic carousel with filmstrip and side-scroll styles.

### 4. Album Widget (`sofir-album`)
Organize multiple galleries into beautiful album collections.

---

## 🎨 Widget 1: Gallery

**Location:** SOFIR Elements Category  
**Widget Name:** `sofir-gallery`  
**Icon:** Gallery Grid

### Features

#### 7 Gallery Layouts:
1. **Masonry Gallery** - Pinterest-style layout maintaining original proportions
2. **Mosaic Gallery** - Seamless collage without gaps
3. **Tiled Gallery** - Structured grid layout
4. **Thumbnail Grid** - Extra customizable image grid
5. **Film Gallery** - Elegant frames around each image
6. **Blog Style** - Single column vertical layout
7. **Image Browser** - One large image at a time

### Widget Settings

#### Content Tab:
- **Add Images** - Select multiple images from media library
- **Layout** - Choose from 7 layout options
- **Columns** - 1-6 columns (responsive)
- **Gap** - Spacing between images (0-100px)
- **Image Size** - Thumbnail/Medium/Medium Large/Large/Full
- **Enable Lightbox** - Advanced lightbox with fullscreen view
- **Show Caption** - Display image captions
- **Show Title** - Display image titles
- **Lazy Load** - Improve performance with lazy loading

#### Lightbox Settings:
- **Enable Social Sharing** - Share images on social media
- **Enable Fullscreen** - Fullscreen view mode
- **Enable Zoom** - Zoom in/out functionality
- **Enable Autoplay** - Auto-advance through images
- **Autoplay Speed** - Time between slides (ms)
- **Show Image Counter** - Display current position (e.g., "3/10")
- **Enable Download** - Allow image downloads

#### Style Tab:
- **Image Style:**
  - Border Radius - Rounded corners
  - Border - Image borders
  - Box Shadow - Drop shadow effects
  - Hover Effect - Zoom In/Zoom Out/Grayscale/Blur

### Usage Example

```php
// Use in templates
echo do_shortcode('[elementor-template id="123"]');
```

### CSS Classes

```css
.sofir-gallery                    /* Main wrapper */
.sofir-gallery-masonry           /* Masonry layout */
.sofir-gallery-mosaic            /* Mosaic layout */
.sofir-gallery-tiled             /* Tiled layout */
.sofir-gallery-thumbnail         /* Thumbnail grid */
.sofir-gallery-film              /* Film gallery */
.sofir-gallery-blog              /* Blog style */
.sofir-gallery-imagebrowser      /* Image browser */
.sofir-gallery-item              /* Individual item */
.sofir-gallery-overlay           /* Hover overlay */
.sofir-gallery-hover-zoom        /* Zoom hover effect */
```

---

## 🎬 Widget 2: Slideshow

**Location:** SOFIR Elements Category  
**Widget Name:** `sofir-slideshow`  
**Icon:** Slideshow

### Features

- **4 Transition Effects:** Fade, Slide, Zoom, Flip
- **3 Pagination Types:** Dots, Thumbnails, Numbers
- **Autoplay** with configurable speed
- **Navigation Arrows** with custom styling
- **Keyboard Navigation** (Arrow keys)
- **Touch Swipe** support for mobile
- **Pause on Hover** option
- **Loop** continuous playback
- **Captions** with custom styling

### Widget Settings

#### Content Tab:
- **Add Images** - Gallery selector
- **Image Size** - Medium/Medium Large/Large/Full

#### Slideshow Settings:
- **Autoplay** - Auto-advance slides
- **Autoplay Speed** - 1000-10000ms
- **Transition Speed** - 100-2000ms
- **Transition Effect** - Fade/Slide/Zoom/Flip
- **Show Navigation Arrows** - Previous/Next buttons
- **Show Pagination Dots** - Bottom indicators
- **Pagination Type** - Dots/Thumbnails/Numbers
- **Show Captions** - Display image captions
- **Pause on Hover** - Stop autoplay on hover
- **Loop** - Continuous playback
- **Keyboard Navigation** - Arrow key support
- **Touch Swipe** - Mobile touch support

#### Style Tab:
- **Slideshow Style:**
  - Height - 200-1000px or 20-100vh
  - Navigation Color - Arrow colors
  - Navigation Background - Arrow background
  - Pagination Color - Dot colors
  - Pagination Active Color - Active dot color

- **Caption Style:**
  - Text Color
  - Background Color
  - Typography
  - Padding

### CSS Classes

```css
.sofir-slideshow                 /* Main wrapper */
.sofir-slideshow-container       /* Slides container */
.sofir-slideshow-item            /* Individual slide */
.sofir-slideshow-nav             /* Navigation buttons */
.sofir-slideshow-pagination      /* Pagination wrapper */
.sofir-slideshow-caption         /* Caption overlay */
.sofir-slideshow-effect-fade     /* Fade transition */
.sofir-slideshow-effect-slide    /* Slide transition */
.sofir-slideshow-effect-zoom     /* Zoom transition */
.sofir-slideshow-effect-flip     /* Flip transition */
```

---

## 🎞️ Widget 3: Filmstrip Gallery

**Location:** SOFIR Elements Category  
**Widget Name:** `sofir-filmstrip-gallery`  
**Icon:** Carousel

### Features

- **2 Styles:** Filmstrip (with perforations), Side Scroll
- **Cinematic Filmstrip Effect** - Authentic film perforation borders
- **Horizontal Scrolling** - Smooth carousel navigation
- **Responsive** - Different item counts per device
- **Autoplay** with pause on hover
- **Loop** continuous scrolling
- **Lightbox Integration** - Click to enlarge
- **Captions** optional display

### Widget Settings

#### Content Tab:
- **Add Images** - Gallery selector
- **Style** - Filmstrip/Side Scroll
- **Image Size** - Medium/Medium Large/Large/Full

#### Carousel Settings:
- **Items to Show** - 1-10 visible items
- **Items to Scroll** - 1-10 items per navigation
- **Autoplay** - Auto-scroll enabled
- **Autoplay Speed** - Scroll interval (ms)
- **Scroll Speed** - Animation duration (100-2000ms)
- **Show Navigation** - Previous/Next arrows
- **Loop** - Infinite scrolling
- **Pause on Hover** - Stop on mouse hover
- **Enable Lightbox** - Click to view fullscreen
- **Show Captions** - Display captions

#### Responsive:
- **Tablet Items** - Items on tablets (1-8)
- **Mobile Items** - Items on mobile (1-4)

#### Style Tab:
- **Filmstrip Style:**
  - Item Height - 100-800px
  - Item Gap - 0-50px spacing
  - Border Radius
  - Border
  - Filmstrip Effect - Toggle perforation effect
  - Navigation Position - Top/Center/Bottom
  - Navigation Color
  - Navigation Background

### CSS Classes

```css
.sofir-filmstrip-gallery         /* Main wrapper */
.sofir-filmstrip-container       /* Container */
.sofir-filmstrip-track           /* Scrolling track */
.sofir-filmstrip-item            /* Individual item */
.sofir-filmstrip-effect          /* Filmstrip perforation */
.sofir-filmstrip-nav             /* Navigation buttons */
.sofir-filmstrip-caption         /* Caption overlay */
```

---

## 📚 Widget 4: Album

**Location:** SOFIR Elements Category  
**Widget Name:** `sofir-album`  
**Icon:** Photo Library

### Features

- **3 Layouts:** Grid Album, List Album, Masonry
- **Multiple Albums** - Organize galleries into albums
- **Sub-Albums** - Nested album support
- **Cover Images** - Custom or auto cover selection
- **Image Counts** - Display number of photos
- **Descriptions** - Album descriptions
- **Lightbox Integration** - View all album photos
- **Hover Effects** - Lift/Zoom/Fade animations

### Widget Settings

#### Content Tab:
- **Albums** - Repeater field:
  - Album Title - Name of album
  - Description - Album description
  - Images - Gallery of photos
  - Cover Image - Custom cover (optional)

- **Layout** - Grid Album/List Album/Masonry
- **Columns** - 1-4 columns
- **Gap** - Spacing between albums
- **Cover Image Size** - Medium/Medium Large/Large/Full
- **Show Image Count** - Display photo count badge
- **Show Description** - Display descriptions
- **Enable Lightbox** - Click to view photos
- **Enable Sub-Albums** - Allow nested albums

#### Style Tab:
- **Album Style:**
  - Background Color
  - Border
  - Border Radius
  - Box Shadow
  - Padding
  - Hover Effect - Lift/Zoom/Fade

- **Title Style:**
  - Color
  - Typography
  - Spacing

- **Description Style:**
  - Color
  - Typography

- **Image Count Style:**
  - Color
  - Background Color
  - Typography

### CSS Classes

```css
.sofir-album                     /* Main wrapper */
.sofir-album-grid                /* Grid layout */
.sofir-album-list                /* List layout */
.sofir-album-masonry             /* Masonry layout */
.sofir-album-item                /* Album card */
.sofir-album-cover               /* Cover image */
.sofir-album-count               /* Image count badge */
.sofir-album-overlay             /* Hover overlay */
.sofir-album-content             /* Text content */
.sofir-album-title               /* Album title */
.sofir-album-description         /* Description */
.sofir-album-hover-lift          /* Lift hover effect */
.sofir-album-hover-zoom          /* Zoom hover effect */
```

---

## 🎯 Advanced Lightbox

All gallery widgets include an advanced lightbox with the following features:

### Lightbox Features:
- ✅ **Fullscreen View** - Immersive photo viewing
- ✅ **Navigation** - Previous/Next arrows
- ✅ **Keyboard Controls** - Arrow keys, ESC to close
- ✅ **Image Counter** - "3/10" position indicator
- ✅ **Captions** - Show image captions
- ✅ **Zoom** - Pinch to zoom on mobile
- ✅ **Social Sharing** - Share images
- ✅ **Autoplay Slideshow** - Auto-advance images
- ✅ **Download** - Download image option
- ✅ **Deep Linking** - Direct URLs to images
- ✅ **Responsive** - Works on all devices

### Lightbox Keyboard Shortcuts:
- `←` Left Arrow - Previous image
- `→` Right Arrow - Next image
- `ESC` - Close lightbox

### CSS Classes

```css
.sofir-lightbox                  /* Lightbox overlay */
.sofir-lightbox-content          /* Content wrapper */
.sofir-lightbox-image            /* Main image */
.sofir-lightbox-nav              /* Navigation arrows */
.sofir-lightbox-close            /* Close button */
.sofir-lightbox-caption          /* Caption text */
.sofir-lightbox-counter          /* Image counter */
```

---

## 📱 Responsive Design

All gallery widgets are fully responsive:

### Breakpoints:
- **Desktop:** Full columns (1024px+)
- **Tablet:** Reduced columns (768px-1023px)
- **Mobile:** 1-2 columns (below 768px)

### Mobile Features:
- Touch swipe navigation
- Optimized image sizes
- Responsive navigation controls
- Full-width layouts on small screens

---

## 🎨 Customization

### Custom CSS:

```css
/* Custom gallery hover effect */
.sofir-gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* Custom slideshow height */
.sofir-slideshow {
    height: 80vh;
}

/* Custom filmstrip item size */
.sofir-filmstrip-item {
    width: 400px;
    height: 300px;
}

/* Custom album card style */
.sofir-album-item {
    border: 2px solid #0073aa;
    border-radius: 15px;
}
```

### JavaScript Hooks:

```javascript
// Initialize gallery with custom settings
document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.sofir-gallery');
    if (gallery) {
        // Custom initialization
    }
});
```

---

## 🚀 Performance

### Optimization Features:
- ✅ **Lazy Loading** - Load images as needed
- ✅ **Image Optimization** - WordPress image sizes
- ✅ **CSS Grid/Flexbox** - Modern layouts
- ✅ **Minimal JavaScript** - Vanilla JS, no dependencies
- ✅ **Efficient Animations** - Hardware-accelerated transforms

### Best Practices:
1. Use appropriate image sizes (don't use "Full" unless needed)
2. Enable lazy loading for large galleries
3. Optimize images before upload
4. Use WebP format when possible
5. Limit autoplay speed to reasonable intervals

---

## 🛠️ Developer Reference

### Widget Registration:

```php
// widgets/gallery.php
namespace Sofir\Elementor\Widgets;

class Gallery extends BaseWidget {
    public function get_name() {
        return 'sofir-gallery';
    }
    
    public function get_categories() {
        return [ 'sofir' ];
    }
}
```

### Asset Enqueuing:

```php
// modules/elementor/manager.php
public function enqueue_frontend_styles(): void {
    wp_enqueue_style(
        'sofir-gallery',
        SOFIR_PLUGIN_URL . 'assets/css/gallery.css',
        [],
        SOFIR_VERSION
    );
}

public function enqueue_frontend_scripts(): void {
    wp_enqueue_script(
        'sofir-gallery',
        SOFIR_PLUGIN_URL . 'assets/js/gallery.js',
        [],
        SOFIR_VERSION,
        true
    );
}
```

### Widget Files:
- `/modules/elementor/widgets/gallery.php` - Gallery widget
- `/modules/elementor/widgets/slideshow.php` - Slideshow widget
- `/modules/elementor/widgets/filmstrip-gallery.php` - Filmstrip widget
- `/modules/elementor/widgets/album.php` - Album widget
- `/modules/elementor/base-widget.php` - Base widget class
- `/modules/elementor/manager.php` - Widget registration

### Asset Files:
- `/assets/css/gallery.css` - Gallery styles (920 lines)
- `/assets/js/gallery.js` - Gallery functionality (590 lines)

---

## 📖 Usage Examples

### Example 1: Photography Portfolio

```
1. Add Gallery widget to page
2. Select "Masonry Gallery" layout
3. Upload 20-30 images
4. Set columns to 3
5. Enable lightbox with social sharing
6. Choose "Zoom In" hover effect
```

### Example 2: Product Showcase

```
1. Add Slideshow widget
2. Upload product images
3. Set "Fade" transition effect
4. Enable thumbnail pagination
5. Set autoplay to 4000ms
6. Show captions with product names
```

### Example 3: Wedding Photo Album

```
1. Add Album widget
2. Create albums: "Ceremony", "Reception", "Portraits"
3. Upload photos to each album
4. Set "Grid Album" layout with 3 columns
5. Enable lightbox
6. Add descriptions to each album
```

### Example 4: Event Gallery

```
1. Add Filmstrip Gallery widget
2. Select "Filmstrip" style
3. Upload event photos
4. Set items to show: 4
5. Enable filmstrip effect
6. Enable autoplay with pause on hover
```

---

## 🔧 Troubleshooting

### Images Not Loading:
- Check file permissions
- Verify image URLs are correct
- Ensure images are uploaded to media library

### Lightbox Not Working:
- Check for JavaScript conflicts
- Verify gallery.js is enqueued
- Check browser console for errors

### Layout Issues:
- Clear Elementor cache
- Regenerate CSS
- Check for theme CSS conflicts

### Performance Issues:
- Enable lazy loading
- Optimize image sizes
- Reduce autoplay speed
- Limit number of images per page

---

## 📊 Widget Comparison

| Feature | Gallery | Slideshow | Filmstrip | Album |
|---------|---------|-----------|-----------|-------|
| Multiple Layouts | 7 | 4 effects | 2 styles | 3 layouts |
| Lightbox | ✅ | ❌ | ✅ | ✅ |
| Autoplay | ❌ | ✅ | ✅ | ❌ |
| Captions | ✅ | ✅ | ✅ | ✅ |
| Navigation | ❌ | ✅ | ✅ | ❌ |
| Responsive | ✅ | ✅ | ✅ | ✅ |
| Hover Effects | ✅ | ❌ | ❌ | ✅ |
| Sub-Albums | ❌ | ❌ | ❌ | ✅ |
| Touch Swipe | ❌ | ✅ | ❌ | ❌ |

---

## 🎓 Inspiration Sources

These widgets are inspired by industry-leading gallery solutions:

### Moment Theme (Priority Vision)
- Professional photography layouts
- Elegant transitions
- Portfolio-ready designs

### Imagely
- Advanced lightbox features
- 20+ gallery layouts
- Professional photography focus
- Optimized performance

### Key Features Implemented:
- ✅ Masonry Gallery
- ✅ Mosaic Gallery
- ✅ Tiled Gallery
- ✅ Thumbnail Grid
- ✅ Film Gallery
- ✅ Filmstrip Gallery
- ✅ Side Scroll Gallery
- ✅ Slideshow Gallery
- ✅ Blog Style Gallery
- ✅ Image Browser Gallery
- ✅ Grid Album
- ✅ List Album
- ✅ Advanced Lightbox

---

## 📝 Changelog

### Version 1.0.0 (Current)
- ✅ Initial release
- ✅ 4 gallery widgets
- ✅ 7 gallery layouts
- ✅ Advanced lightbox
- ✅ Full responsive design
- ✅ Touch/swipe support
- ✅ Keyboard navigation
- ✅ Performance optimized

---

## 🤝 Support

For issues or questions about gallery widgets:
1. Check troubleshooting section
2. Review usage examples
3. Check browser console for errors
4. Contact SOFIR support

---

**Made with ❤️ by SOFIR Plugin**  
Professional Gallery Solutions for WordPress & Elementor
