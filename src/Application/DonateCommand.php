<?php
namespace Funseco\Donate\Application;

use Illuminate\Support\Facades\Log;
use Funseco\Core\Models\Player;
use Funseco\Core\Services\Billing\BillingFlow;
use Funseco\Core\Services\Cache\ServerInfo;
use Funseco\Core\SharedKernel\Billing\BillResult;
use Funseco\Core\SharedKernel\Commands\Command;
use Funseco\Core\SharedKernel\Commands\DeclaresCommand;
use Funseco\Core\SharedKernel\Commands\Described;
use Funseco\Core\SharedKernel\Donations;
use Funseco\Core\SharedKernel\Events\DonationReceived;

/** /donate <coppers>: charge the player, thank them, add to their stored total. Panel buttons run it too. */
class DonateCommand implements Command, Described
{
    use DeclaresCommand;

    protected string $name = 'donate';
    protected string $description = 'Donates coppers to the server';
    protected string $usage = '<coppers>';

    public function handle(Player $player, array $args): void
    {
        $flow = app(BillingFlow::class);
        $amount = (string) ($args[0] ?? '');

        // XAseco order: United server, United player, then the amount.
        $flow->check($player, function () use ($flow, $player, $amount) {
            if ($amount === '' || ! is_numeric($amount)) {
                $flow->tell($player->login, config('donate.messages.help'));

                return;
            }
            $coppers = (int) $amount;
            $min = (int) config('donate.min', 10);
            if ($coppers < $min) {
                $flow->tell($player->login, $this->text(config('donate.messages.minimum'), $min));

                return;
            }

            $server = (string) (ServerInfo::get()['server_name'] ?? '');
            $label = format_colors($this->text(config('donate.messages.label'), $coppers, $server));
            $flow->charge($player, $coppers, $label, fn (BillResult $result) => $this->paid($player, $result, $server));
        });
    }

    private function paid(Player $player, BillResult $result, string $server): void
    {
        $coppers = $result->coppers;
        if ($coppers <= 0) {
            return;
        }
        $messages = config('donate.messages');
        if ($coppers >= (int) config('donate.public_threshold', 100)) {
            \Server::query()->ChatSendServerMessage(format_colors($this->text($messages['thanks_all'], $server, $coppers, $player->nickname)));
        } else {
            \Server::query()->ChatSendServerMessageToLogin(format_colors($this->text($messages['thanks_you'], $coppers)), $player->login);
        }
        Log::info("Player {$player->login} donated {$coppers} coppers to this server (TxId {$result->transactionId})");
        app(Donations::class)->add($player, $coppers);
        event(new DonationReceived($player, $coppers));
    }

    private function text(string $template, string|int ...$values): string
    {
        foreach (array_values($values) as $i => $value) {
            $template = str_replace('{'.($i + 1).'}', (string) $value, $template);
        }

        return $template;
    }
}
