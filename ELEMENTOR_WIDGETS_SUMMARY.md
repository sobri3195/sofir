# SOFIR Elementor Widgets Summary

## Quick Reference

### Total Widgets: 38

| Category | Count | Widgets |
|----------|-------|---------|
| **SOFIR Elements** | 12 | post-feed, term-feed, search-form, map, contact-info, review-stats, visit-chart, ring-chart, countdown, create-post, dynamic-data, appointment-form |
| **SOFIR Booking & Events** | 7 | event-list, event-calendar, event-registration, booking-form, restaurant-menu, restaurant-order-form, restaurant-delivery-form |
| **SOFIR E-Commerce** | 16 | WooCommerce (5), EDD (5), North Commerce (4), vendor-products, vendor-store-list |
| **SOFIR E-Learning** | 3 | course-list, course-progress, my-courses |

## New Widgets (12 Total)

### Events & Booking (4)
1. **event-list** - Display event list/grid
2. **event-calendar** - Interactive event calendar
3. **event-registration** - Event registration form
4. **booking-form** - Universal booking form

### Restaurant (3)
5. **restaurant-menu** - Restaurant menu display
6. **restaurant-order-form** - Dine-in order form
7. **restaurant-delivery-form** - Delivery order form

### Marketplace (2)
8. **vendor-products** - Vendor product listing
9. **vendor-store-list** - Vendor store directory

### E-Learning (3)
10. **course-list** - Course catalog
11. **course-progress** - Student progress tracker
12. **my-courses** - User's enrolled courses

## File Locations

```
/modules/elementor/
├── manager.php          # Widget registration
├── base-widget.php      # Base widget class
└── widgets/             # All widget files
    ├── event-list.php
    ├── event-calendar.php
    ├── event-registration.php
    ├── booking-form.php
    ├── restaurant-menu.php
    ├── restaurant-order-form.php
    ├── restaurant-delivery-form.php
    ├── vendor-products.php
    ├── vendor-store-list.php
    ├── course-list.php
    ├── course-progress.php
    └── my-courses.php
```

## Categories in Elementor

When using Elementor editor, widgets are organized in these categories:

1. **SOFIR Elements** - General purpose widgets
2. **SOFIR Booking & Events** - Event and restaurant booking widgets
3. **SOFIR E-Commerce** - WooCommerce, EDD, North Commerce, and vendor widgets
4. **SOFIR E-Learning** - Course and learning management widgets

## Common Features

All new widgets include:
- ✅ Responsive column controls (1-6 columns)
- ✅ Gap/spacing controls
- ✅ Layout options (Grid/List)
- ✅ Style controls (colors, typography)
- ✅ Show/hide toggles for all elements
- ✅ CSRF protection on forms
- ✅ Integration with SOFIR modules

## Documentation

- **Full English Docs**: `ELEMENTOR_BOOKING_WIDGETS.md`
- **Dokumentasi Indonesia**: `ELEMENTOR_BOOKING_WIDGETS_ID.md`

## Integration

Widgets integrate with:
- ✅ SOFIR Restaurant Module
- ✅ SOFIR E-Course Module
- ✅ SOFIR Multi-Vendor Module
- ✅ SOFIR Events CPT
- ✅ SOFIR Appointments Module
- ✅ WooCommerce (optional)
- ✅ Easy Digital Downloads (optional)
- ✅ North Commerce (optional)

## Usage in Elementor

1. Open Elementor editor
2. Search for "SOFIR" in widgets panel
3. Browse by category
4. Drag widget to page
5. Configure controls
6. Publish!

---

**Version**: 1.0.0  
**Added**: November 2024  
**Total Widgets**: 38 (26 existing + 12 new)
