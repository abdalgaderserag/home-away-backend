<?php

namespace App\Enum\Offer;

enum OfferStatus : string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Declined = 'declined';
}
