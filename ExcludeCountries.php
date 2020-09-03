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
use Piwik\Piwik;
use Piwik\Plugin;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Settings\Setting;
use Piwik\Tracker\Request;

class ExcludeCountries extends Plugin
{
    /**
     * inspired by plugins/UserCountry/Columns/Country.php
     */
    public static function listCountries() {
        $regionDataProvider = StaticContainer::get('Piwik\Intl\Data\Provider\RegionDataProvider');
        $countryList = $regionDataProvider->getCountryList();
        array_walk($countryList, function (&$item, $key) {
            $item = Piwik::translate('Intl_Country_' . strtoupper($key));
        });
        asort($countryList); //order by localized name
        return $countryList;
    }

    public function registerEvents() {
        return [
            "Tracker.isExcludedVisit" => "checkExcludedCountry",
        ];
    }

    public function checkExcludedCountry(&$excluded, Request $request) {
        $logger = StaticContainer::getContainer()->get("Psr\Log\LoggerInterface");
        $provider = LocationProvider::getProviderById(Common::getCurrentLocationProviderId());
        $settings = new SystemSettings();


        $location = $provider->getLocation(["ip" => $request->getIp()]);
        $countryCode = strtolower($location[LocationProvider::COUNTRY_CODE_KEY]);

        $excludeBool = $settings->excludeBool->getValue();

        if ($excludeBool) {
            $countries = $this->settingToCountryCodes($settings->excludedCountries);
            if (in_array($countryCode, $countries)) {
                $logger->debug("request excluded by ExcludeCountries plugin (" . $countryCode . ")");
                $excluded = true;
            }
        } else {
            $countries = $this->settingToCountryCodes($settings->includedCountries);
            if (!in_array($countryCode, $countries)) {
                $logger->debug("request excluded by ExcludeCountries plugin (" . $countryCode . ")");
                $excluded = true;
            }
        }
    }

    private function settingToCountryCodes(Setting $setting) {
        $codes = [];
        foreach ($setting->getValue() as $value) {
            if (!empty($value["country"])) {
                $codes[] = $value["country"];
            }
        }
        return $codes;
    }

}
