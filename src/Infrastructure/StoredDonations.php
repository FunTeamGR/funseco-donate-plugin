<?php
namespace Funseco\Donate\Infrastructure;

use Funseco\Core\Models\Player;
use Funseco\Core\Models\PlayerExtra;
use Funseco\Core\SharedKernel\Donations;

/** Donation totals as stored in player_extras.donations (imported from XAseco). */
class StoredDonations implements Donations
{
    public function total(Player $player): int
    {
        return $player->id ? (int) PlayerExtra::where('player_id', $player->id)->value('donations') : 0;
    }

    public function add(Player $player, int $coppers): void
    {
        if (! $player->id || $coppers <= 0) {
            return;
        }
        PlayerExtra::firstOrCreate(['player_id' => $player->id])->increment('donations', $coppers);
    }

    public function top(int $limit): array
    {
        return PlayerExtra::query()
            ->join('players', 'players.id', '=', 'player_extras.player_id')
            ->where('player_extras.donations', '>', 0)
            ->orderByDesc('player_extras.donations')->orderBy('players.id')
            ->limit($limit)
            ->pluck('player_extras.donations', 'players.login')
            ->map(fn ($total) => (int) $total)->all();
    }
}
