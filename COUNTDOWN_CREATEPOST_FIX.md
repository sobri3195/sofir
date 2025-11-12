# Countdown Timer & Create Post Form Fix

## Issues Fixed

### 1. Countdown Timer - Target Date Not Updating Display
**Problem**: When the target date attribute was changed in the block editor, the countdown timer display did not update to reflect the new target date.

**Root Cause**: 
- The countdown JavaScript only initialized on `DOMContentLoaded`
- When using `ServerSideRender` in the Gutenberg editor, blocks are rendered dynamically after DOMContentLoaded has fired
- When the targetDate attribute changed, the block re-rendered but the countdown JavaScript didn't re-initialize

**Solution** (assets/js/countdown.js):
- ✅ Added MutationObserver to detect dynamically added countdown elements
- ✅ Used WeakMap to track and prevent duplicate interval timers
- ✅ Added validation for target date format
- ✅ Added error handling for invalid dates
- ✅ Properly clean up intervals when countdown expires
- ✅ Exposed global `window.sofirInitCountdowns()` function for manual initialization
- ✅ Auto-detect and initialize countdowns added to the DOM dynamically

### 2. Create Post Form - Title and Content Not Appearing
**Problem**: When users typed title and content in the create post form and clicked submit, the post was not created with the entered data.

**Root Cause**:
- The REST API endpoint path was incorrect (should be 'posts' not 'post')
- No validation for empty title
- Missing proper error handling
- No loading state feedback
- wp.apiFetch might not be available on frontend without proper configuration
- Missing REST API nonce configuration

**Solution** (assets/js/create-post.js):
- ✅ Fixed REST endpoint path (convert 'post' to 'posts')
- ✅ Added validation for empty title
- ✅ Added loading state with disabled button during submission
- ✅ Improved error handling with console logging
- ✅ Added fallback XMLHttpRequest method if wp.apiFetch is unavailable
- ✅ Added MutationObserver for dynamically added forms
- ✅ Prevent duplicate initialization with data attribute flag
- ✅ Exposed global `window.sofirInitCreatePostForms()` function

**Solution** (includes/class-blocks-registrar.php):
- ✅ Added `wpApiSettings` localization with REST URL and nonce
- ✅ Added `setup_api_fetch()` method to configure wp.apiFetch with nonce middleware
- ✅ Ensures authentication works properly for REST API calls

## Technical Details

### Countdown Timer Features
1. **Dynamic Initialization**: Uses MutationObserver to detect new countdown elements
2. **Memory Management**: WeakMap prevents memory leaks from multiple initializations
3. **Error Handling**: Shows error messages for invalid dates
4. **Auto-cleanup**: Stops interval when countdown expires
5. **Null-safe**: Checks for element existence before updating

### Create Post Form Features
1. **Dual API Support**: Uses wp.apiFetch when available, falls back to XMLHttpRequest
2. **User Feedback**: Shows "Creating..." text and disables button during submission
3. **Validation**: Ensures title is not empty before submitting
4. **Error Logging**: Console.error for debugging issues
5. **Success Redirect**: Redirects to new post after creation
6. **Dynamic Forms**: Works with forms added after page load

## Testing

### Test Countdown Timer
1. Add countdown block to page
2. Set target date in block settings
3. Verify countdown updates immediately
4. Change target date
5. Verify countdown updates to new date
6. Set invalid date format
7. Verify error message appears

### Test Create Post Form
1. Add create-post block to page
2. Log in to WordPress
3. Fill in title and content
4. Click Create Post button
5. Verify button shows "Creating..." and is disabled
6. Verify post is created with correct title and content
7. Verify redirect to new post
8. Test with empty title
9. Verify validation message appears

## Files Modified

1. **assets/js/countdown.js** - Enhanced countdown initialization with MutationObserver
2. **assets/js/create-post.js** - Fixed REST API calls and added validation
3. **includes/class-blocks-registrar.php** - Added REST API nonce configuration

## Compatibility

- ✅ Works in Gutenberg editor
- ✅ Works on frontend
- ✅ Works with dynamically loaded content
- ✅ Works with or without wp.apiFetch available
- ✅ Backward compatible with existing implementations
- ✅ No breaking changes

## Browser Support

Both fixes use standard JavaScript features with fallbacks:
- MutationObserver (modern browsers, IE11+)
- WeakMap (modern browsers, IE11+)
- XMLHttpRequest (all browsers)
- Graceful degradation for older browsers
