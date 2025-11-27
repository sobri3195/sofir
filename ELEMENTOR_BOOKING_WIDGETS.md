# SOFIR Elementor Booking & Reservation Widgets

## Overview

SOFIR Plugin now includes **12 new Elementor widgets** for booking, reservations, events, restaurant orders, marketplace, and e-learning systems. These widgets are organized into **3 new categories** for better organization.

Total Elementor Widgets: **38 widgets** (previously 26)

## Widget Categories

### 1. SOFIR Booking & Events (7 widgets)
- Event List
- Event Calendar
- Event Registration Form
- Booking Form (Universal)
- Restaurant Menu
- Restaurant Order Form (Dine-in)
- Restaurant Delivery Form

### 2. SOFIR E-Commerce (16 widgets)
#### Existing:
- WooCommerce (5): Products, Cart, Checkout, Categories, Account
- EDD (5): Products, Cart, Checkout, Download Button, Categories
- North Commerce (4): Products, Cart, Checkout, Categories

#### New:
- Vendor Products
- Vendor Store List

### 3. SOFIR E-Learning (3 widgets)
- Course List
- Course Progress
- My Courses

### 4. SOFIR Elements (12 widgets - existing)
- Post Feed
- Term Feed
- Search Form
- Map
- Contact Info
- Review Stats
- Visit Chart
- Ring Chart
- Countdown
- Create Post
- Dynamic Data
- Appointment Form

---

## Widget Documentation

### 1. Event List

**Category:** SOFIR Booking & Events  
**Icon:** Calendar  
**Purpose:** Display a list/grid of events

#### Controls:
- **Events Per Page** - Number of events to display (1-100)
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Gap** - Space between items (0-100px)
- **Show Date** - Toggle event date display
- **Show Location** - Toggle location display
- **Show Capacity** - Toggle capacity/available spots
- **Show Thumbnail** - Toggle featured image
- **Show Upcoming Only** - Filter to show only upcoming events

#### Usage:
Perfect for event listing pages, homepage event showcases, or event archives.

---

### 2. Event Calendar

**Category:** SOFIR Booking & Events  
**Icon:** Calendar  
**Purpose:** Interactive calendar view of events

#### Controls:
- **Default View** - Month, Week, or Day view
- **Show Navigation** - Enable/disable month navigation
- **Show Event Details** - Display event information on calendar
- **Enable Popup** - Show event details in popup on click

#### Features:
- Interactive calendar with month navigation
- Event markers on calendar dates
- Click to view event details
- Responsive design for mobile

#### Usage:
Great for event pages, conference schedules, or booking calendars.

---

### 3. Event Registration Form

**Category:** SOFIR Booking & Events  
**Icon:** Form  
**Purpose:** Allow users to register for events

#### Controls:
- **Event ID** - Specific event (0 = current post)
- **Show Event Info** - Display event title, date, location
- **Show Capacity** - Display available spots
- **Show Terms & Conditions** - Terms checkbox
- **Button Text** - Customize submit button
- **Success Message** - Custom confirmation message

#### Form Fields:
- Full Name (required)
- Email (required)
- Phone
- Notes/Special Requests
- Terms & Conditions checkbox

#### Usage:
Embed on event single pages or standalone registration pages.

---

### 4. Booking Form

**Category:** SOFIR Booking & Events  
**Icon:** Form  
**Purpose:** Universal booking form for any post type

#### Controls:
- **Booking Type** - Select any post type
- **Item ID** - Specific item (0 = current post)
- **Show Calendar** - Date picker
- **Show Time Slots** - Time selection dropdown
- **Show Notes Field** - Special requests textarea
- **Require Payment** - Payment requirement notice
- **Button Text** - Customize submit button

#### Form Fields:
- Full Name (required)
- Email (required)
- Phone (required)
- Date selection
- Time slots (9:00 AM - 5:30 PM in 30-min intervals)
- Number of Guests
- Special Requests

#### Usage:
Versatile booking form for appointments, hotel reservations, car rentals, or any bookable items.

---

### 5. Restaurant Menu

**Category:** SOFIR Booking & Events  
**Icon:** Product Categories  
**Purpose:** Display restaurant menu items

#### Controls:
- **Items Per Page** - Number of menu items (1-100)
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Gap** - Space between items
- **Show Category Filter** - Menu category tabs/filters
- **Show Price** - Display item prices
- **Show Description** - Display item descriptions
- **Show Image** - Display item photos
- **Show Add to Cart** - Add to cart/order button

#### Usage:
Perfect for restaurant websites, cafe menus, or food delivery sites.

---

### 6. Restaurant Order Form (Dine-in)

**Category:** SOFIR Booking & Events  
**Icon:** Form  
**Purpose:** Create dine-in restaurant orders

#### Controls:
- **Show Menu Selection** - Display menu items with checkboxes
- **Show Table Number** - Table number field
- **Show Special Requests** - Special requests textarea
- **Button Text** - Customize submit button

#### Form Fields:
- Customer Name (required)
- Table Number (required)
- Menu Items Selection (multiple with quantities)
- Special Requests
- Order Summary with Total Price

#### Features:
- Real-time order summary calculation
- Multiple menu item selection
- Quantity control per item
- Total price calculation

#### Usage:
For restaurant table ordering systems or QR code ordering.

---

### 7. Restaurant Delivery Form

**Category:** SOFIR Booking & Events  
**Icon:** Form  
**Purpose:** Create delivery orders

#### Controls:
- **Show Menu Selection** - Display menu items
- **Show Delivery Time** - Preferred delivery time picker
- **Button Text** - Customize submit button

#### Form Fields:
- Customer Name (required)
- Phone (required)
- Email
- Delivery Address (required)
- Preferred Delivery Time (ASAP or scheduled)
- Menu Items Selection
- Special Requests/Notes
- Order Summary with Delivery Fee

#### Features:
- Delivery fee calculation
- Time slot selection (10:00 AM - 9:30 PM)
- ASAP delivery option
- Real-time total calculation

#### Usage:
Food delivery websites, restaurant delivery systems, or online ordering pages.

---

### 8. Vendor Products

**Category:** SOFIR E-Commerce  
**Icon:** Products  
**Purpose:** Display products from vendor marketplace

#### Controls:
- **Vendor Store ID** - Filter by specific vendor (0 = all)
- **Products Per Page** - Number of products (1-100)
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Show Price** - Display product prices
- **Show Vendor Info** - Display vendor/store name
- **Show Rating** - Display product ratings
- **Show Add to Cart** - Add to cart button

#### Usage:
Multi-vendor marketplaces, vendor profile pages, or product listings.

---

### 9. Vendor Store List

**Category:** SOFIR E-Commerce  
**Icon:** Sitemap  
**Purpose:** Display list of vendor stores

#### Controls:
- **Stores Per Page** - Number of stores (1-100)
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Show Store Logo** - Display vendor logo
- **Show Description** - Store description
- **Show Product Count** - Number of products
- **Show Rating** - Store rating
- **Show Location** - Store location

#### Usage:
Marketplace homepage, vendor directory, or store finder pages.

---

### 10. Course List

**Category:** SOFIR E-Learning  
**Icon:** Archive Posts  
**Purpose:** Display list of courses

#### Controls:
- **Courses Per Page** - Number of courses (1-100)
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Show Price** - Course price
- **Show Instructor** - Instructor name
- **Show Duration** - Course duration
- **Show Lessons Count** - Number of lessons
- **Show Rating** - Course rating
- **Show Enroll Button** - Enrollment button

#### Usage:
Course catalog pages, learning platform homepage, or course categories.

---

### 11. Course Progress

**Category:** SOFIR E-Learning  
**Icon:** Skill Bar  
**Purpose:** Display student progress in a course

#### Controls:
- **Course ID** - Specific course (0 = current post)
- **Show Percentage** - Display completion percentage
- **Show Lesson List** - List of lessons with status
- **Show Completion Status** - Completed/in-progress indicators
- **Progress Bar Color** - Customize progress bar color

#### Features:
- Visual progress bar
- Lesson-by-lesson completion tracking
- Percentage calculation
- Completion status badges

#### Usage:
Student dashboard, course single pages, or learning progress pages.

---

### 12. My Courses

**Category:** SOFIR E-Learning  
**Icon:** My Account  
**Purpose:** Display user's enrolled courses

#### Controls:
- **Layout** - Grid or List
- **Columns** - 1, 2, 3, 4, or 6 columns
- **Show Progress** - Display progress bar per course
- **Show Continue Button** - Continue learning button
- **Show Certificate Link** - Download certificate link
- **Filter by Status** - All, In Progress, or Completed

#### Features:
- User-specific course list
- Progress tracking per course
- Continue learning functionality
- Certificate download (for completed)

#### Usage:
Student dashboard, user profile pages, or my-account learning section.

---

## Implementation

All widgets are registered in `/modules/elementor/manager.php` and organized into categories:

```php
$widget_files = [
    // Existing widgets...
    'event-list',
    'event-calendar',
    'event-registration',
    'booking-form',
    'restaurant-menu',
    'restaurant-order-form',
    'restaurant-delivery-form',
    'vendor-products',
    'vendor-store-list',
    'course-list',
    'course-progress',
    'my-courses',
];
```

## Widget Categories Registration

```php
// SOFIR Booking & Events
$elements_manager->add_category( 'sofir-booking', [
    'title' => 'SOFIR Booking & Events',
    'icon' => 'fa fa-calendar',
] );

// SOFIR E-Learning
$elements_manager->add_category( 'sofir-learning', [
    'title' => 'SOFIR E-Learning',
    'icon' => 'fa fa-graduation-cap',
] );
```

## Widget Files Location

All widget files are located in:
```
/modules/elementor/widgets/
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

## Best Practices

1. **Forms** - All forms include CSRF protection via `wp_nonce_field()`
2. **Responsive** - All widgets include responsive column controls
3. **Styling** - Widgets inherit from `BaseWidget` with standard style controls
4. **Integration** - Widgets integrate with existing SOFIR modules (Events, Restaurant, Multi-Vendor, E-Course)
5. **Flexibility** - All display options are toggleable via widget controls
6. **User Experience** - Real-time calculations, validation, and feedback messages

## Frontend Requirements

Widgets may require additional JavaScript and CSS for interactive features:
- Calendar navigation (`assets/js/calendar.js`)
- Form submission handling (`assets/js/forms.js`)
- Order calculations (`assets/js/restaurant.js`)
- Progress tracking (`assets/js/ecourse.js`)

## Backend Integration

Widgets integrate with existing SOFIR REST API endpoints:
- Events: `/sofir/v1/events/`
- Restaurant: `/sofir/v1/restaurant/`
- Multi-Vendor: `/sofir/v1/vendors/`
- E-Course: `/sofir/v1/ecourse/`

---

## Summary

✅ **12 new Elementor widgets** created  
✅ **3 new categories** added (Booking & Events, E-Learning)  
✅ **Complete form solutions** for booking, events, restaurant ordering  
✅ **Marketplace widgets** for vendor products and stores  
✅ **E-learning widgets** for courses and progress tracking  
✅ **Total 38 widgets** now available in SOFIR Elementor integration

All widgets follow SOFIR coding standards and integrate seamlessly with existing modules.
