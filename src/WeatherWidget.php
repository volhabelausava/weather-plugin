<?php

namespace Weather;

use Weather\Service\CityTermManager;
use Weather\Service\WeatherDataAPI;

class WeatherWidget extends \WP_Widget
{
    /**
     * Weather data getting service.
     */
    protected $weatherData;

    /**
     * City Term Manager.
     */
    protected $cityTermManager;

    /**
     * WeatherWidget constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'WeatherWidget',
            'Weather Widget',
            ['description' => 'Shows current weather']
        );
        $this->weatherData = new WeatherDataAPI();
        $this->cityTermManager = new CityTermManager();
        add_action('widgets_init', [$this, 'registerWeatherWidget']);
    }

    /**
     * Registers weather widget.
     */
    public function registerWeatherWidget()
    {
        register_widget($this);
    }

    /**
     * {@inheritdoc}
     */
    public function widget($args, $instance)
    {
        try {
        $weather = $this->weatherData->getWeatherData();
        $cityName = $this->cityTermManager->getCityNameById($weather->getCityId());
        } catch (\Exception $e) {
            error_log($e);
            View::showContent('error');
            return;
        }

        View::showContent('weather-widget', [
            'args' => $args,
            'title' => 'Current weather at ' . $cityName,
            'temperature' => $weather->getTemperature(),
            ]);
    }
}