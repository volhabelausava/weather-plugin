<?php

namespace Weather;

use Weather\Service\CityTermManager;
use Weather\Service\ErrorHandling;
use Weather\Service\PagesManager;

class Deactivate
{
    /**
     * Executes actions when plugin has been deactivated.
     */
    public static function deactivate()
    {
        try {
            PagesManager::deletePage(PagesManager::WEATHER_SLUG);
            PagesManager::deletePage(PagesManager::CHANGE_CITY_SLUG);
            PagesManager::deletePage(PagesManager::WEATHER_STATISTICS_SLUG);
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
        flush_rewrite_rules();
    }
}