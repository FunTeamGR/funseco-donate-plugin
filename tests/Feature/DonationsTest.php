<?php
namespace Funseco\Donate\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Funseco\Core\Testing\FakesServer;
use Funseco\Donate\Tests\TestCase;
use Funseco\Core\Models\Player;
use Funseco\Core\Models\PlayerExtra;
use Funseco\Core\Services\Cache\CurrentPlayers;
use Funseco\Core\Services\Chat\Facades\Chat;
use Funseco\Core\SharedKernel\Donations;

class DonationsTest extends TestCase
{
    use FakesServer;
    use RefreshDatabase;

    private Player $alice;
    private Player $bob;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeServer();
        state()->forget('funseco:server_united');
        CurrentPlayers::clear();
        $this->alice = Player::factory()->create(['login' => 'alice', 'nickname' => 'Alice']);
        $this->bob = Player::factory()->create(['login' => 'bob', 'nickname' => 'Bob']);
        CurrentPlayers::add($this->alice);
        CurrentPlayers::add($this->bob);
        PlayerExtra::create(['player_id' => $this->alice->id, 'donations' => 500]);
        PlayerExtra::create(['player_id' => $this->bob->id, 'donations' => 2000]);
    }

    private function united(bool $united): void
    {
        $this->responses['GetMainServerPlayerInfo'] = ['Login' => 'srv'];
        $this->responses['GetDetailedPlayerInfo'] = ['Login' => 'x', 'OnlineRights' => $united ? 3 : 1, 'LadderStats' => []];
    }

    public function test_contract_reads_the_stored_totals(): void
    {
        $donations = app(Donations::class);
        $this->assertSame(500, $donations->total($this->alice));
        $this->assertSame(['bob' => 2000, 'alice' => 500], $donations->top(10));
        $this->assertSame(['bob' => 2000], $donations->top(1));
    }

    public function test_topdons_lists_donators_on_a_united_server(): void
    {
        $this->united(true);
        Chat::resolve('/topdons', 'alice');

        $window = $this->window();
        $this->assertStringContainsString('Current TOP 100 Donators:', $window);
        $this->assertLessThan(strpos($window, 'Alice'), strpos($window, 'Bob'), 'highest first');
        $this->assertStringContainsString('2000', $window);
    }

    public function test_topdons_needs_a_united_server_and_donators(): void
    {
        $this->united(false);
        Chat::resolve('/topdons', 'alice');
        $this->assertSame([format_colors('{#server}> {#error}This requires a TM United Forever server!')], $this->messages());

        state()->forget('funseco:server_united');
        $this->united(true);
        PlayerExtra::query()->update(['donations' => 0]);
        $this->calls = [];
        Chat::resolve('/topdons', 'alice');
        $this->assertSame([format_colors('{#server}> {#error}No donator(s) found!')], $this->messages());
    }

    public function test_stats_shows_the_donations_row_only_on_a_united_server(): void
    {
        $this->united(false);
        Chat::resolve('/stats', 'alice');
        $this->assertStringNotContainsString('Donations', $this->window());

        state()->forget('funseco:server_united');
        $this->united(true);
        Chat::resolve('/stats', 'alice');
        $this->assertStringContainsString('Donations', $this->window());
        $this->assertStringContainsString('500', $this->window());

        Chat::resolve('/stats bob', 'alice');   // offline-looking info is still online here; rights come from the same fake
        $this->assertStringContainsString('2000', $this->window());
    }
}
