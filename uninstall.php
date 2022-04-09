<?php
if (!defined(WP_UNINSTALL_PLUGIN)) {
    die;
}
use Weather\Admin\Configuration;
use Weather\WeatherStorage;
use \Weather\Service\CityTermManager;

Configuration::deleteSettings();
(new CityTermManager())->deleteCityVocabulary();
(new WeatherStorage())->deleteTable();
