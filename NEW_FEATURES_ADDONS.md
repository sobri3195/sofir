# SOFIR Plugin - New Features & Add-ons

## Overview

This document details the newly added features and add-ons to the SOFIR WordPress plugin, including payment gateways, loyalty program, and additional CPT Library templates.

---

## ✅ Already Implemented Features

### 1. Payment Gateway Add-ons

**Location**: SOFIR → Payments

Complete payment gateway integration with admin UI for:

#### Supported Gateways:
- ✅ **Manual Payment** - Bank transfer with payment proof
- ✅ **Duitku** - Indonesian payment gateway
  - Merchant Code configuration
  - API Key integration
  - Webhook support
- ✅ **Xendit** - Modern payment platform
  - API Key configuration
  - Invoice creation
  - Webhook callbacks
- ✅ **Midtrans** - Popular Indonesian gateway
  - Server Key & Client Key
  - Sandbox/Production mode
  - Snap API integration

#### Admin Features:
- Toggle switches to enable/disable each gateway
- API key configuration forms with help text
- Webhook URLs with one-click copy
- Transaction history table
- Status badges (completed/pending/failed)
- Complete documentation section

#### Shortcode:
```php
[sofir_payment_form amount="100000" item_name="Product Name"]
```

#### REST API:
- `POST /wp-json/sofir/v1/payments/create` - Create payment
- `GET /wp-json/sofir/v1/payments/transactions` - Get transactions
- `POST /wp-json/sofir/v1/payments/webhook/{gateway}` - Webhook handlers

#### Hooks:
- `sofir/payment/status_changed` - Triggered when payment status changes
- `sofir/payment/{gateway}_webhook` - Gateway-specific webhook events

---

### 2. Loyalty Program

**Location**: SOFIR → Users (Loyalty settings)

Complete points-based rewards system with:

#### Features:
- ✅ Points for user actions (signup, login, comment, post, purchase)
- ✅ Configurable point values for each action
- ✅ Points per currency for purchases
- ✅ Reward catalog with redemption
- ✅ User point history tracking
- ✅ REST API for frontend integration

#### Shortcodes:
```php
[sofir_loyalty_points] - Display user's current points
[sofir_loyalty_rewards] - Show available rewards catalog
```

#### REST API:
- `GET /wp-json/sofir/v1/loyalty/points/{user_id}` - Get user points
- `GET /wp-json/sofir/v1/loyalty/history/{user_id}` - Get point history
- `POST /wp-json/sofir/v1/loyalty/redeem` - Redeem reward
- `GET /wp-json/sofir/v1/loyalty/rewards` - Get all rewards

#### Hooks:
- `sofir/loyalty/points_added` - When points are awarded
- `sofir/loyalty/points_deducted` - When points are used
- `sofir/loyalty/reward_redeemed` - When user redeems reward

#### Default Rewards:
- 10% Discount Coupon (500 points)
- 20% Discount Coupon (1000 points)
- Free Shipping (750 points)

---

### 3. Events & Appointments

**Already implemented** with full functionality:

- ✅ Event CPT with date, capacity, location, gallery
- ✅ Appointment CPT with datetime, duration, status, provider/client
- ✅ AJAX booking form
- ✅ Status tracking (pending, confirmed, cancelled, completed)
- ✅ Filter by date, status, provider

---

### 4. Multi-Vendor Marketplace

**Already implemented** with:

- ✅ Vendor Store CPT
- ✅ Vendor Product CPT
- ✅ Commission calculation
- ✅ Earnings tracking
- ✅ Single page templates
- ✅ Version-based rewrite system

---

## 🆕 New Add-ons Modules

### 5. Restaurant Orders Module

**Location**: `modules/restaurant/manager.php`

Complete restaurant ordering system for dine-in and delivery:

#### Features:
- Order management (dine-in & delivery)
- Menu item management
- Customer information tracking
- Order status workflow (pending → preparing → ready → completed)
- Table number for dine-in
- Delivery address for delivery orders

#### Shortcodes:
```php
[sofir_restaurant_menu category="appetizers" columns="3"]
[sofir_order_form type="dine_in"]
[sofir_order_form type="delivery"]
```

#### REST API:
- `GET /wp-json/sofir/v1/restaurant/orders` - Get all orders
- `PATCH /wp-json/sofir/v1/restaurant/orders/{id}` - Update order status
- `GET /wp-json/sofir/v1/restaurant/menu` - Get menu items

#### AJAX:
- `sofir_create_order` - Create new order (dine-in or delivery)

#### Hooks:
- `sofir/restaurant/order_created` - When new order is created
- `sofir/restaurant/order_status_changed` - When order status updates

#### Custom Post Types:
- `restaurant_order` - Orders with customer info and items
- `menu_item` - Menu items with price, category, image

---

### 6. E-Course Module

**Location**: `modules/ecourse/manager.php`

Complete e-learning platform with course management:

#### Features:
- Course catalog with pricing
- Lesson modules
- Student enrollment system
- Progress tracking per course
- Completion certificates
- Course ratings and reviews
- Instructor profiles

#### Shortcodes:
```php
[sofir_course_list columns="3" count="12"]
[sofir_course_progress course_id="123"]
[sofir_my_courses]
```

#### REST API:
- `GET /wp-json/sofir/v1/ecourse/courses` - Get all courses
- `GET /wp-json/sofir/v1/ecourse/courses/{id}` - Get single course
- `POST /wp-json/sofir/v1/ecourse/enrollment` - Enroll in course
- `GET /wp-json/sofir/v1/ecourse/progress/{user_id}/{course_id}` - Get progress
- `POST /wp-json/sofir/v1/ecourse/lesson/{id}/complete` - Mark lesson complete

#### AJAX:
- `sofir_enroll_course` - Enroll user in course
- `sofir_complete_lesson` - Mark lesson as completed

#### Hooks:
- `sofir/ecourse/enrolled` - When user enrolls in course
- `sofir/ecourse/lesson_completed` - When lesson is completed

#### Custom Post Types:
- `course` - Courses with price, instructor, duration, level
- `lesson` - Individual lessons within courses

---

## 📚 New CPT Library Templates

**Location**: SOFIR → Library

### 7. Restaurant Orders Template 🍽️

One-click install includes:
- **restaurant_order** CPT - Order management
- **menu_item** CPT - Menu catalog
- **order_status** taxonomy - Order status tracking
- **menu_category** taxonomy - Menu categories
- Sample pages and navigation menu

**Use Case**: Restaurant, café, food delivery service

---

### 8. Car Rental Template 🚗

One-click install includes:
- **vehicle** CPT - Vehicle catalog with specs
- **rental_booking** CPT - Booking management
- **vehicle_type** taxonomy - Car types (sedan, SUV, etc)
- **vehicle_brand** taxonomy - Car brands
- Fields: price, status, location, gallery, contact
- Sample pages and navigation menu

**Use Case**: Car rental service, vehicle leasing

---

### 9. Community & Forum Template 👥

One-click install includes:
- **forum_topic** CPT - Discussion topics
- **member_profile** CPT - Member profiles
- **forum_category** taxonomy - Topic categories
- **forum_tag** taxonomy - Topic tags
- **member_group** taxonomy - User groups
- Sample pages and navigation menu

**Use Case**: Online community, discussion forum, membership site

---

### 10. Doctor Appointments Template ⚕️

One-click install includes:
- **doctor** CPT - Doctor profiles with specialties
- **medical_appointment** CPT - Patient appointments
- **specialty** taxonomy - Medical specialties
- **hospital** taxonomy - Hospital/clinic locations
- **appointment_type** taxonomy - Appointment types
- Fields: schedule, location, hours, rating, contact
- Sample pages and navigation menu

**Use Case**: Medical clinic, hospital, doctor directory

---

### 11. E-Learning & Courses Template 🎓

One-click install includes:
- **course** CPT - Course catalog
- **lesson** CPT - Course lessons
- **course_category** taxonomy - Course categories
- **course_level** taxonomy - Difficulty levels (beginner, intermediate, advanced)
- Fields: price, rating, instructor, duration
- Sample pages and navigation menu

**Use Case**: Online learning platform, training center, educational site

---

## 📊 CPT Library Summary

Total templates now available: **11**

1. 🏢 Business Directory (existing)
2. 🏨 Hotel & Accommodation (existing)
3. 📰 News & Blog (existing)
4. 📅 Events & Calendar (existing)
5. ⏰ Appointments & Booking (existing)
6. 🛒 Toko Online / E-Commerce (existing)
7. 🍽️ **Restaurant Orders (NEW)**
8. 🚗 **Car Rental (NEW)**
9. 👥 **Community & Forum (NEW)**
10. ⚕️ **Doctor Appointments (NEW)**
11. 🎓 **E-Learning & Courses (NEW)**

---

## 🎯 Use Cases

### Restaurant & Food Service
- Restaurant dine-in orders
- Food delivery service
- Catering business
- Cloud kitchen

### Rental Business
- Car rental service
- Equipment rental
- Property rental
- Bike/scooter rental

### Community & Social
- Online forum
- Community website
- Membership site
- Social network

### Healthcare
- Doctor appointment system
- Medical clinic website
- Telemedicine platform
- Health services directory

### Education
- Online course platform
- Training center
- Educational institution
- Certification programs

---

## 🔧 Technical Implementation

### Module Loading
All modules are automatically loaded via `includes/sofir-loader.php`:

```php
use Sofir\Restaurant\Manager as RestaurantManager;
use Sofir\Ecourse\Manager as EcourseManager;

// In discover_modules():
RestaurantManager::class,
EcourseManager::class,
```

### Assets
- JavaScript: `assets/js/restaurant.js`, `assets/js/ecourse.js`
- CSS: `assets/css/restaurant.css`, `assets/css/ecourse.css`
- Enqueued conditionally when shortcodes are used

### Database
- Orders stored as custom post type
- Customer data in post meta
- Progress tracking in user meta
- Enrollment data in user meta

---

## 📖 Developer Reference

### Filter Hooks
```php
// Modify payment gateways
add_filter('sofir/payment/gateways', function($gateways) {
    return $gateways;
});

// Modify loyalty rewards
add_filter('sofir/loyalty/rewards', function($rewards) {
    return $rewards;
});
```

### Action Hooks
```php
// After order created
add_action('sofir/restaurant/order_created', function($order_id, $type) {
    // Custom logic
}, 10, 2);

// After course enrollment
add_action('sofir/ecourse/enrolled', function($user_id, $course_id) {
    // Custom logic
}, 10, 2);

// After lesson completed
add_action('sofir/ecourse/lesson_completed', function($user_id, $course_id, $lesson_id) {
    // Custom logic
}, 10, 3);
```

---

## 🚀 Getting Started

### Installing Payment Gateways
1. Go to **SOFIR → Payments**
2. Toggle enable for desired gateway
3. Enter API credentials
4. Copy webhook URL to gateway dashboard
5. Test with a transaction

### Installing Loyalty Program
1. Go to **SOFIR → Users**
2. Enable loyalty program
3. Configure point values
4. Add custom rewards if needed
5. Use shortcodes on pages

### Installing CPT Templates
1. Go to **SOFIR → Library**
2. Choose a template
3. Click "View Demo" to see live preview
4. Click "Install Sekarang" button
5. Refresh permalinks at Settings → Permalinks
6. Start creating content

---

## ✨ Summary of Additions

**Payment Gateways**: 3 Indonesian gateways + Manual = 4 total
**Loyalty Program**: Complete points & rewards system
**Restaurant Module**: Dine-in & delivery orders
**E-Course Module**: Complete learning platform
**New Templates**: 5 additional ready-to-use templates

**Total CPT Library Templates**: 11 (6 existing + 5 new)

All features are production-ready with:
- ✅ Complete documentation
- ✅ REST API endpoints
- ✅ Shortcode support
- ✅ Admin UI
- ✅ Webhook integration
- ✅ Event hooks for extensibility
- ✅ Responsive design
- ✅ Security best practices

---

## 📝 Notes

- All payment webhooks are secured and validated
- Loyalty points are stored in user meta for performance
- Course progress is tracked per user per course
- Restaurant orders support both guest and registered users
- All modules follow WordPress coding standards
- Full support for multisite installations
- Compatible with all major themes and page builders

---

## 🔗 Related Documentation

- `LOYALTY_PROGRAM_DOCUMENTATION.md` - Loyalty system details
- `CPT_READY_LIBRARY_GUIDE_ID.md` - CPT Library Indonesian guide
- `CPT_READY_LIBRARY_GUIDE_EN.md` - CPT Library English guide
- `PAYMENT_ADMIN_UI.md` - Payment gateway setup guide
- `MULTI_SITE_READY_LIBRARY.md` - Multi-site deployment guide
