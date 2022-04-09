<?php

namespace Weather;

use Weather\Service\ErrorHandling;
use Weather\Service\PagesManager;

class Activate
{
    /**
     * Executes actions when plugin has been activated.
     */
    public static function activate()
    {
        $weatherStorage = new WeatherStorage();
        $weatherStorage->createTable();

        try {
            PagesManager::addPage('Current Weather', PagesManager::WEATHER_SLUG);
            PagesManager::addPage('Change City', PagesManager::CHANGE_CITY_SLUG);
            PagesManager::addPage('Statistics', PagesManager::WEATHER_STATISTICS_SLUG);
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }

        flush_rewrite_rules();
    }
}