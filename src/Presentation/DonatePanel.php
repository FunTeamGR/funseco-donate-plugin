<?php
namespace Funseco\Donate\Presentation;

use Funseco\Core\Models\Player;
use Funseco\Core\Services\Cache\CurrentPlayers;
use Funseco\Core\Services\Looks\PlayerLooks;
use Funseco\Core\Services\ManiaLink\Facades\ManiaLink;

/**
 * The donate bar (XAseco panels/Donate*.xml). Which template a player sees is their own choice
 * (/donpanel); templates are listed in config/panels.php. Display only: nothing is billed.
 */
class DonatePanel
{
    /** Template names (full, with the 'Donate' prefix) this plugin can draw. */
    public static function templates(): array
    {
        return array_map(fn ($n) => 'Donate'.$n, array_keys(config('panels.donate', [])));
    }

    public static function showToAll(): void
    {
        foreach (CurrentPlayers::all() as $player) {
            self::showTo($player);
        }
    }

    public static function showTo(Player $player): void
    {
        $xml = self::xml(app(PlayerLooks::class)->panel($player, 'donate'));
        ManiaLink::displayLogin($player, $xml ?? self::empty());
    }

    public static function hideFromAll(): void
    {
        ManiaLink::displayAll(self::empty());
    }

    public static function empty(): string
    {
        return '<manialink id="'.ManiaLink::getAction('donate_panel').'"></manialink>';
    }

    /** XML for a template given by full name; null for '' or an unknown name. */
    public static function xml(string $name): ?string
    {
        $t = config('panels.donate.'.substr($name, strlen('Donate')));
        if ($name === '' || !is_array($t)) {
            return null;
        }

        $coppers = array_values(config('donate.coppers', []));
        $xml = '<manialink id="'.ManiaLink::getAction('donate_panel').'"><frame posn="'.$t['frame'].'">'
            .'<quad sizen="'.$t['bg'][0].' '.$t['bg'][1].'" style="Bgs1InRace" substyle="NavButton"/>';
        if ($t['coin']) {
            $size = $t['coin'][2];
            $xml .= '<quad posn="'.$t['coin'][0].' '.$t['coin'][1].' 2" sizen="'.$size.' '.$size.'" style="Icons128x128_1" substyle="Coppers"/>';
        }
        $labelStyle = isset($t['label_style']) ? ' style="'.$t['label_style'].'"' : ' textsize="'.($t['label_textsize'] ?? 1).'" textcolor="dddf"';
        $xml .= '<label posn="'.$t['label'][0].' '.$t['label'][1].' 2"'.$labelStyle.' text="'.htmlspecialchars($t['label'][2]).'"/>'
            .'<format textsize="'.($t['textsize'] ?? 1).'" textcolor="ffff"/>';

        foreach ($coppers as $i => $value) {
            if (!isset($t['w'][$i])) {
                break;
            }
            $text = sprintf($t['texts'][$i] ?? $t['texts'][0], $value);
            $pos = isset($t['ys'])
                ? ($t['right'].' '.$t['ys'][$i].' 2')
                : ($t['x'][$i].' '.$t['y'].' 2');
            $xml .= '<label posn="'.$pos.'" sizen="'.$t['w'][$i].' '.$t['h'].'"'
                .(isset($t['ys']) ? ' halign="right"' : '')
                .($t['button_style'] ? ' style="'.$t['button_style'].'"' : '')
                .($t['scale'] != 1 ? ' scale="'.$t['scale'].'"' : '')
                .' text="'.htmlspecialchars($text).'" action="'.ManiaLink::getAction('donate'.$value).'"/>';
        }

        return $xml.'</frame></manialink>';
    }
}
