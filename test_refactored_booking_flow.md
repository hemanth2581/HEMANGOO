# Refactored Time Slot Booking Flow - Test Guide

## Overview
The time slot booking screen has been completely refactored to be simpler, more relevant, and properly integrated with the backend database. This document outlines the changes and testing procedures.

## Key Changes Made

### 1. Database Schema Updates
- **New Schema**: `Backend/hemango_updated_schema.sql`
- **Proper Relations**: Users, Factories, Time Slots, Quality Reports, Bookings
- **Capacity Management**: Real-time slot capacity tracking
- **Admin Integration**: Proper booking status management

### 2. Time Slot Screen Refactoring
- **UI**: Replaced GridLayout with RecyclerView for better performance
- **Layout**: New `item_time_slot.xml` with modern card design
- **Adapter**: `TimeSlotAdapter.kt` for efficient slot management
- **Selection**: Improved slot selection with visual feedback

### 3. Backend API Integration
- **Get Slots**: `Backend/api/v2/slots/get_available.php`
- **Book Slot**: `Backend/api/v2/slots/book_slot.php`
- **Admin Pending**: `Backend/api/v2/admin/pending_bookings.php`
- **Admin Actions**: `Backend/api/v2/admin/update_booking_status.php`

### 4. Data Flow Improvements
- **Real-time Updates**: Slot capacity updates in real-time
- **Transaction Safety**: Database transactions for booking operations
- **Error Handling**: Comprehensive error handling and validation
- **Admin Visibility**: Complete booking details for admin review

## Test Scenarios

### Scenario 1: Farmer Booking Flow
1. **Login as Farmer**
   - Email: farmer@hemango.com
   - Password: (any password)
   - Role: Farmer

2. **Start Booking Process**
   - Navigate to Farmer Dashboard
   - Click "Start Booking"
   - Should go to SelectMangoActivity

3. **Select Mango Details**
   - Choose mango type and variety
   - Enter quantity (e.g., 100 kg)
   - Click "Next"

4. **Select Factory**
   - Choose a factory from the list
   - Click "Book Slot"

5. **Submit Quality Report**
   - Fill quality parameters
   - Upload at least one image
   - Click "Submit Quality Report"

6. **Select Time Slot** ⭐ **REFACTORED SCREEN**
   - **New UI**: RecyclerView with card-based slot display
   - **Features**:
     - Clean, modern card design
     - Time range display (e.g., "9:00 AM - 10:00 AM")
     - Availability indicator ("5 spots left")
     - Price display ("₹150/kg")
     - Capacity information ("Max: 1000kg")
     - Visual selection feedback
   - **Interaction**:
     - Tap on available slot to select
     - Selected slot highlights with blue indicator
     - Confirm button enables only after selection
   - Click "Confirm Booking"

7. **Booking Confirmation**
   - Review all details
   - Click "Confirm Booking"
   - Should show success dialog

### Scenario 2: Admin Review Flow
1. **Login as Admin**
   - Email: admin@hemango.com
   - Password: (any password)
   - Role: Admin

2. **View Pending Requests**
   - Navigate to Admin Dashboard
   - Click "Pending Requests"
   - Should see the booking created by farmer

3. **Review Booking Details**
   - Click on a pending booking
   - Should see complete details:
     - Farmer information
     - Mango details and quantity
     - Selected time slot
     - Quality report with images
     - Booking date and time

4. **Approve/Reject Booking**
   - Click "Approve" or "Reject"
   - Add admin notes if needed
   - Confirm action
   - Booking status should update

### Scenario 3: Database Integration
1. **Check Database Tables**
   - Verify data in `users` table
   - Check `factories` table has sample data
   - Verify `factory_time_slots` has generated slots
   - Check `bookings` table for new booking
   - Verify `quality_reports` table has quality data

2. **Test Slot Capacity Management**
   - Book a slot
   - Check `current_bookings_kg` updates in `factory_time_slots`
   - Verify slot availability decreases
   - Reject booking and verify capacity restores

## Expected Results

### Time Slot Screen
- ✅ **Modern UI**: Clean card-based design with proper spacing
- ✅ **Real-time Data**: Slots load from database with current availability
- ✅ **Visual Feedback**: Clear selection indicators and availability status
- ✅ **Performance**: Smooth scrolling with RecyclerView
- ✅ **User Experience**: Intuitive selection process

### Backend Integration
- ✅ **Data Consistency**: All data properly stored in database
- ✅ **Transaction Safety**: Booking operations are atomic
- ✅ **Capacity Management**: Real-time slot capacity tracking
- ✅ **Admin Access**: Complete booking details available to admin

### Admin Panel
- ✅ **Complete Data**: All booking information visible
- ✅ **Quality Reports**: Images and quality parameters displayed
- ✅ **Status Management**: Proper approve/reject functionality
- ✅ **Real-time Updates**: Changes reflect immediately

## Technical Improvements

### 1. Database Design
- **Normalized Structure**: Proper foreign key relationships
- **Capacity Tracking**: Real-time slot availability management
- **Audit Trail**: Complete booking history and admin actions
- **Data Integrity**: Constraints and validation at database level

### 2. Frontend Architecture
- **RecyclerView**: Efficient slot display with proper recycling
- **Adapter Pattern**: Clean separation of concerns
- **State Management**: Proper selection state handling
- **Error Handling**: Comprehensive error management

### 3. Backend APIs
- **RESTful Design**: Proper HTTP methods and status codes
- **Transaction Safety**: Database transactions for data consistency
- **Validation**: Input validation and error handling
- **CORS Support**: Proper cross-origin request handling

## Files Modified/Created

### Frontend
- `SelectSlotActivity.kt` - Completely refactored
- `TimeSlotAdapter.kt` - New RecyclerView adapter
- `item_time_slot.xml` - New slot item layout
- `activity_timebooking.xml` - Updated layout
- `colors.xml` - Added new colors

### Backend
- `hemango_updated_schema.sql` - New database schema
- `api/v2/slots/get_available.php` - Get available slots API
- `api/v2/slots/book_slot.php` - Book slot API
- `api/v2/admin/pending_bookings.php` - Admin pending bookings API
- `api/v2/admin/update_booking_status.php` - Admin booking actions API

## Next Steps
1. Deploy the new database schema
2. Test the complete flow end-to-end
3. Verify admin panel functionality
4. Performance testing with multiple users
5. Error handling and edge case testing
