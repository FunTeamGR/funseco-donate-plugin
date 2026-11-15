<?php
namespace Funseco\Donate\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Funseco\Core\Testing\FakesServer;
use Funseco\Donate\Tests\TestCase;
use Funseco\Core\Models\Player;
use Funseco\Core\Servers\TmForever\Callbacks\BillUpdated as BillCallback;
use Funseco\Core\Services\Cache\CurrentPlayers;
use Funseco\Core\Services\Chat\Facades\Chat;
use Funseco\Core\Services\ManiaLink\ManiaLinkService;
use Funseco\Core\SharedKernel\Donations;
use Funseco\Core\SharedKernel\Events\DonationReceived;

class DonateChargeTest extends TestCase
{
    use FakesServer;
    use RefreshDatabase;

    private Player $alice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeServer();
        state()->flush();
        state()->forever('funseco:server_united', true);
        state()->forever('funseco:player_united:alice', true);
        $this->responses['SendBill'] = 42;
        CurrentPlayers::clear();
        $this->alice = Player::factory()->create(['login' => 'alice', 'nickname' => 'Alice']);
        CurrentPlayers::add($this->alice);
    }

    private function bill(int $state, string $name = ''): void
    {
        event(new BillCallback(42, $state, $name, 7));
    }

    public function test_donating_charges_then_thanks_everybody_and_stores_the_total(): void
    {
        Event::fake([DonationReceived::class]);
        Chat::resolve('/donate 500', 'alice');

        $bill = $this->sent('SendBill');
        $this->assertCount(1, $bill);
        $this->assertSame(['alice', 500], array_slice($bill[0], 0, 2));
        $this->assertSame('', $bill[0][3]);
        $this->assertSame([], $this->messages('ChatSendServerMessage'), 'nothing before payment');
        $this->assertSame(0, app(Donations::class)->total($this->alice));

        $this->bill(4, 'Payed');

        $this->assertStringContainsString('donated', $this->messages('ChatSendServerMessage')[0]);
        $this->assertStringContainsString('500', $this->messages('ChatSendServerMessage')[0]);
        $this->assertSame(500, app(Donations::class)->total($this->alice));
        Event::assertDispatched(DonationReceived::class, fn ($e) => $e->coppers === 500 && $e->player->login === 'alice');
    }

    public function test_a_small_donation_is_thanked_privately_and_totals_add_up(): void
    {
        Chat::resolve('/donate 50', 'alice');
        $this->bill(4, 'Payed');
        $this->assertSame([], $this->messages('ChatSendServerMessage'));
        $this->assertStringContainsString('Thanks for your', $this->messages()[0]);

        $this->responses['SendBill'] = 43;
        Chat::resolve('/donate 100', 'alice');
        event(new BillCallback(43, 4, 'Payed', 8));
        $this->assertSame(150, app(Donations::class)->total($this->alice));
    }

    public function test_panel_button_runs_the_same_charge(): void
    {
        $id = app(ManiaLinkService::class)->getAction('donate1000');
        app(ManiaLinkService::class)->dispatchAnswer($this->alice, $id);
        $this->assertSame(1000, $this->sent('SendBill')[0][1]);
    }

    public function test_bad_amounts_are_told_and_nothing_is_billed(): void
    {
        Chat::resolve('/donate', 'alice');
        Chat::resolve('/donate abc', 'alice');
        Chat::resolve('/donate 5', 'alice');
        $this->assertSame([], $this->sent('SendBill'));
        $messages = $this->messages();
        $this->assertStringContainsString('/donate <number>', $messages[0]);
        $this->assertStringContainsString('/donate <number>', $messages[1]);
        $this->assertStringContainsString('Minimum donation is', $messages[2]);
        $this->assertStringContainsString('10', $messages[2]);
    }

    public function test_united_checks_in_order(): void
    {
        state()->forever('funseco:server_united', false);
        Chat::resolve('/donate 100', 'alice');
        $this->assertStringContainsString('requires a TM United Forever server', $this->messages()[0]);

        state()->forever('funseco:server_united', true);
        state()->forever('funseco:player_united:alice', false);
        Chat::resolve('/donate 100', 'alice');
        $this->assertStringContainsString('requires a TM United Forever account', $this->messages()[1]);
        $this->assertSame([], $this->sent('SendBill'));
    }

    public function test_refused_and_failed_bills_are_told_and_not_stored(): void
    {
        Chat::resolve('/donate 100', 'alice');
        $this->bill(5, 'Refused');
        $this->assertStringContainsString('Transaction refused!', $this->messages()[0]);

        $this->responses['SendBill'] = 44;
        Chat::resolve('/donate 100', 'alice');
        event(new BillCallback(44, 6, 'Not enough coppers', 1));
        $this->assertStringContainsString('Transaction failed:', $this->messages()[1]);
        $this->assertStringContainsString('Not enough coppers', $this->messages()[1]);
        $this->assertSame(0, app(Donations::class)->total($this->alice));
    }

    public function test_server_without_united_account_after_cache_is_told_by_the_seam(): void
    {
        $this->faults['SendBill'] = 'boom';
        Chat::resolve('/donate 100', 'alice');
        $this->assertStringContainsString('Transaction failed:', $this->messages()[0]);
    }
}
