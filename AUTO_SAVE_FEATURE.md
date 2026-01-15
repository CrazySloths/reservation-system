# Auto-Save Feature Documentation

## Overview
The booking system now automatically saves form data to the browser's localStorage, preventing data loss from page refreshes, internet disconnections, or accidental navigation.

## How It Works

### Step 1: Date & Time Selection
**Saved Data:**
- Facility selection
- Booking date
- Start time
- End time
- Event purpose
- Expected attendees

**Storage Key:** `booking_step1_data`

### Step 2: Equipment Selection
**Saved Data:**
- Selected equipment items
- Quantity for each item

**Storage Key:** `booking_step2_data`

### Step 3: Review & Submit
**Saved Data:**
- Valid ID type selection
- Special requests/notes

**Note:** File uploads (ID images) cannot be saved for security reasons. Users will need to re-upload files if they refresh the page.

**Storage Key:** `booking_step3_data`

## User Experience

### Automatic Saving
- Data is saved **instantly** as the user types or selects options
- No manual "Save Draft" button needed
- Works completely in the background

### Automatic Restoration
- When users return to any booking step, their data is **automatically restored**
- A green notification appears at the bottom-right corner:
  - "Form Data Restored"
  - Shows for 8 seconds
  - Can be dismissed by clicking the X
  - Includes a "Start Fresh" button

### Data Clearing

**Automatic Clearing:**
- All saved data is **automatically cleared** when the booking is successfully submitted
- Ensures no data persists after completion

**Manual Clearing:**
- Users can click "Start Fresh" in the notification
- Confirmation dialog appears (via SweetAlert2)
- Clears all saved data and redirects to Step 1

## Benefits

### 1. **Internet Connection Issues**
If internet drops and comes back:
- User can simply refresh the page
- All form data is instantly restored
- No need to re-enter information

### 2. **Accidental Navigation**
If user accidentally:
- Clicks the back button
- Closes the tab
- Navigates to another page
- Their progress is saved and can be resumed

### 3. **Multiple Sessions**
- User can start a booking on mobile
- Continue it later on desktop
- Data persists across browser sessions (until cleared or submitted)

### 4. **Peace of Mind**
- Users can take breaks without losing work
- No stress about losing lengthy form inputs
- Professional, modern user experience

## Technical Details

### Storage Method
- Uses browser's `localStorage` API
- Data stored as JSON strings
- No server-side storage (client-side only)
- Persists even after browser restart

### Data Structure

**Step 1 Example:**
```json
{
  "facility_id": "1",
  "booking_date": "2025-11-25",
  "booking_date_display": "Monday, November 25, 2025",
  "start_time": "08:00",
  "start_time_display": "08:00 AM",
  "end_time": "17:00",
  "end_time_display": "05:00 PM",
  "purpose": "Birthday celebration",
  "expected_attendees": "50"
}
```

**Step 2 Example:**
```json
{
  "1": 2,  // Equipment ID 1, Quantity 2
  "3": 1   // Equipment ID 3, Quantity 1
}
```

**Step 3 Example:**
```json
{
  "valid_id_type": "National ID",
  "special_requests": "Need parking space for 10 vehicles"
}
```

### Security Considerations

**What IS Saved:**
- Form field values
- Dropdown selections
- Text inputs
- Number inputs

**What IS NOT Saved:**
- File uploads (ID images)
- Payment information
- Passwords
- Checkbox states (terms acceptance)

**Privacy:**
- Data is stored only on the user's device
- Not transmitted to any server until form submission
- Can be manually cleared at any time
- Automatically clears after successful submission

## Browser Compatibility
Works on all modern browsers that support localStorage:
- Chrome 4+
- Firefox 3.5+
- Safari 4+
- Edge (all versions)
- Opera 10.5+

## Future Enhancements (Optional)

### Potential Additions:
1. **Expiration Timer:** Auto-clear saved data after 7 days
2. **Multiple Drafts:** Save multiple booking drafts with unique IDs
3. **Cloud Sync:** Optional user account integration for cross-device sync
4. **Progress Indicator:** Show saved data timestamp in notification
5. **Export/Import:** Allow users to export/import their draft as a file

## Testing the Feature

### Test Scenario 1: Internet Loss
1. Start filling out booking form (Step 1)
2. Enter facility, date, time, purpose
3. Disconnect from internet
4. Refresh the page (will show offline error)
5. Reconnect to internet
6. Refresh again - **all data should be restored**

### Test Scenario 2: Accidental Back
1. Fill out Step 1 completely
2. Proceed to Step 2
3. Select some equipment
4. Click browser back button
5. Navigate forward again - **Step 2 selections should be restored**

### Test Scenario 3: Multiple Sessions
1. Start booking on computer
2. Fill Step 1 and Step 2
3. Close browser completely
4. Open browser again
5. Go to booking page - **data should still be there**

### Test Scenario 4: Fresh Start
1. Return to any booking step with saved data
2. See the green "Form Data Restored" notification
3. Click "Start Fresh" button
4. Confirm the action
5. **All data should be cleared** and redirected to Step 1

## User Support

### FAQ

**Q: How long is my data saved?**
A: Your booking data is saved in your browser until you either:
- Submit the booking successfully
- Click "Start Fresh" and confirm
- Clear your browser's cache/localStorage
- Complete the booking process

**Q: Can I access my saved data on another device?**
A: No, the data is saved locally on your current device/browser only. For cross-device access, you would need to complete each step before switching devices.

**Q: What happens if I start a new booking?**
A: If you have existing saved data, you'll see a notification. You can either:
- Continue with the saved data
- Click "Start Fresh" to clear it and start over

**Q: Why don't my uploaded ID images get saved?**
A: For security and privacy reasons, file uploads cannot be saved in the browser. You'll need to re-upload your ID images if you refresh the page.

**Q: Is my data secure?**
A: Yes! Your data is stored only on your device's browser, not on any server. It's automatically cleared after successful submission.

