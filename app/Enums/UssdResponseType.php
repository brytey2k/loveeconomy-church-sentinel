<?php

declare(strict_types=1);

namespace App\Enums;

enum UssdResponseType: string
{
    case RESPONSE = 'response';
    case ADD_TO_CART = 'AddToCart';
    case RELEASE = 'release';
}
