<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OlympiadLeaderboardUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public int $olympiadId) {}

    public function broadcastOn()
    {
        return new Channel("olympiad.{$this->olympiadId}");
    }

    public function broadcastAs()
    {
        return 'leaderboard.updated';
    }
}
