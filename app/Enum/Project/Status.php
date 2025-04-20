<?php

namespace App\Enum\Project;

enum Status : string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
