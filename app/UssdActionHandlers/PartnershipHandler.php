<?php

declare(strict_types=1);

namespace App\UssdActionHandlers;

use App\Interfaces\UssdActionHandler;
use App\Models\UssdSession;

class PartnershipHandler implements UssdActionHandler
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(UssdSession $session): void
    {
        // TODO: Implement handle() method.
    }
}
