# SOFIR Loyalty Program - Complete Documentation

## 📌 Table of Contents

1. [Introduction](#introduction)
2. [Key Features](#key-features)
3. [Admin Settings](#admin-settings)
4. [Points System](#points-system)
5. [Rewards & Redemption](#rewards--redemption)
6. [Shortcodes](#shortcodes)
7. [REST API](#rest-api)
8. [Event Hooks](#event-hooks)
9. [Usage Examples](#usage-examples)

---

## Introduction

**SOFIR Loyalty Program** is a fully integrated points-based loyalty system for WordPress. The system rewards users for various activities such as registration, daily login, comments, publishing posts, and purchases.

### Advantages:

✅ **Automatic** - Points are awarded automatically based on user activities  
✅ **Flexible** - Configure points for each activity according to your needs  
✅ **Integrated** - Works with Payment Gateways (Manual, Duitku, Xendit, Midtrans)  
✅ **REST API** - Access point data through REST API  
✅ **Developer-Friendly** - Event hooks for advanced customization

---

## Key Features

### 1. Automatic Points System

| Activity | Default Points | Description |
|----------|---------------|-------------|
| **Sign Up** | 100 points | Awarded when user registers a new account |
| **Daily Login** | 10 points | Awarded once per day on first login |
| **Comment** | 5 points | Awarded when comment is approved |
| **Publish Post** | 20 points | Awarded when publishing a post |
| **Purchase** | 1 point/unit | Based on total purchase amount (configurable) |

### 2. Reward System

Default rewards:

- **10% Discount** - 500 points
- **20% Discount** - 1000 points  
- **Free Shipping** - 750 points

Rewards can be customized according to your business needs.

### 3. User History

Every point transaction is recorded with details:
- Amount of points added/deducted
- Transaction reason
- Date and time
- History stores up to 50 latest entries

---

## Admin Settings

### Accessing Settings

1. Login to WordPress Admin
2. Go to **SOFIR → Users**
3. Scroll down to **Loyalty Program** section

### Points Configuration

In the settings page, you can configure:

#### **1. Enable/Disable Program**
Toggle to activate or deactivate the entire loyalty program.

#### **2. Sign Up Bonus**
Points awarded when a user registers a new account.

**Default:** 100 points  
**Example:** If set to 200, new users will immediately receive 200 points.

#### **3. Daily Login Bonus**
Points awarded for daily login (maximum 1x per day).

**Default:** 10 points  
**Note:** System checks last login date, so only 1 reward per day.

#### **4. Comment Posted**
Points awarded when a comment is approved.

**Default:** 5 points  
**Note:** Spam or pending comments don't earn points.

#### **5. Post Published**
Points awarded when publishing a post/article.

**Default:** 20 points  
**Note:** Applies to all post types.

#### **6. Points per Currency Unit**
Points awarded per currency unit on purchase.

**Default:** 1 point per $1  
**Examples:**
- Purchase $100 = 100 points (if ratio is 1)
- Purchase $100 = 1,000 points (if ratio is 10)

---

## Points System

### 1. Adding Points

Points are awarded automatically through WordPress hooks:

```php
// On user registration
add_action( 'user_register', function( $user_id ) {
    // Automatically receives 100 points (if enabled)
} );

// On user login
add_action( 'wp_login', function( $user_login, $user ) {
    // Receives 10 points (maximum 1x per day)
}, 10, 2 );

// On payment completed
add_action( 'sofir/payment/status_changed', function( $transaction_id, $status ) {
    // Receives points based on total purchase
}, 10, 2 );
```

### 2. Deducting Points

Points are deducted when:
- User redeems a reward
- Admin manual adjustment (via code)

### 3. Point Validation

The system automatically:
- ✅ Checks point balance before redemption
- ✅ Points cannot go negative (minimum 0)
- ✅ Updates history on every transaction

---

## Rewards & Redemption

### Viewing Rewards

Users can view available rewards using:

**Shortcode:**
```
[sofir_loyalty_rewards]
```

**REST API:**
```
GET /wp-json/sofir/v1/loyalty/rewards
```

### Redeeming Rewards

**Via Shortcode:**
"Redeem" button automatically appears next to each reward (if points are sufficient).

**Via REST API:**
```bash
POST /wp-json/sofir/v1/loyalty/redeem
Content-Type: application/json

{
  "reward_id": "discount_10"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Reward redeemed successfully",
  "remaining_points": 250
}
```

### Customizing Rewards

To modify or add rewards, edit the `modules/loyalty/manager.php` file in the `load_rewards()` section:

```php
private function load_rewards(): array {
    return [
        'vip_access' => [
            'id' => 'vip_access',
            'name' => 'VIP Access 1 Month',
            'description' => 'Premium VIP access for 30 days',
            'points_cost' => 2000,
        ],
        // Add more rewards...
    ];
}
```

---

## Shortcodes

### 1. [sofir_loyalty_points]

Displays the points balance of the currently logged-in user.

**Usage:**
```
[sofir_loyalty_points]
```

**Output:**
```html
<div class="sofir-loyalty-points">
  <h3>Your Loyalty Points</h3>
  <div class="sofir-points-balance">1250</div>
  <p class="sofir-points-label">Points</p>
</div>
```

**Note:** If user is not logged in, displays "Please log in to view your points."

### 2. [sofir_loyalty_rewards]

Displays the available rewards catalog.

**Usage:**
```
[sofir_loyalty_rewards]
```

**Output:**
```html
<div class="sofir-loyalty-rewards">
  <h3>Available Rewards</h3>
  <div class="sofir-reward-item can-redeem">
    <h4>10% Discount Coupon</h4>
    <p class="sofir-reward-description">Get 10% off your next purchase</p>
    <div class="sofir-reward-cost">500 points</div>
    <button class="button button-primary sofir-redeem-btn" data-reward-id="discount_10">Redeem</button>
  </div>
  <!-- More rewards... -->
</div>
```

**CSS Classes:**
- `.can-redeem` - User has sufficient points
- `.insufficient-points` - Insufficient points (button disabled)

---

## REST API

### Base URL
```
/wp-json/sofir/v1/loyalty/
```

### 1. Get User Points

**Endpoint:**
```
GET /loyalty/points/{user_id}
```

**Permission:** User must be logged in and have access to that user's data.

**Response:**
```json
{
  "user_id": 5,
  "points": 1250
}
```

### 2. Get User History

**Endpoint:**
```
GET /loyalty/history/{user_id}
```

**Permission:** User must be logged in and have access to that user's data.

**Response:**
```json
[
  {
    "points": 100,
    "reason": "Sign up bonus",
    "date": "2024-01-15 10:30:00"
  },
  {
    "points": 10,
    "reason": "Daily login bonus",
    "date": "2024-01-16 08:15:00"
  },
  {
    "points": -500,
    "reason": "Redeemed: 10% Discount Coupon",
    "date": "2024-01-16 14:20:00"
  }
]
```

### 3. Redeem Reward

**Endpoint:**
```
POST /loyalty/redeem
```

**Permission:** User must be logged in.

**Request Body:**
```json
{
  "reward_id": "discount_10"
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Reward redeemed successfully",
  "remaining_points": 750
}
```

**Response (Error - Insufficient Points):**
```json
{
  "message": "Insufficient points"
}
```

### 4. Get All Rewards

**Endpoint:**
```
GET /loyalty/rewards
```

**Permission:** Public (no login required).

**Response:**
```json
[
  {
    "id": "discount_10",
    "name": "10% Discount Coupon",
    "description": "Get 10% off your next purchase",
    "points_cost": 500
  },
  {
    "id": "discount_20",
    "name": "20% Discount Coupon",
    "description": "Get 20% off your next purchase",
    "points_cost": 1000
  }
]
```

---

## Event Hooks

### Action Hooks

#### 1. sofir/loyalty/points_added

Triggered when points are added.

**Parameters:**
- `$user_id` (int) - User ID
- `$points` (int) - Number of points added
- `$new_total` (int) - Total points after addition
- `$reason` (string) - Reason for adding points

**Example:**
```php
add_action( 'sofir/loyalty/points_added', function( $user_id, $points, $new_total, $reason ) {
    // Send email notification
    if ( $new_total >= 1000 ) {
        wp_mail( 
            get_user_by( 'id', $user_id )->user_email,
            'Congratulations! You reached 1000 points',
            'You can now redeem premium rewards!'
        );
    }
}, 10, 4 );
```

#### 2. sofir/loyalty/points_deducted

Triggered when points are deducted.

**Parameters:**
- `$user_id` (int) - User ID
- `$points` (int) - Number of points deducted
- `$new_total` (int) - Total points after deduction
- `$reason` (string) - Reason for deduction

**Example:**
```php
add_action( 'sofir/loyalty/points_deducted', function( $user_id, $points, $new_total, $reason ) {
    // Log reward redemption activity
    error_log( "User $user_id redeemed $points points: $reason" );
}, 10, 4 );
```

#### 3. sofir/loyalty/reward_redeemed

Triggered when a reward is successfully redeemed.

**Parameters:**
- `$user_id` (int) - User ID
- `$reward_id` (string) - Reward ID
- `$reward` (array) - Complete reward data

**Example:**
```php
add_action( 'sofir/loyalty/reward_redeemed', function( $user_id, $reward_id, $reward ) {
    // Generate discount coupon code
    if ( $reward_id === 'discount_10' ) {
        $coupon_code = 'LOYAL10-' . strtoupper( wp_generate_password( 6, false ) );
        update_user_meta( $user_id, 'pending_coupon', $coupon_code );
        
        // Send email with coupon code
        $user = get_user_by( 'id', $user_id );
        wp_mail(
            $user->user_email,
            'Your Discount Coupon Code',
            "Thank you! Your coupon code: $coupon_code"
        );
    }
}, 10, 3 );
```

---

## Usage Examples

### 1. Display Points in Template

**Single Post:**
```php
<?php
// Display author's points
if ( function_exists( 'Sofir\Loyalty\Manager::instance' ) ) {
    $loyalty = Sofir\Loyalty\Manager::instance();
    $author_id = get_the_author_meta( 'ID' );
    $points = $loyalty->get_user_points( $author_id );
    
    echo '<div class="author-points">';
    echo '<span>Author Points:</span> ' . number_format( $points );
    echo '</div>';
}
?>
```

### 2. Custom Point Award

**Award points for custom activities:**
```php
add_action( 'gform_after_submission', function( $entry, $form ) {
    if ( is_user_logged_in() && function_exists( 'Sofir\Loyalty\Manager::instance' ) ) {
        $loyalty = Sofir\Loyalty\Manager::instance();
        $user_id = get_current_user_id();
        
        // Award 50 points for form submission
        $loyalty->add_points( $user_id, 50, 'Submitted contact form' );
    }
}, 10, 2 );
```

### 3. Points-Based Badges

**Display badges on user profile:**
```php
function sofir_get_user_badge( $user_id ) {
    $loyalty = Sofir\Loyalty\Manager::instance();
    $points = $loyalty->get_user_points( $user_id );
    
    if ( $points >= 10000 ) {
        return '🏆 Platinum Member';
    } elseif ( $points >= 5000 ) {
        return '⭐ Gold Member';
    } elseif ( $points >= 1000 ) {
        return '🥈 Silver Member';
    } else {
        return '🥉 Bronze Member';
    }
}

// Usage
echo sofir_get_user_badge( get_current_user_id() );
```

### 4. Leaderboard Page

**Create a leaderboard for top users:**
```php
function sofir_display_leaderboard() {
    global $wpdb;
    
    $top_users = $wpdb->get_results( "
        SELECT user_id, meta_value as points
        FROM {$wpdb->usermeta}
        WHERE meta_key = 'sofir_loyalty_points'
        ORDER BY CAST(meta_value AS UNSIGNED) DESC
        LIMIT 10
    " );
    
    echo '<div class="sofir-leaderboard">';
    echo '<h2>🏆 Top 10 Members</h2>';
    echo '<ol>';
    
    foreach ( $top_users as $user_data ) {
        $user = get_userdata( $user_data->user_id );
        echo '<li>';
        echo '<strong>' . esc_html( $user->display_name ) . '</strong> - ';
        echo number_format( $user_data->points ) . ' points';
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</div>';
}

// Shortcode
add_shortcode( 'sofir_leaderboard', 'sofir_display_leaderboard' );
```

### 5. AJAX Reward Redemption

**JavaScript for redeeming rewards via AJAX:**
```javascript
jQuery(document).ready(function($) {
    $('.sofir-redeem-btn').on('click', function(e) {
        e.preventDefault();
        
        var rewardId = $(this).data('reward-id');
        var button = $(this);
        
        button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: '/wp-json/sofir/v1/loyalty/redeem',
            method: 'POST',
            contentType: 'application/json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            data: JSON.stringify({ reward_id: rewardId }),
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message);
                button.prop('disabled', false).text('Redeem');
            }
        });
    });
});
```

---

## Tips & Best Practices

### 1. Security

✅ Always validate points before redemption  
✅ Use nonces for form submissions  
✅ Check user capability before manual adjustments

### 2. Performance

✅ History limited to 50 latest entries for efficiency  
✅ Use caching for leaderboard queries  
✅ Query points by meta_key for optimal performance

### 3. User Experience

✅ Display progress bars towards rewards  
✅ Email notifications for point milestones  
✅ Gamification with badges and levels

### 4. Customization

✅ Use filter hooks to modify rewards  
✅ Add custom point events as needed  
✅ Integrate with other plugins (WooCommerce, EDD, etc.)

---

## Troubleshooting

### Points not automatically added

**Solution:**
1. Check if Loyalty Program is enabled in admin
2. Ensure user is logged in
3. Verify activity meets requirements (e.g., login bonus is 1x per day only)

### Cannot redeem rewards

**Solution:**
1. Check user's point balance
2. Ensure reward_id is valid
3. Check permission callback in REST API

### History not showing

**Solution:**
1. History is stored in user meta `sofir_loyalty_history`
2. Maximum 50 latest entries
3. Query with: `get_user_meta( $user_id, 'sofir_loyalty_history', true )`

---

## Support & Documentation

- **Plugin Repository:** [GitHub - SOFIR](https://github.com/sofir/sofir)
- **Complete Documentation:** See `/modules/loyalty/manager.php`
- **REST API:** `/wp-json/sofir/v1/loyalty/`

---

**Version:** 0.1.0  
**Last Updated:** 2024  
**Status:** ✅ Production Ready
