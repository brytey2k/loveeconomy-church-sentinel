<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\UssdSession;

interface UssdActionHandler
{
    public function handle(UssdSession $session): void;
}
