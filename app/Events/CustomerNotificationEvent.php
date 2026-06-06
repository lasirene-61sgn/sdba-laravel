<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;
use App\Models\Notification;

class CustomerNotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $customer;

    /**
     * Create a new event instance.
     */
    public function __construct(Customer $customer, Notification $notification)
    {
        $this->customer = $customer;
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('customer.' . $this->customer->id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification,
            'customer_id' => $this->customer->id,
            'timestamp' => now()->toISOString(),
        ];
    }
}