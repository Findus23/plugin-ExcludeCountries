<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExcludeCountries;

use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Tracker\Request;

class ExcludeCountries extends \Piwik\Plugin
{
    public function registerEvents() {
        return [
            "Tracker.isExcludedVisit" => "checkExcludedCountry",
        ];
    }

    public function checkExcludedCountry(&$excluded, Request $request) {
        $logger = StaticContainer::getContainer()->get("Psr\Log\LoggerInterface");
        $provider = LocationProvider::getProviderById(Common::getCurrentLocationProviderId());

        $location = $provider->getLocation(["ip" => $request->getIp()]);
        $countryCode = $location[LocationProvider::COUNTRY_CODE_KEY];
        if (strtolower($countryCode) !== "de") {
            $logger->debug("request excluded by ExcludeCountries plugin (" . $countryCode . ")");
            $excluded = true;
        }
    }

}
