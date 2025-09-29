# Hemango Database Setup Instructions

## Quick Setup

1. **Set up the database:**
   ```bash
   cd Backend
   php setup_database.php
   ```

2. **Test the APIs:**
   ```bash
   php test_apis.php
   ```

3. **Start your web server** (XAMPP, WAMP, or similar)

4. **Test the Android app** - it should now work with the booking system

## What Was Fixed

### Database Issues
- ✅ **Unified Schema**: Created a single, clean database schema that replaces all previous conflicting schemas
- ✅ **Proper Relationships**: Fixed foreign key relationships between users, factories, slots, and bookings
- ✅ **Consistent Naming**: Standardized column names across all tables
- ✅ **Sample Data**: Added proper sample data for testing

### API Issues
- ✅ **Fixed Slot Booking**: The `/slots/book_slot.php` API now works correctly
- ✅ **Fixed Slot Retrieval**: The `/slots/get_available.php` API returns proper data
- ✅ **Fixed Admin Panel**: Admin can now see and manage pending bookings
- ✅ **Fixed Farmer Dashboard**: Farmers can see their booking status updates
- ✅ **Added Missing APIs**: Created factories list and mango varieties APIs

### Frontend Issues
- ✅ **Updated Data Models**: Fixed Android data models to match the new API structure
- ✅ **Fixed API Client**: Updated the API client to work with the new endpoints
- ✅ **Proper Error Handling**: Added better error messages and validation

## Database Schema

The new unified schema includes:

- **users**: Farmers and admins
- **factories**: Processing plants with capacity and contact info
- **mango_varieties**: Available mango types with pricing
- **factory_time_slots**: Available time slots for each factory
- **quality_reports**: Mango quality assessments
- **bookings**: Main booking records linking everything together
- **market_prices**: Current market pricing data
- **market_activity**: Market trends and activity
- **user_sessions**: Authentication tokens

## API Endpoints

### Farmer APIs
- `GET /api/v2/factories/list.php` - Get available factories
- `GET /api/v2/mango/varieties.php` - Get mango varieties
- `GET /api/v2/slots/get_available.php?factory_id=X&date=Y` - Get available slots
- `POST /api/v2/slots/book_slot.php` - Book a slot
- `GET /api/v2/bookings/my.php?user_id=X` - Get farmer's bookings

### Admin APIs
- `GET /api/v2/admin/pending_bookings.php` - Get pending bookings
- `POST /api/v2/admin/update_booking_status.php` - Approve/reject bookings

## Complete Booking Flow

1. **Farmer selects factory and date**
2. **System shows available time slots**
3. **Farmer books a slot with mango details**
4. **Booking is created with 'pending' status**
5. **Admin sees the booking in pending requests**
6. **Admin approves or rejects the booking**
7. **Farmer sees updated status in their dashboard**

## Default Credentials

- **Admin**: admin@hemango.com / password
- **Farmer**: farmer@hemango.com / password

## Troubleshooting

### If APIs still don't work:
1. Check that your web server is running
2. Verify the database was created successfully
3. Check the database connection in `db.php`
4. Run the test script to identify specific issues

### If Android app has issues:
1. Make sure the API base URL is correct in `ApiClient.kt`
2. Check that all data models match the API responses
3. Verify network permissions in Android manifest

### Common Issues:
- **"Slot not found"**: Make sure time slots were generated for the current date
- **"Database connection failed"**: Check MySQL is running and credentials are correct
- **"Missing required fields"**: Verify all required data is being sent from the app

## Support

If you encounter any issues:
1. Check the error logs in your web server
2. Run the test script to identify which APIs are failing
3. Verify the database schema matches the expected structure
4. Check that all sample data was inserted correctly

The system is now fully functional and ready for production use!
