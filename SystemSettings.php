<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExcludeCountries;

use Piwik\Settings\FieldConfig;
use Piwik\Settings\Setting;

/**
 * Defines Settings for ExcludeCountries.
 *
 * Usage like this:
 * $settings = new SystemSettings();
 * $settings->metric->getValue();
 * $settings->description->getValue();
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $excludeBool;

    /** @var Setting */
    public $excludedCountries;

    /** @var Setting */
    public $includedCountries;

    protected function init() {
        $this->excludeBool = $this->createExcludeBoolSetting();
        $this->excludedCountries = $this->createExcludedCountriesSetting();
        $this->includedCountries = $this->createIncludedCountriesSetting();

    }

    private function createExcludeBoolSetting() {
        return $this->makeSetting('excludeBool', $default = true, FieldConfig::TYPE_BOOL, function (FieldConfig $field) {
            $field->title = 'Exclude Countries';
            $field->uiControl = FieldConfig::UI_CONTROL_CHECKBOX;
            $field->description = 'If enabled, visitors from the selected countries are not tracked. If disabled, only visitors from the selected countries are tracked';
        });
    }

    private function createExcludedCountriesSetting() {
        return $this->makeSetting('excludedCountries', [], FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $countries = ExcludeCountries::listCountries();
            $field->title = 'Excluded Countries';
            $field->description = "Don't track users from these countries.";
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair("Country", 'country', FieldConfig::UI_CONTROL_SINGLE_SELECT);
            $field1->availableValues = $countries;
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->condition = "excludeBool == true";
        });
    }

    private function createIncludedCountriesSetting() {
        return $this->makeSetting('includedCountries', [], FieldConfig::TYPE_ARRAY, function (FieldConfig $field) {
            $countries = ExcludeCountries::listCountries();
            $field->title = 'Included Countries';
            $field->description = "Only track users from these countries.";
            $field->uiControl = FieldConfig::UI_CONTROL_MULTI_TUPLE;
            $field1 = new FieldConfig\MultiPair("Country", 'country', FieldConfig::UI_CONTROL_SINGLE_SELECT);
            $field1->availableValues = $countries;
            $field->uiControlAttributes['field1'] = $field1->toArray();
            $field->condition = "excludeBool == false";
        });
    }
}
