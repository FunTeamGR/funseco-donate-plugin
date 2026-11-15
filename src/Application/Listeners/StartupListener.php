<?php
namespace Funseco\Donate\Application\Listeners;

use Funseco\Donate\Presentation\DonatePanel;
use Funseco\Core\SharedKernel\Events\StartupEvent;

class StartupListener
{
    public function handle(StartupEvent $event)
    {
        DonatePanel::showToAll();
    }
}
