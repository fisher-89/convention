<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DrawStart implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $data;
    protected $users;
    public $broadcastQueue = 'convention';
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data,array $users)
    {
        $this->data = $data;
        $this->users = $users;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
//        return new PrivateChannel('channel-name');
        return new Channel('draw');
    }

    public function broadcastWith()
    {
        return [
          'data'=>$this->data,
          'users'=>$this->users,
        ];
    }
}
