# Real-Time Push Notifications Setup Guide

This guide explains how to set up and use real-time push notifications in your Laravel application.

## 1. Configuration

### Environment Variables
Add the following to your `.env` file:

```env
# Pusher Configuration (for WebSocket real-time notifications)
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_CLUSTER=mt1
PUSHER_SCHEME=https

# Firebase Configuration (for push notifications)
FIREBASE_API_KEY=your_firebase_api_key
FIREBASE_PROJECT_ID=your_firebase_project_id
FIREBASE_MESSAGING_SENDER_ID=your_messaging_sender_id
FIREBASE_APP_ID=your_firebase_app_id
FIREBASE_SERVER_KEY=your_legacy_server_key (optional)
FIREBASE_ACCESS_TOKEN=your_access_token (from API response)

# Broadcasting Configuration
BROADCAST_CONNECTION=pusher
```

### Broadcasting Service Provider
The broadcasting service provider is already registered in `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ScheduleServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
];
```

## 2. Database Migration

Make sure your notifications table includes the necessary fields. The notification model is already set up in `app/Models/Notification.php`.

## 3. Frontend Integration

### Install Dependencies
```bash
npm install laravel-echo pusher-js
```

### Include the JavaScript File
Include the push notification JavaScript file in your frontend:

```html
<script src="/js/push-notifications.js"></script>
```

### Initialize the Notification System
When a customer logs in, initialize the notification system:

```javascript
// Get the customer ID from your authentication system
const customerId = /* your customer ID */;
initNotificationSystem(customerId);
```

## 4. Backend Usage

### Sending Real-Time Notifications

You can send real-time notifications using the `RealTimeNotificationService`:

```php
use App\Services\RealTimeNotificationService;

$notificationService = new RealTimeNotificationService();

// Send a notification to a specific customer
$notificationService->sendRealTimeNotification(
    $customerId,
    'event_added',
    'New event added: ' . $event->name,
    [
        'event_id' => $event->id,
        'event_name' => $event->name
    ]
);
```

### Using with NotificationsService

The existing `NotificationsService` has been updated to automatically send real-time notifications when creating standard notifications:

```php
use App\Services\NotificationsService;
use App\Services\RealTimeNotificationService;

$realTimeService = new RealTimeNotificationService();
$notificationService = new NotificationsService($realTimeService);

// This will create a database notification and send a real-time notification
$notificationService->createEventAddedNotification($adminId, $event);
```

## 5. API Endpoints

The following API endpoints are available for notification management:

- `GET /api/customer/notifications` - Get all notifications
- `GET /api/customer/all-notifications` - Get all notifications including admin updates
- `POST /api/customer/notifications/{id}/read` - Mark a notification as read
- `POST /api/customer/notifications/mark-all-read` - Mark all notifications as read
- `GET /api/customer/notifications/unread-count` - Get count of unread notifications

## 6. Event Broadcasting

The system uses Laravel's event broadcasting to send real-time notifications via WebSockets:

- `CustomerNotificationEvent` - Handles broadcasting notifications to specific customers
- Channel: `private-customer.{id}` - Private channel for each customer

## 7. Frontend Implementation

The JavaScript file `resources/js/push-notifications.js` provides functions to:

- Initialize the notification system
- Listen for real-time notifications
- Display browser notifications
- Update UI elements

## 8. Testing

To test the real-time notifications:

1. Set up Pusher credentials in your `.env` file
2. Start your Laravel application
3. Connect to the WebSocket using the frontend JavaScript
4. Trigger notification events from the backend
5. Observe real-time updates in the frontend

## 9. Troubleshooting

### Common Issues:

1. **WebSocket Connection Issues**: Ensure your Pusher credentials are correct
2. **Channel Authorization**: Make sure the customer can access their private channel
3. **CORS Issues**: Configure CORS properly for WebSocket connections

### Debugging:

1. Check browser console for WebSocket errors
2. Verify Pusher dashboard for connection activity
3. Check Laravel logs for broadcasting errors

## 10. Security Considerations

- Private channels ensure customers only receive their own notifications
- Proper authentication is required to access notification channels
- All notification data is properly sanitized before broadcasting