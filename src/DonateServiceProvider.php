<?php
namespace Funseco\Donate;

use Funseco\Core\SharedKernel\PublishesPluginConfig;
use Funseco\Core\SharedKernel\Settings\Setting;
use Funseco\Core\SharedKernel\Settings\SettingType;
use Funseco\Core\SharedKernel\Settings\SettingsRegistry;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Funseco\Core\Services\ManiaLink\ManiaLinkService;
use Funseco\Core\Services\Looks\PlayerLooks;
use Funseco\Core\SharedKernel\Events\MapEnded;
use Funseco\Core\SharedKernel\Events\MapStarted;
use Funseco\Core\SharedKernel\Events\PanelChanged;
use Funseco\Core\SharedKernel\Events\StartupEvent;
use Funseco\Core\SharedKernel\Donations;
use Funseco\Donate\Application\TopDonsCommand;
use Funseco\Donate\Infrastructure\StoredDonations;
use Funseco\Donate\Presentation\DonatePanel;
use Funseco\Donate\Application\Listeners\DonateActionHandler;
use Funseco\Core\SharedKernel\Events\PlayerConnected;
use Funseco\Donate\Application\Listeners\PlayerConnectListener;
use Funseco\Donate\Application\Listeners\StartupListener;

class DonateServiceProvider extends ServiceProvider
{
    use PublishesPluginConfig;

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/donate.php', 'donate');
        // Template geometry; the app's config/panels.php may still override a whole 'donate' template.
        $this->mergeConfigFrom(__DIR__.'/../config/panels.php', 'panels.donate');
        $this->publishPluginConfig(__DIR__.'/..');

        // coppers: one ManiaLink action per button is registered in boot(), so it needs a restart. Messages skipped (texts).
        $this->app->make(SettingsRegistry::class)->declare('donate', 'donate', [
            Setting::int('min', 'Minimum donation', min: 1, unit: 'coppers'),
            Setting::int('public_threshold', 'Public thanks from', min: 0, unit: 'coppers'),
            Setting::list('coppers', 'Donation buttons', itemType: SettingType::Int, restart: true),
        ]);
        $this->app->singleton(Donations::class, StoredDonations::class);
    }

    public function boot(): void
    {
        \Chat::register(TopDonsCommand::class);
        \Chat::register(\Funseco\Donate\Application\DonateCommand::class);
        app(ManiaLinkService::class)->addAction('donate_panel');
        app(ManiaLinkService::class)->addActions(
            array_map(fn ($copper) => 'donate'.$copper, config('donate.coppers')),
            DonateActionHandler::class,
        );

        Event::listen(StartupEvent::class, StartupListener::class);   // after the player list is loaded; SocketReady is too early
        Event::listen(PlayerConnected::class, PlayerConnectListener::class);

        // Panel choices: /donpanel picks the template; the bar is hidden on the score screen.
        app(PlayerLooks::class)->registerPanels('donate', DonatePanel::templates());
        Event::listen(PanelChanged::class, function (PanelChanged $e) {
            if ($e->kind === 'donate') {
                DonatePanel::showTo($e->player);
            }
        });
        Event::listen(MapEnded::class, function () {
            DonatePanel::hideFromAll();
        });
        Event::listen(MapStarted::class, function () {
            DonatePanel::showToAll();
        });
    }
}
