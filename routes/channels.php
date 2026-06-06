<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('customer.{id}', function ($user, $id) {
    // Check if the authenticated user can access this customer channel
    // For this implementation, we'll check if the authenticated user is the customer themselves
    return (int) $user->id === (int) $id;
});