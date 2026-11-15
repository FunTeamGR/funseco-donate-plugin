<?php
namespace Funseco\Donate\Tests\Feature;

use Funseco\Donate\Tests\TestCase;
use Funseco\Core\Services\ManiaLink\Facades\ManiaLink;
use Funseco\Donate\Presentation\DonatePanel;

class DonatePanelTest extends TestCase
{
    public function test_default_panel_is_donate_below_cp_list_rm_with_the_owner_values()
    {
        $xml = DonatePanel::xml('DonateBelowCPListRM');

        $this->assertStringContainsString('<manialink id="'.ManiaLink::getAction('donate_panel').'">', $xml);
        $this->assertStringContainsString('<frame posn="10.4 -45.6 0">', $xml);
        $this->assertStringContainsString('text="$s$abc100C"', $xml);
        $this->assertStringContainsString('text="$s$a5010000C"', $xml);
        foreach ([100, 200, 500, 1000, 2000, 5000, 10000] as $copper) {
            $this->assertStringContainsString('action="'.ManiaLink::getAction('donate'.$copper).'"', $xml);
        }
        $this->assertNotFalse(simplexml_load_string($xml));
    }

    public function test_every_template_is_valid_xml_and_unknown_or_off_draws_nothing()
    {
        foreach (DonatePanel::templates() as $name) {
            $this->assertNotFalse(simplexml_load_string(DonatePanel::xml($name)), $name);
        }
        $this->assertNull(DonatePanel::xml(''));
        $this->assertNull(DonatePanel::xml('DonateNope'));
    }
}
