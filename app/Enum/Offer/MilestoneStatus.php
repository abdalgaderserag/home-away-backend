<?php

namespace App\Enum\Offer;

enum MilestoneStatus: string
{
    case Waiting = 'waiting';
    case Pending = 'pending';
    case Completed = 'completed';
}
