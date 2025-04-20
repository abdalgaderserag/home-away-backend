<?php

namespace App\Enum\Offer;

enum MilestoneStatus: string
{
    case Waiting = 'waiting';
    case InProgress = 'inprogress';
    case Completed = 'completed';
}
