<?php

declare(strict_types=1);

namespace Ken_Cir\Casino\Handlers;

use Ken_Cir\Casino\CasinoMain;
use pocketmine\event\Listener;

class EventHandler implements Listener
{
    private CasinoMain $plugin;

    public function __construct(CasinoMain $plugin)
    {
        $this->plugin = $plugin;
    }
}