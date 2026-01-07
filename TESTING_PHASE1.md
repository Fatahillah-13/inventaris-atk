# Phase 1 ATK Request Workflow - Testing Guide

This document provides instructions for manually testing the Phase 1 ATK Request Workflow feature.

## Setup

1. **Run migrations** (if not already done):
   ```bash
   php artisan migrate
   ```

2. **Seed test data**:
   ```bash
   php artisan db:seed --class=AtkPhase1TestSeeder
   ```

   This creates:
   - 3 divisions (IT, HR, Finance)
   - 3 item categories (Alat Tulis, Kertas, Perlengkapan Kantor)
   - 20 requestable ATK items
   - 2 non-requestable equipment items
   - 4 test users with credentials below

## Test Users

| Email | Password | Role | Division |
|-------|----------|------|----------|
| admin@inventaris.test | password | admin | - |
| staff.it@inventaris.test | password | staff_pengelola | IT |
| staff.hr@inventaris.test | password | staff_pengelola | HR |
| staff.finance@inventaris.test | password | staff_pengelola | Finance |

## Test Scenarios

### 1. Access Control Testing

**Test unauthorized access:**
- Logout or open in incognito mode
- Try to access `/permintaan-atk`
- ✓ Should redirect to login

**Test role-based access:**
- Login as `admin@inventaris.test`
- Access `/permintaan-atk`
- ✓ Should see the catalog

### 2. Catalog Browsing

**View catalog:**
- Login as `staff.it@inventaris.test`
- Navigate to "Permintaan ATK" from main navigation
- ✓ Should see 18 requestable items on first page
- ✓ Should see pagination if more than 18 items
- ✓ Should NOT see non-requestable items (Printer, Scanner)

### 3. Add Items to Cart

**Add single item:**
- From catalog, select an item (e.g., "Pulpen Hitam Standard")
- Set quantity to 5
- Click "Tambah ke Keranjang"
- ✓ Should redirect to cart page
- ✓ Should show success message
- ✓ Should see item with quantity 5

**Add multiple items:**
- Go back to catalog
- Add "Kertas A4 80gram" with quantity 3
- Add "Stapler Sedang" with quantity 2
- ✓ Cart should show 3 items total

**Add same item twice:**
- Add "Pulpen Hitam Standard" again with quantity 3
- ✓ Quantity should increase from 5 to 8 (not create duplicate)

### 4. Cart Management

**View cart:**
- Navigate to cart via button or navigation
- ✓ Should see all items added
- ✓ Should see total item count
- ✓ Should see total quantity sum

**Update quantity:**
- Change quantity of an item from 8 to 10
- Click "Update"
- ✓ Should update successfully
- ✓ Should see new quantity

**Remove item:**
- Click "Hapus" on an item
- Confirm deletion
- ✓ Item should be removed from cart
- ✓ Totals should update

**Empty cart:**
- Remove all items
- ✓ Should show "Keranjang Anda kosong" message
- ✓ Should show link back to catalog

### 5. Checkout Process

**Submit request:**
- Add some items to cart
- Click "Ajukan Permintaan"
- Confirm submission
- ✓ Should redirect to "Permintaan Saya" page
- ✓ Should show success message with request number
- ✓ Request number format should be: REQ-YYYYMM-XXXX

**Verify submitted request:**
- Check "Permintaan Saya" page
- ✓ Should see the submitted request
- ✓ Should show correct period (current month/year)
- ✓ Should show "submitted" status
- ✓ Should show correct division
- ✓ Should show submission date/time

### 6. View Requests

**View request list:**
- Navigate to "Permintaan Saya" from catalog buttons
- ✓ Should see all submitted requests for current user
- ✓ Should show request number, period, division, date, item count
- ✓ Should have pagination if more than 20 requests

**View request detail:**
- Click "Lihat Detail" on a request
- ✓ Should show complete request information
- ✓ Should show all items with quantities
- ✓ Should show item details (code, name, unit)
- ✓ Should show totals

### 7. Period-Based Draft

**Test one draft per period:**
- Login as `staff.hr@inventaris.test`
- Add items to cart
- Logout
- Login as `staff.it@inventaris.test`
- Add items to cart (different user)
- ✓ Each user should have their own draft

**Test draft persistence:**
- Add items to cart
- Logout
- Login again as same user
- Go to cart
- ✓ Items should still be there (same period)

### 8. Authorization Testing

**Test data isolation:**
- Login as `staff.it@inventaris.test`
- Submit a request (note the URL)
- Logout
- Login as `staff.hr@inventaris.test`
- Try to access IT staff's request URL directly
- ✓ Should show 403 Forbidden

**Test cart isolation:**
- Add items to cart as one user
- Note the cart item ID from page source or network tab
- Try to update/delete using another user's session
- ✓ Should show 403 Forbidden

### 9. Sequential Request Numbers

**Test numbering:**
- Submit multiple requests from different users
- ✓ Request numbers should be sequential
- ✓ Format: REQ-202601-0001, REQ-202601-0002, etc.
- ✓ Numbers should increment regardless of user

### 10. Navigation and UI

**Test navigation:**
- ✓ "Permintaan ATK" link should appear in main navigation
- ✓ Link should be active when on ATK pages
- ✓ All buttons should work correctly

**Test responsive design:**
- Test on different screen sizes
- ✓ Should be mobile-friendly
- ✓ Cards and tables should adapt

## Expected Behavior Summary

✅ **Access Control:**
- Only authenticated users with `staff_pengelola` or `admin` role can access
- Users cannot view/modify other users' data

✅ **Cart System:**
- One draft per user per period (YYYY-MM)
- Items persist until checkout or period changes
- Adding same item increments quantity

✅ **Checkout:**
- Generates unique sequential request number
- Draft becomes submitted request
- Cannot checkout empty cart

✅ **Request Management:**
- Users can view only their own requests
- Request details show complete information
- Pagination works for large lists

## Notes

- All tests pass (17 tests, 43 assertions)
- Database uses transactions to ensure data integrity
- Race conditions handled with database locking
- Follows existing application styling patterns
