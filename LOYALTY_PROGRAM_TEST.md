# Loyalty Program Testing Guide

## 🧪 Testing Checklist

Use this checklist to verify all loyalty program features are working correctly.

---

## 1. Admin Settings

### Access Test
- [ ] Navigate to WordPress Admin
- [ ] Click SOFIR menu → Users tab
- [ ] Scroll to "Loyalty Program" section
- [ ] Verify form is visible

### Enable/Disable Test
- [ ] Toggle "Enable Loyalty Program" checkbox
- [ ] Click "Save Loyalty Settings"
- [ ] Verify success notice appears: "Loyalty program settings saved."
- [ ] Refresh page and verify toggle state persists

### Point Configuration Test
- [ ] Set Sign Up Bonus to: `200`
- [ ] Set Daily Login Bonus to: `20`
- [ ] Set Comment Posted to: `15`
- [ ] Set Post Published to: `50`
- [ ] Set Points per Currency Unit to: `2`
- [ ] Click "Save Loyalty Settings"
- [ ] Refresh page and verify all values are saved

---

## 2. Automatic Point Awards

### Test User Setup
Create a test user for loyalty testing:
```bash
# Via WP-CLI
wp user create loyaltytest loyalty@test.com --role=subscriber --user_pass=test123

# Or via WordPress Admin
Users → Add New → Fill form → Add New User
```

### Sign Up Points Test
1. **Action:** Register a new user account
2. **Expected:** User receives 100 points (or configured amount)
3. **Verification:**
   ```bash
   wp user meta get {user_id} sofir_loyalty_points
   ```
4. **Result:** [ ] Pass / [ ] Fail

### Daily Login Points Test (Day 1)
1. **Action:** Login with test user
2. **Expected:** User receives 10 points
3. **Check history:**
   ```bash
   wp user meta get {user_id} sofir_loyalty_history
   ```
4. **Result:** [ ] Pass / [ ] Fail

### Daily Login Points Test (Same Day)
1. **Action:** Logout and login again (same day)
2. **Expected:** No additional points awarded
3. **Result:** [ ] Pass / [ ] Fail

### Daily Login Points Test (Next Day)
1. **Action:** Login next day (change server date if testing)
2. **Expected:** Additional 10 points awarded
3. **Result:** [ ] Pass / [ ] Fail

### Comment Points Test
1. **Action:** Post a comment on any post (as logged-in user)
2. **Status:** Ensure comment is approved
3. **Expected:** User receives 5 points
4. **Result:** [ ] Pass / [ ] Fail

### Publish Post Points Test
1. **Action:** Create and publish a new post
2. **Expected:** User receives 20 points
3. **Result:** [ ] Pass / [ ] Fail

### Purchase Points Test
1. **Action:** Complete a payment via SOFIR Payment Gateway
2. **Example:** Payment of Rp 100,000 (ratio 1) = 100 points
3. **Expected:** User receives points based on amount × ratio
4. **Result:** [ ] Pass / [ ] Fail

---

## 3. Shortcodes

### [sofir_loyalty_points] Test

#### Test 1: Logged In User
1. Create a new page/post
2. Add shortcode: `[sofir_loyalty_points]`
3. View page as logged-in user
4. **Expected Output:**
   ```html
   Your Loyalty Points
   [Number]
   Points
   ```
5. **Result:** [ ] Pass / [ ] Fail

#### Test 2: Logged Out User
1. View same page as logged-out user
2. **Expected Output:** "Please log in to view your points."
3. **Result:** [ ] Pass / [ ] Fail

### [sofir_loyalty_rewards] Test

#### Test 1: Basic Display
1. Create a new page/post
2. Add shortcode: `[sofir_loyalty_rewards]`
3. View page
4. **Expected:** List of rewards with:
   - Reward name
   - Description
   - Points cost
   - Redeem button (if logged in)
5. **Result:** [ ] Pass / [ ] Fail

#### Test 2: Sufficient Points
1. Ensure test user has 500+ points
2. View rewards page
3. **Expected:** "Redeem" button is enabled for 10% Discount
4. **Result:** [ ] Pass / [ ] Fail

#### Test 3: Insufficient Points
1. Ensure test user has < 500 points
2. View rewards page
3. **Expected:** Button is disabled with text "Insufficient Points"
4. **Result:** [ ] Pass / [ ] Fail

---

## 4. REST API

### Setup
```bash
# Get user ID
USER_ID=1

# Get nonce (from browser console)
# wp.apiFetch.nonceMiddleware.nonce
NONCE="your_nonce_here"

# Base URL
BASE_URL="https://yoursite.com/wp-json/sofir/v1/loyalty"
```

### Test 1: Get User Points
```bash
curl -X GET "$BASE_URL/points/$USER_ID" \
  -H "X-WP-Nonce: $NONCE" \
  -H "Cookie: wordpress_logged_in_xxx=..."
```

**Expected Response:**
```json
{
  "user_id": 1,
  "points": 1250
}
```
**Result:** [ ] Pass / [ ] Fail

### Test 2: Get User History
```bash
curl -X GET "$BASE_URL/history/$USER_ID" \
  -H "X-WP-Nonce: $NONCE"
```

**Expected Response:**
```json
[
  {
    "points": 100,
    "reason": "Sign up bonus",
    "date": "2024-01-15 10:30:00"
  },
  ...
]
```
**Result:** [ ] Pass / [ ] Fail

### Test 3: Get All Rewards
```bash
curl -X GET "$BASE_URL/rewards"
```

**Expected Response:**
```json
[
  {
    "id": "discount_10",
    "name": "10% Discount Coupon",
    "description": "Get 10% off your next purchase",
    "points_cost": 500
  },
  ...
]
```
**Result:** [ ] Pass / [ ] Fail

### Test 4: Redeem Reward (Success)
```bash
# Ensure user has 500+ points first
curl -X POST "$BASE_URL/redeem" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: $NONCE" \
  -d '{"reward_id":"discount_10"}'
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Reward redeemed successfully",
  "remaining_points": 750
}
```
**Result:** [ ] Pass / [ ] Fail

### Test 5: Redeem Reward (Insufficient Points)
```bash
# Ensure user has < 500 points
curl -X POST "$BASE_URL/redeem" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: $NONCE" \
  -d '{"reward_id":"discount_10"}'
```

**Expected Response:**
```json
{
  "message": "Insufficient points"
}
```
**HTTP Status:** 400  
**Result:** [ ] Pass / [ ] Fail

### Test 6: Redeem Reward (Invalid ID)
```bash
curl -X POST "$BASE_URL/redeem" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: $NONCE" \
  -d '{"reward_id":"invalid_reward"}'
```

**Expected Response:**
```json
{
  "message": "Invalid reward"
}
```
**HTTP Status:** 400  
**Result:** [ ] Pass / [ ] Fail

---

## 5. Event Hooks

### Test Hook: sofir/loyalty/points_added

Add this code to `functions.php` or a custom plugin:

```php
add_action( 'sofir/loyalty/points_added', function( $user_id, $points, $new_total, $reason ) {
    error_log( "LOYALTY TEST: User $user_id gained $points points. Total: $new_total. Reason: $reason" );
}, 10, 4 );
```

**Test:**
1. Perform any point-earning activity
2. Check error log: `tail -f /path/to/wp-content/debug.log`
3. **Expected:** Log entry appears with correct data
4. **Result:** [ ] Pass / [ ] Fail

### Test Hook: sofir/loyalty/points_deducted

Add this code:

```php
add_action( 'sofir/loyalty/points_deducted', function( $user_id, $points, $new_total, $reason ) {
    error_log( "LOYALTY TEST: User $user_id lost $points points. Total: $new_total. Reason: $reason" );
}, 10, 4 );
```

**Test:**
1. Redeem a reward
2. Check error log
3. **Expected:** Log entry appears with correct data
4. **Result:** [ ] Pass / [ ] Fail

### Test Hook: sofir/loyalty/reward_redeemed

Add this code:

```php
add_action( 'sofir/loyalty/reward_redeemed', function( $user_id, $reward_id, $reward ) {
    $user = get_user_by( 'id', $user_id );
    error_log( "LOYALTY TEST: {$user->user_email} redeemed {$reward['name']} ({$reward['points_cost']} points)" );
}, 10, 3 );
```

**Test:**
1. Redeem a reward
2. Check error log
3. **Expected:** Log entry with user email and reward details
4. **Result:** [ ] Pass / [ ] Fail

---

## 6. Integration Tests

### Payment Gateway Integration

#### Setup Test Payment
```php
// Simulate payment completion
$transaction_id = 'TRX-TEST-' . time();
$transactions = [
    $transaction_id => [
        'id' => $transaction_id,
        'gateway' => 'manual',
        'amount' => 100000,
        'item_name' => 'Test Product',
        'status' => 'pending',
        'user_id' => 1,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql'),
    ]
];
update_option('sofir_payment_transactions', $transactions);
```

#### Trigger Payment Webhook
```php
// Change status to completed
do_action('sofir/payment/status_changed', $transaction_id, 'completed');
```

**Expected:**
- User receives points (100000 × ratio)
- History updated
- Hook fires

**Result:** [ ] Pass / [ ] Fail

---

## 7. Edge Cases

### Test 1: Negative Points Prevention
```php
// Try to deduct more points than user has
$loyalty = \Sofir\Loyalty\Manager::instance();
$user_id = 1;
$current_points = $loyalty->get_user_points($user_id);
$loyalty->deduct_points($user_id, $current_points + 100, 'Test deduction');
$new_points = $loyalty->get_user_points($user_id);
```

**Expected:** Points = 0 (not negative)  
**Result:** [ ] Pass / [ ] Fail

### Test 2: History Cap at 50 Entries
```php
// Add 60 point transactions
$loyalty = \Sofir\Loyalty\Manager::instance();
for ($i = 0; $i < 60; $i++) {
    $loyalty->add_points(1, 10, "Test entry $i");
}

// Get history
$history = get_user_meta(1, 'sofir_loyalty_history', true);
echo count($history); // Should be 50
```

**Expected:** History count = 50  
**Result:** [ ] Pass / [ ] Fail

### Test 3: Concurrent Redemption
1. Open two browser tabs
2. Both tabs: User has exactly 500 points
3. Both tabs: Try to redeem 500-point reward simultaneously
4. **Expected:** Only one redemption succeeds
5. **Result:** [ ] Pass / [ ] Fail

### Test 4: Special Characters in Reward Reason
```php
$loyalty = \Sofir\Loyalty\Manager::instance();
$loyalty->add_points(1, 50, "Test <script>alert('xss')</script> reason");
```

**Expected:** Reason stored safely, no XSS when displayed  
**Result:** [ ] Pass / [ ] Fail

---

## 8. Performance Tests

### Test 1: Query Performance
```php
// Measure time to get points for 100 users
$start = microtime(true);
for ($i = 1; $i <= 100; $i++) {
    $loyalty = \Sofir\Loyalty\Manager::instance();
    $points = $loyalty->get_user_points($i);
}
$end = microtime(true);
echo "Time: " . ($end - $start) . " seconds";
```

**Expected:** < 1 second  
**Result:** [ ] Pass / [ ] Fail

### Test 2: History Retrieval Performance
```php
// Measure time to get history for user with 50 entries
$start = microtime(true);
$history = get_user_meta(1, 'sofir_loyalty_history', true);
$end = microtime(true);
echo "Time: " . ($end - $start) . " seconds";
```

**Expected:** < 0.1 seconds  
**Result:** [ ] Pass / [ ] Fail

---

## 9. Compatibility Tests

### Theme Compatibility
- [ ] Test with Twenty Twenty-Three theme
- [ ] Test with Twenty Twenty-Four theme
- [ ] Test with custom theme
- [ ] Verify shortcodes render correctly
- [ ] Verify CSS doesn't conflict

### Plugin Compatibility
- [ ] Test with WooCommerce (if installed)
- [ ] Test with Bit Integration plugin
- [ ] Test with other SOFIR modules
- [ ] Verify no JavaScript conflicts
- [ ] Verify no PHP conflicts

### Browser Compatibility
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browsers

---

## 10. Security Tests

### Authorization Tests
- [ ] Try to access another user's points without permission
- [ ] Try to redeem reward without login
- [ ] Try to modify points via REST API without capability
- [ ] Try to bypass nonce validation

### Sanitization Tests
- [ ] Submit reward_id with SQL injection attempt
- [ ] Submit XSS in custom point reason
- [ ] Submit extremely long strings
- [ ] Submit negative point values

---

## 📊 Test Summary

| Category | Tests | Passed | Failed |
|----------|-------|--------|--------|
| Admin Settings | 3 | [ ] | [ ] |
| Point Awards | 6 | [ ] | [ ] |
| Shortcodes | 5 | [ ] | [ ] |
| REST API | 6 | [ ] | [ ] |
| Event Hooks | 3 | [ ] | [ ] |
| Integration | 1 | [ ] | [ ] |
| Edge Cases | 4 | [ ] | [ ] |
| Performance | 2 | [ ] | [ ] |
| Compatibility | 10 | [ ] | [ ] |
| Security | 8 | [ ] | [ ] |
| **TOTAL** | **48** | **[ ]** | **[ ]** |

---

## 🐛 Bug Report Template

If you find any issues during testing:

```markdown
### Bug Report

**Test:** [Test name]
**Expected:** [What should happen]
**Actual:** [What actually happened]
**Steps to Reproduce:**
1. 
2. 
3. 

**Environment:**
- WordPress version: 
- PHP version: 
- SOFIR version: 
- Theme: 
- Other plugins: 

**Error logs:**
```
[Paste relevant error logs]
```

**Screenshots:**
[Attach if applicable]
```

---

## ✅ Sign-Off

**Tester Name:** ___________________________  
**Date:** ___________________________  
**Status:** [ ] All Tests Passed / [ ] Issues Found  
**Notes:** ___________________________

---

**Version:** 0.1.0  
**Last Updated:** 2024
