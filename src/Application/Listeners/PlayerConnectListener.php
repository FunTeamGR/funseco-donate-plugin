<?php

namespace Funseco\Donate\Application\Listeners;

use Funseco\Donate\Presentation\DonatePanel;
use Funseco\Core\SharedKernel\Events\PlayerConnected;

class PlayerConnectListener
{
    public function handle(PlayerConnected $event): void
    {
        DonatePanel::showTo($event->player);
    }
}
