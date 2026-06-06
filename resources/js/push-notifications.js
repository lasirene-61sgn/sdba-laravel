// JavaScript file for handling real-time push notifications
// This would typically be included in your frontend to receive real-time notifications

// Using Laravel Echo for WebSocket connections
// Make sure to install: npm install laravel-echo pusher-js

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

// Initialize Laravel Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY || 'your-pusher-app-key', // Set in your .env file
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1', // Set in your .env file
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth', // Laravel's default auth endpoint
});

// Listen for customer-specific notifications
function listenForNotifications(customerId) {
    // Listen on the private channel for this customer
    window.Echo.private(`customer.${customerId}`)
        .listen('CustomerNotificationEvent', (e) => {
            console.log('New notification received:', e);
            
            // Display notification to user
            displayNotification(e.notification);
            
            // Update UI to show new notification
            updateNotificationBadge();
        });
}

// Function to display notification to user
function displayNotification(notification) {
    // Create a browser notification if permissions are granted
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notification.message, {
            body: 'New notification from the app',
            icon: '/path/to/icon.png',
            tag: notification.id
        });
    }
    
    // Also update the UI
    const notificationElement = document.createElement('div');
    notificationElement.className = 'notification-item';
    notificationElement.innerHTML = `
        <div class="notification-content">
            <h4>${notification.message}</h4>
            <small>${new Date().toLocaleTimeString()}</small>
        </div>
    `;
    
    // Add to notifications container
    const container = document.getElementById('notifications-container');
    if (container) {
        container.prepend(notificationElement);
    }
}

// Function to update notification badge
function updateNotificationBadge() {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        let count = parseInt(badge.textContent) || 0;
        badge.textContent = count + 1;
    }
}

// Request notification permissions
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Notification permission granted');
            }
        });
    }
}

// Initialize notification system
function initNotificationSystem(customerId) {
    if (typeof Echo !== 'undefined') {
        listenForNotifications(customerId);
        requestNotificationPermission();
        console.log('Real-time notification system initialized for customer:', customerId);
    } else {
        console.error('Laravel Echo not loaded. Please include the Echo library.');
    }
}

// Export functions for use in other modules
export {
    initNotificationSystem,
    listenForNotifications,
    displayNotification,
    updateNotificationBadge
};

// Example usage:
// When a customer logs in, initialize the notification system
// initNotificationSystem(customerId);