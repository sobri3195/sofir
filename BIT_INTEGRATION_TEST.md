# SOFIR Bit Integration - Testing Guide

## Test Checklist

Gunakan checklist ini untuk memverifikasi integrasi SOFIR dengan Bit Integration berfungsi dengan baik.

## Prerequisites

- [x] SOFIR plugin installed and activated
- [x] Bit Integration plugin installed and activated
- [x] WordPress user with Administrator role

## Test 1: Integration Detection

### Steps:
1. Login to WordPress Admin
2. Navigate to **Bit Integration → Create Integration**
3. Click on **Trigger** dropdown

### Expected Result:
- ✅ "SOFIR" appears in the list of available triggers
- ✅ SOFIR icon (purple circle with "S") is displayed
- ✅ Description shows: "SOFIR provides comprehensive triggers..."

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 2: User Registration Trigger

### Steps:
1. Create new integration with:
   - **Trigger:** SOFIR → User Registered
   - **Action:** Webhook → Send Data
   - **Webhook URL:** https://webhook.site/ (get unique URL)
2. Map fields: user_email, user_login, display_name
3. Save and activate integration
4. Register a new test user

### Expected Result:
- ✅ New user created successfully
- ✅ Webhook receives data with correct fields
- ✅ Data matches user information

### Test Data:
```json
{
  "user_id": "123",
  "user_login": "testuser",
  "user_email": "test@example.com",
  "display_name": "Test User",
  "first_name": "Test",
  "last_name": "User"
}
```

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 3: Payment Completed Trigger

### Steps:
1. Create integration:
   - **Trigger:** SOFIR → Payment Completed
   - **Action:** Webhook → Send Data
2. Complete a test payment (use Manual gateway)
3. Check webhook data

### Expected Result:
- ✅ Webhook triggered on payment completion
- ✅ All payment fields present:
  - transaction_id
  - gateway
  - amount
  - item_name
  - user_id
  - status

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 4: Appointment Created Trigger

### Steps:
1. Create integration:
   - **Trigger:** SOFIR → Appointment Created
   - **Action:** Webhook
2. Create a new appointment post
3. Verify webhook receives data

### Expected Result:
- ✅ Trigger fires on appointment creation
- ✅ Appointment data is complete:
  - appointment_id
  - appointment_datetime
  - appointment_duration
  - appointment_status
  - appointment_provider
  - appointment_client

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 5: User Login Trigger

### Steps:
1. Create integration:
   - **Trigger:** SOFIR → User Logged In
   - **Action:** Webhook
2. Logout from WordPress
3. Login again
4. Check webhook

### Expected Result:
- ✅ Webhook receives login event
- ✅ Data includes:
  - user_id
  - user_login
  - user_email
  - login_time

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 6: Post Published Trigger

### Steps:
1. Create integration:
   - **Trigger:** SOFIR → Post Published
   - **Action:** Webhook
2. Create and publish a new post
3. Verify webhook

### Expected Result:
- ✅ Trigger fires on post publish (not draft)
- ✅ Data includes:
  - post_id
  - post_title
  - post_type
  - post_author
  - permalink

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 7: Form Submission Trigger

### Steps:
1. Create integration:
   - **Trigger:** SOFIR → Form Submitted
   - **Action:** Webhook
2. Trigger form submission manually:
   ```php
   do_action( 'sofir/form/submission', 'test_form', [
       'name' => 'John Doe',
       'email' => 'john@example.com',
       'message' => 'Test message'
   ]);
   ```
3. Check webhook

### Expected Result:
- ✅ Webhook receives form data
- ✅ Form ID and data are correct

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 8: Multiple Triggers

### Steps:
1. Create 3 different integrations with different triggers
2. Activate all three
3. Trigger each event
4. Verify all work independently

### Expected Result:
- ✅ All integrations work simultaneously
- ✅ No conflicts between integrations
- ✅ Each receives correct data

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 9: Integration with External Services

### Test with Mailchimp:

**Steps:**
1. Create integration:
   - **Trigger:** SOFIR → User Registered
   - **Action:** Mailchimp → Subscribe
2. Configure Mailchimp API
3. Register test user
4. Check Mailchimp list

### Expected Result:
- ✅ User added to Mailchimp
- ✅ All mapped fields transferred correctly

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

### Test with Google Sheets:

**Steps:**
1. Create integration:
   - **Trigger:** SOFIR → Payment Completed
   - **Action:** Google Sheets → Add Row
2. Configure Google Sheets connection
3. Complete test payment
4. Check spreadsheet

### Expected Result:
- ✅ New row added to sheet
- ✅ Payment data in correct columns

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

### Test with Slack:

**Steps:**
1. Create integration:
   - **Trigger:** SOFIR → Payment Completed
   - **Action:** Slack → Send Message
2. Configure Slack webhook
3. Complete payment
4. Check Slack channel

### Expected Result:
- ✅ Message posted to Slack
- ✅ Payment details in message

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 10: Action Tests

### Test Create User Action:

**Steps:**
1. Create integration:
   - **Trigger:** External (e.g., Google Forms)
   - **Action:** SOFIR → Create User
2. Map fields
3. Trigger external form
4. Check WordPress users

### Expected Result:
- ✅ New WordPress user created
- ✅ All fields populated correctly

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 11: Error Handling

### Test Invalid Data:

**Steps:**
1. Create integration with required fields
2. Send incomplete data
3. Check error logs

### Expected Result:
- ✅ Integration handles error gracefully
- ✅ Error logged in Bit Integration
- ✅ No site crash or fatal error

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 12: Performance Test

### Steps:
1. Create integration
2. Trigger 100 rapid events
3. Monitor server load
4. Check webhook delivery

### Expected Result:
- ✅ All events processed
- ✅ No timeout errors
- ✅ Server remains responsive
- ✅ All webhooks delivered

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 13: Conditional Logic

### Steps:
1. Create integration with conditions:
   - Trigger only if amount > 100000
2. Test with amount = 50000 (should NOT trigger)
3. Test with amount = 150000 (should trigger)

### Expected Result:
- ✅ Condition logic works correctly
- ✅ Only matching events trigger action

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 14: Field Mapping

### Steps:
1. Create integration
2. Map all available fields
3. Test with complete data
4. Test with partial data

### Expected Result:
- ✅ All fields map correctly
- ✅ Empty fields handled properly
- ✅ Special characters preserved

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test 15: Deactivation & Reactivation

### Steps:
1. Create and activate integration
2. Deactivate SOFIR plugin
3. Reactivate SOFIR plugin
4. Test integration again

### Expected Result:
- ✅ Integration still works after reactivation
- ✅ No data loss
- ✅ Configuration preserved

### Pass/Fail:
- [ ] PASS
- [ ] FAIL (Note issue: ________________)

---

## Test Summary

Total Tests: 15

Passed: _____ / 15  
Failed: _____ / 15  
Not Tested: _____ / 15

## Issues Found

1. ______________________________________________________
2. ______________________________________________________
3. ______________________________________________________

## Notes

_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

## Sign-off

Tested by: ___________________  
Date: ___________________  
Version: SOFIR 0.1.0 + Bit Integration _____

## Additional Manual Tests

### Test Webhook Logs

1. Go to Bit Integration → Integration → Logs
2. Verify logs show:
   - Request time
   - Response status
   - Data sent
   - Errors (if any)

### Test with Different WordPress Versions

- [ ] WordPress 6.3
- [ ] WordPress 6.4
- [ ] WordPress 6.5 (latest)

### Test with Different PHP Versions

- [ ] PHP 8.0
- [ ] PHP 8.1
- [ ] PHP 8.2
- [ ] PHP 8.3

### Browser Compatibility

- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

## Automated Testing (Future)

Ideas for automated tests:

```php
// Example PHPUnit test structure
class BitIntegrationTest extends WP_UnitTestCase {
    public function test_triggers_registered() {
        $triggers = apply_filters( 'btcbi_trigger', [] );
        $this->assertArrayHasKey( 'sofir', $triggers );
    }
    
    public function test_user_register_trigger() {
        // Create test user
        // Verify trigger fires
        // Check data structure
    }
}
```

## Resources

- Bit Integration Documentation: https://www.bit-integrations.com/docs
- SOFIR Documentation: See README.md
- Webhook Testing: https://webhook.site
- Request Inspector: https://requestbin.com
