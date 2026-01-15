# 🏦 Cashless Payment System Guide

## Overview
The LGU Facilities Reservation System now supports **multiple cashless payment methods** including GCash, Maya, and major Philippine banks!

---

## 🚀 Features

✅ **Multiple Payment Channels:**
- GCash
- Maya (PayMaya)
- BPI
- BDO
- Metrobank
- UnionBank
- Landbank

✅ **Test Mode for Development:**
- Use `TEST-` prefix for reference numbers
- Perfect for thesis defense demonstrations
- No real money needed

✅ **Manual Verification:**
- Treasurer verifies each payment
- Reference number matching
- Fraud prevention

✅ **Future-Ready:**
- PayMongo integration structure ready
- Easy upgrade path when API arrives

---

## 📝 Setup Instructions

### Step 1: Configure Payment Settings

1. Open your `.env` file
2. Copy settings from `PAYMENT_ENV_SETTINGS.txt`
3. Update with your LGU's actual account numbers

```env
# Test Mode (set to false for production)
PAYMENT_TEST_MODE=true

# GCash Configuration
GCASH_ENABLED=true
GCASH_ACCOUNT_NUMBER=09171234567
GCASH_ACCOUNT_NAME="Your LGU Treasurer's Office"

# Maya Configuration  
MAYA_ENABLED=true
MAYA_ACCOUNT_NUMBER=09171234567
MAYA_ACCOUNT_NAME="Your LGU Treasurer's Office"

# Bank Accounts (update with real accounts)
BPI_ENABLED=true
BPI_ACCOUNT_NUMBER="1234-5678-90"
BPI_ACCOUNT_NAME="Your LGU Treasurer's Office"

# ... (repeat for other banks)
```

### Step 2: Clear Configuration Cache

```bash
php artisan config:clear
```

### Step 3: Test the System

1. Create a test booking as a Citizen
2. Go to payment slip
3. Click "Pay Online Now"
4. Select a payment channel
5. Enter a test reference: `TEST-123456789`
6. Submit

---

## 🎮 How to Use

### For Citizens:

1. **View Payment Slip**
   - Navigate to "My Reservations" → Select booking
   - Click "View Payment Slip"

2. **Choose Cashless Payment**
   - Click "Pay Online Now" button
   - Select your preferred payment method (GCash, Maya, BPI, etc.)

3. **Make Payment**
   - Send exact amount to the displayed account
   - Copy your transaction reference number
   - Enter reference number in the form
   - Click "Submit Payment"

4. **Wait for Verification**
   - Treasurer will verify within 24 hours
   - You'll receive an Official Receipt once verified

### For Treasurer:

1. **View Pending Payments**
   - Go to "Payment Verification" menu
   - See list of payments awaiting verification

2. **Verify Cashless Payment**
   - Click on payment slip
   - See payment channel and reference number
   - Open your GCash/Maya/Bank app
   - Search for the reference number
   - Confirm amount matches

3. **Approve Payment**
   - Select payment method from dropdown (pre-filled)
   - Add optional notes
   - Click "Verify & Confirm Payment"
   - Official Receipt auto-generated!

---

## 🧪 Test Mode Usage

### Development & Thesis Defense

When `PAYMENT_TEST_MODE=true`:

**For Testing:**
```
Reference Number: TEST-001
Reference Number: TEST-123456789
Reference Number: TEST-DEMO
```

Any reference starting with `TEST-` will:
- ✅ Be accepted immediately
- ✅ Show "TEST MODE" badge
- ✅ Skip duplicate checks
- ✅ Work without real money

**For Thesis Panel:**
1. Keep test mode ON
2. Use TEST- references during demo
3. Show complete workflow
4. Explain it's for demonstration

---

## 🔄 Workflow Diagram

```
CITIZEN                    TREASURER                 ADMIN
   |                          |                        |
   | 1. Create Booking        |                        |
   |------------------------->|                        |
   |                          |                        |
   |                          | 2. Admin Approves      |
   |                          |<-----------------------|
   |                          |                        |
   | 3. View Payment Slip     |                        |
   |<-------------------------|                        |
   |                          |                        |
   | 4. Pay Online            |                        |
   | (Select Channel)         |                        |
   | Enter Reference #        |                        |
   |------------------------->|                        |
   |                          |                        |
   |                          | 5. Verify Payment      |
   |                          | (Check App/Bank)       |
   |                          |                        |
   |<-------------------------| 6. Generate OR         |
   | Download Receipt         |                        |
   |                          |                        |
   |                          |----------------------->| 7. Admin Confirms Booking
   |                          |                        |
   | ✅ Booking Confirmed     |                        |
   |<--------------------------------------------------|
```

---

## 🛠 Production Deployment

### Before Going Live:

1. **Update .env File:**
   ```env
   PAYMENT_TEST_MODE=false  # ← IMPORTANT!
   ```

2. **Add Real Account Numbers:**
   - GCash: Official LGU GCash number
   - Maya: Official LGU Maya number
   - Banks: Actual bank account numbers

3. **Disable Unused Channels:**
   ```env
   METROBANK_ENABLED=false
   ```

4. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

5. **Train Staff:**
   - Show treasurer how to verify payments
   - Explain reference number matching
   - Practice with TEST mode first

---

## 🔐 Security Features

✅ **Duplicate Prevention:** Can't use same reference twice  
✅ **User Verification:** Only owner can pay their slip  
✅ **Manual Approval:** Treasurer double-checks each payment  
✅ **Audit Trail:** All payments logged with timestamps  
✅ **Test Mode Separation:** Test transactions clearly marked  

---

## 📊 Payment Channels Overview

| Channel | Reference Length | Icon | Use Case |
|---------|-----------------|------|----------|
| GCash | 13 digits | 📱 | Most popular e-wallet |
| Maya | 12 digits | 💳 | Growing e-wallet |
| BPI | 16 digits | 🏦 | Major bank |
| BDO | 15 digits | 🏦 | Largest bank |
| Metrobank | 14 digits | 🏦 | Major bank |
| UnionBank | 12 digits | 🏦 | Digital-friendly bank |
| Landbank | 13 digits | 🏦 | Government bank |

---

## 🚀 Future: PayMongo Integration

Once PayMongo API key arrives:

1. Add to `.env`:
   ```env
   PAYMONGO_ENABLED=true
   PAYMONGO_SECRET_KEY=sk_test_...
   PAYMONGO_PUBLIC_KEY=pk_test_...
   ```

2. Instant verification!
3. No manual treasurer approval needed
4. Webhook automation

---

## ❓ Troubleshooting

### "Reference number already used"
- Check if you entered correct reference
- Each reference can only be used once
- Contact treasurer if payment was made

### "Payment not showing in treasurer queue"
- Ensure you clicked "Submit Payment"
- Check your internet connection
- Refresh the page

### Test Mode Not Working
- Run: `php artisan config:clear`
- Check `.env`: `PAYMENT_TEST_MODE=true`
- Reference must start with `TEST-`

---

## 📞 Support

For questions or issues:
1. Check this guide first
2. Test with TEST mode
3. Review treasurer verification process
4. Check logs: `storage/logs/laravel.log`

---

## 🎓 For Thesis Defense

**Key Points to Highlight:**

1. ✅ **Multi-channel support** - Not just one payment method
2. ✅ **Test mode** - Safe demonstration without real money
3. ✅ **Manual verification** - Treasurer control and security
4. ✅ **Hybrid approach** - Manual now, automated later (PayMongo)
5. ✅ **Production-ready** - Real LGU can use immediately
6. ✅ **Future-proof** - Easy to add more channels

**Demo Flow:**
1. Show cashless payment page with all channels
2. Make test payment with `TEST-` reference
3. Show treasurer verification process
4. Generate Official Receipt
5. Explain test vs production mode

---

## 📝 Configuration Reference

All settings in `config/payment.php`:

- `test_mode` - Enable/disable test transactions
- `paymongo_enabled` - PayMongo integration toggle
- `channels` - Array of payment methods with:
  - `enabled` - Show/hide channel
  - `name` - Display name
  - `account_number` - LGU account
  - `account_name` - Account holder
  - `instructions` - Help text
  - `reference_length` - Expected digits
  - `icon` - Lucide icon name

---

**System Version:** 1.0.0  
**Last Updated:** December 27, 2025  
**Status:** ✅ Production Ready

