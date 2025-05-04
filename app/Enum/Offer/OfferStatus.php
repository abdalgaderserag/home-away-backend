<?php

namespace App\Enum\Offer;

enum OfferStatus : string
{
    case Pending = 'pending';
    case Declined = 'declined';
    case Accepted = 'accepted';
    case Completed = 'completed';
}
