# CPT Ready Library - Test Plan

## 🧪 Test Checklist

### 1. Template Availability Test
**Objective**: Verify all 5 templates are available in Library tab

**Steps**:
1. Login to WordPress Admin
2. Navigate to SOFIR → Library
3. Scroll to "Ready-to-Use CPT Library" section

**Expected Results**:
- [ ] 5 template cards visible
- [ ] 🏢 Business Directory template exists
- [ ] 🏨 Hotel & Accommodation template exists
- [ ] 📰 News & Blog template exists
- [ ] 📅 Events & Calendar template exists
- [ ] ⏰ Appointments & Booking template exists
- [ ] Each card shows icon, badge, name, description, features list
- [ ] Install buttons are active (not disabled)

### 2. Business Directory Installation Test
**Objective**: Install Business Directory template and verify setup

**Steps**:
1. Click "Install Template" on Business Directory card
2. Wait for page refresh
3. Check for success notice
4. Go to WordPress Admin menu
5. Check CPT menu items

**Expected Results**:
- [ ] Success message: "Template 'Business Directory' berhasil diinstall!"
- [ ] "Listings" menu appears in sidebar
- [ ] Click Listings → Check custom fields visible in add new post
- [ ] Custom fields include: location, hours, rating, status, price, contact, gallery, attributes
- [ ] Taxonomies exist: listing_category, listing_location
- [ ] Template card shows "✓ Sudah Terinstall" badge
- [ ] Install button is disabled

### 3. Hotel Template Installation Test
**Objective**: Install Hotel & Accommodation template

**Steps**:
1. Click "Install Template" on Hotel & Accommodation card
2. Verify installation
3. Check CPT and fields

**Expected Results**:
- [ ] Success notice appears
- [ ] "Properties" menu in sidebar (or "Listings" if Business Directory already installed)
- [ ] Fields: location, rating, price, contact, gallery, attributes
- [ ] Taxonomies: listing_category (Property Type), listing_location

### 4. News Blog Installation Test
**Objective**: Install News & Blog template

**Steps**:
1. Click "Install Template" on News & Blog card
2. Verify installation
3. Check CPT and fields

**Expected Results**:
- [ ] Success notice appears
- [ ] "Articles" menu in sidebar
- [ ] Fields: attributes (minimal)
- [ ] Supports: title, editor, thumbnail, excerpt, author, revisions, comments

### 5. Events Calendar Installation Test
**Objective**: Install Events & Calendar template

**Steps**:
1. Click "Install Template" on Events & Calendar card
2. Verify installation
3. Check CPT and fields

**Expected Results**:
- [ ] Success notice appears
- [ ] "Events" menu in sidebar
- [ ] Fields: event_date, event_capacity, location, contact, gallery, status, attributes
- [ ] Taxonomies: event_category, event_tag
- [ ] Filters active: event_after, location, capacity_min, status

### 6. Appointments Installation Test
**Objective**: Install Appointments & Booking template

**Steps**:
1. Click "Install Template" on Appointments & Booking card
2. Verify installation
3. Check CPT and fields

**Expected Results**:
- [ ] Success notice appears
- [ ] "Appointments" menu in sidebar
- [ ] Fields: appointment_datetime, appointment_duration, appointment_status, appointment_provider, appointment_client, contact, attributes
- [ ] Taxonomy: appointment_service
- [ ] Filters: appointment_after, appointment_status, provider_id, client_id

### 7. Export Functionality Test
**Objective**: Export installed CPT to JSON

**Steps**:
1. Go to SOFIR → Library
2. Scroll to "Export CPT Package" section
3. Select one or more installed CPTs (checkbox)
4. Click "Preview Data" button
5. Check preview display
6. Enter filename
7. Click "Download Ekspor"

**Expected Results**:
- [ ] Checkboxes appear for all installed CPTs
- [ ] Preview button becomes active when CPT selected
- [ ] Preview shows: slug, posts count, fields count, taxonomies count
- [ ] Download button becomes active
- [ ] JSON file downloads successfully
- [ ] JSON contains: version, exported timestamp, post_types, taxonomies, posts arrays

### 8. Import Functionality Test
**Objective**: Import CPT package from JSON file

**Steps**:
1. Delete one installed CPT from SOFIR → Content
2. Go to SOFIR → Library
3. Scroll to "Import CPT Package" section
4. Upload previously exported JSON file
5. Click "Import Paket CPT"

**Expected Results**:
- [ ] File upload accepts .json files
- [ ] Success message shows count: "Berhasil import X CPT, Y taxonomies, dan Z posts"
- [ ] CPT is re-registered
- [ ] Fields restored
- [ ] Taxonomies restored
- [ ] Posts imported as drafts

### 9. Permalink Refresh Test
**Objective**: Ensure rewrite rules work after installation

**Steps**:
1. After installing any template
2. Go to Settings → Permalinks
3. Click "Save Changes" (without changing anything)
4. Visit CPT archive page (e.g., /listings/, /events/)
5. Visit single CPT page

**Expected Results**:
- [ ] Archive pages load (no 404)
- [ ] Single pages load (no 404)
- [ ] URLs are SEO-friendly (e.g., /listings/post-name/)

### 10. Multi-Installation Test
**Objective**: Install multiple templates on same site

**Steps**:
1. Install all 5 templates one by one
2. Check for conflicts
3. Verify all menus exist

**Expected Results**:
- [ ] All 5 templates install successfully
- [ ] No errors or conflicts
- [ ] 5 CPT menus in admin sidebar (Listings, Articles, Events, Appointments, Properties or Listings)
- [ ] All fields work correctly
- [ ] No data loss or overwrite

### 11. Status Indicator Test
**Objective**: Verify "Already Installed" status

**Steps**:
1. Install Business Directory
2. Refresh Library tab
3. Check Business Directory card

**Expected Results**:
- [ ] Badge changes to "✓ Sudah Terinstall"
- [ ] Install button is disabled
- [ ] Badge color is green (#00a32a)

### 12. REST API Test
**Objective**: Verify CPT REST endpoints work

**Steps**:
1. Install any template
2. Open browser dev tools
3. Visit: `/wp-json/wp/v2/{cpt_slug}` (e.g., /wp-json/wp/v2/listing)

**Expected Results**:
- [ ] REST endpoint returns 200 OK
- [ ] JSON data includes CPT posts
- [ ] Custom fields visible in response
- [ ] Taxonomies visible in response

### 13. Filter Query Test
**Objective**: Test REST API filters

**Steps**:
1. Install Business Directory template
2. Add a listing with location "Jakarta"
3. Query: `/wp-json/wp/v2/listing?location=Jakarta`

**Expected Results**:
- [ ] Filter query works
- [ ] Returns posts with matching location
- [ ] Empty array if no matches

### 14. Frontend Display Test
**Objective**: Verify CPT displays on frontend

**Steps**:
1. Install any template
2. Add a test post
3. Visit archive page
4. Visit single page

**Expected Results**:
- [ ] Archive page shows list of posts
- [ ] Single page shows post content
- [ ] Custom fields can be displayed via post meta
- [ ] Theme integration works

### 15. Multi-Site Export/Import Test
**Objective**: Clone CPT to another WordPress installation

**Steps**:
1. Site A: Install template and export to JSON
2. Site B: Import the JSON file
3. Verify CPT structure matches

**Expected Results**:
- [ ] JSON exports successfully from Site A
- [ ] JSON imports successfully to Site B
- [ ] CPT structure identical on both sites
- [ ] Fields configuration matches
- [ ] Taxonomies matches
- [ ] Filters matches

---

## 🐛 Known Issues & Workarounds

### Issue 1: 404 After Installation
**Symptom**: CPT pages show 404 Not Found

**Solution**: Go to Settings → Permalinks → Save Changes

### Issue 2: Template Already Installed
**Symptom**: Want to reinstall template

**Solution**: 
1. Delete CPT from SOFIR → Content
2. Return to Library tab
3. Reinstall template

### Issue 3: Import Failed
**Symptom**: Error when importing JSON

**Solution**:
1. Validate JSON syntax at jsonlint.com
2. Check file size limit
3. Check file permissions
4. Review error log

---

## ✅ Test Results Template

### Test Session: [Date]
**Tester**: [Name]
**Environment**: 
- WordPress Version: [x.x.x]
- PHP Version: [x.x.x]
- SOFIR Version: [x.x.x]

### Results Summary

| Test # | Test Name | Status | Notes |
|--------|-----------|--------|-------|
| 1 | Template Availability | [ ] Pass [ ] Fail | |
| 2 | Business Directory Install | [ ] Pass [ ] Fail | |
| 3 | Hotel Template Install | [ ] Pass [ ] Fail | |
| 4 | News Blog Install | [ ] Pass [ ] Fail | |
| 5 | Events Calendar Install | [ ] Pass [ ] Fail | |
| 6 | Appointments Install | [ ] Pass [ ] Fail | |
| 7 | Export Functionality | [ ] Pass [ ] Fail | |
| 8 | Import Functionality | [ ] Pass [ ] Fail | |
| 9 | Permalink Refresh | [ ] Pass [ ] Fail | |
| 10 | Multi-Installation | [ ] Pass [ ] Fail | |
| 11 | Status Indicator | [ ] Pass [ ] Fail | |
| 12 | REST API | [ ] Pass [ ] Fail | |
| 13 | Filter Query | [ ] Pass [ ] Fail | |
| 14 | Frontend Display | [ ] Pass [ ] Fail | |
| 15 | Multi-Site Export/Import | [ ] Pass [ ] Fail | |

### Overall Status
- [ ] All tests passed
- [ ] Some tests failed (see notes)
- [ ] Critical issues found

### Comments:
[Add any additional comments or observations]

---

## 🔄 Regression Testing

After any code changes, re-run these critical tests:

1. **Template Installation** (Tests 2-6)
2. **Export/Import** (Tests 7-8)
3. **Permalink Refresh** (Test 9)

---

## 📝 Test Automation Ideas

Future automation possibilities:

```php
// PHPUnit test example
public function test_business_directory_installation() {
    $library = \Sofir\Admin\LibraryPanel::instance();
    $templates = $library->get_ready_templates();
    
    $this->assertArrayHasKey('business_directory', $templates);
    
    // Simulate installation
    // ... install logic
    
    $manager = \Sofir\Cpt\Manager::instance();
    $post_types = $manager->get_post_types();
    
    $this->assertArrayHasKey('listing', $post_types);
}
```

---

**Last Updated**: [Date]
**Test Plan Version**: 1.0
