<?php

namespace Weather\Controller;

use Weather\Admin\Configuration;
use Weather\Entity\Weather;
use Weather\Exception\InvalidDataException;
use Weather\Exception\MissingDataException;
use Weather\Exception\UnauthorisedException;
use Weather\Service\CityTermManager;
use Weather\Service\ErrorHandling;
use Weather\Service\WeatherDataAPI;
use Weather\View;

class GetDataAction
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
     * getDataAction constructor.
     */
    public function __construct()
    {
        $this->weatherData = new WeatherDataAPI();
        $this->cityTermManager = new CityTermManager();
    }

    /**
     * Action for WeatherController to get data for "current weather data" request.
     * @return string
     */
    public function __invoke()
    {
        try {
            $weather = $this->weatherData->getWeatherData();
            $cityName = $this->cityTermManager->getCityNameById($weather->getCityId());
            if (current_user_can('manage_options')) {
                $_SESSION[Weather::SESSION_NAME] = $weather;
            }
            return View::getContent('content-weather', [
                'title' => 'Current weather at ' . $cityName,
                'temperature' => $weather->getTemperature(),
                'feelsLikeTemperature' => $weather->getFeelsLikeTemperature(),
                'humidity' => $weather->getHumidity(),
                'windSpeed' => $weather->getWindSpeed(),
            ]);
        } catch (InvalidDataException $e) {
            ErrorHandling::dieErrorResponse($e, 'Provided data is incorrect.', 404);
        } catch (UnauthorisedException $e) {
            if (!current_user_can('manage_options')) {
                ErrorHandling::dieErrorResponse($e);
            }
            ErrorHandling::dieErrorResponse($e, 'Invalid API key.', 401, admin_url('options-general.php?page=' . Configuration::OPTION_GROUP), 'Change API settings', false);
        } catch (MissingDataException $e) {
            ErrorHandling::dieErrorResponse($e, 'Please enter city to get current weather.', 400);
        } catch (\Exception | \TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
    }
}