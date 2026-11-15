<?php
namespace Funseco\Donate\Application;

use Funseco\Core\Models\Player;
use Funseco\Core\Services\Funseco\ServerAccount;
use Funseco\Core\SharedKernel\Commands\Command;
use Funseco\Core\SharedKernel\Commands\DeclaresCommand;
use Funseco\Core\SharedKernel\Commands\Described;
use Funseco\Core\SharedKernel\Donations;
use Funseco\Core\SharedKernel\UI\Window;

/** /topdons: the 100 biggest donators (read-only; stored totals, United servers only). */
class TopDonsCommand implements Command, Described
{
    use DeclaresCommand;

    protected string $name = 'topdons';
    protected string $description = 'Displays top 100 highest donators';

    public function handle(Player $player, array $args): void
    {
        app(ServerAccount::class)->united(function (bool $united) use ($player) {
            if (!$united) {
                $this->tell($player, '{#server}> {#error}This requires a TM United Forever server!');

                return;
            }

            $top = app(Donations::class)->top(100);
            if ($top === []) {
                $this->tell($player, '{#server}> {#error}No donator(s) found!');

                return;
            }

            $nicks = Player::whereIn('login', array_keys($top))->pluck('nickname', 'login');
            $colour = (bool) config('funseco.lists_colornicks', true);
            $rows = [];
            $i = 0;
            foreach ($top as $login => $total) {
                $nick = (string) ($nicks[$login] ?? $login);
                $rows[] = [
                    str_pad((string) ++$i, 2, '0', STR_PAD_LEFT).'.',
                    '{#black}'.($colour ? $nick : preg_replace('/\$(?:[0-9a-f]{1,3}|.)?/i', '', $nick)),
                    (string) $total,
                ];
            }

            Window::show($player, 'Current TOP 100 Donators:', $rows, [0.9, 0.1, 0.65, 0.15], ['Icons128x128_1', 'Coppers', -0.01]);
        });
    }

    private function tell(Player $player, string $message): void
    {
        \Server::query()->ChatSendServerMessageToLogin(format_colors($message), $player->login);
    }
}
