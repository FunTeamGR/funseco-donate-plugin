<?php
namespace Funseco\Donate\Application\Listeners;

use Funseco\Core\Models\Player;
use Funseco\Core\Services\Chat\Facades\Chat;

/** A donate panel button is the same as typing /donate <coppers>. */
class DonateActionHandler
{
    public function handle(Player $player, string $action): void
    {
        Chat::run($player, 'donate', [str_replace('donate', '', $action)]);
    }
}
