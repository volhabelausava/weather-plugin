<?php

namespace Weather;

use Weather\Admin\Configuration;
use Weather\Controller\WeatherController;
use Weather\Service\PagesManager;

class Init
{
    /**
     * Stores all classes for plugin initialization in array.
     * @return string[]
     */
    public static function getComponents()
    {
        return [
            Configuration::class,
            WeatherController::class,
            PagesManager::class,
            WeatherWidget::class
        ];
    }

    /**
     * Initialize the objects required for plugin work.
     */
    public static function run()
    {
        session_start();
        foreach (self::getComponents() as $class) {
            new $class;
        }
    }
}
