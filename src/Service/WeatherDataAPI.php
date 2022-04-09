<?php

namespace Weather\Service;

use Weather\Admin\Configuration;
use Weather\Entity\Weather;
use Weather\Exception\InvalidDataException;
use Weather\Exception\MissingDataException;
use Weather\Exception\UnauthorisedException;

/**
 * Gets weather data from the API resource http://api.openweathermap.org/data/2.5/weather
 */
class WeatherDataAPI implements WeatherDataInterface
{
    /**
     * City term manager.
     * @var CityTermManager
     */
    protected $cityTermManager;

    /**
     * Constant for defining base API URL.
     */
    const BASE_URL = 'http://api.openweathermap.org/data/2.5/weather';

    /**
     * Length of API key, provided by http://api.openweathermap.org.
     */
    const API_KEY_LENGTH = 32;

    /**
     * WeatherDataAPI constructor.
     */
    public function __construct()
    {
        $this->cityTermManager = new CityTermManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getWeatherData()
    {
        $params = $this->getParams();
        $url = static::BASE_URL . '?' . http_build_query($params);
        $jsonData = $this->getJSONFromAPI($url);
        $arrayData = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        $weather = new Weather(
            $arrayData['main']['temp'],
            $arrayData['main']['feels_like'],
            $arrayData['main']['humidity'],
            $arrayData['wind']['speed'],
            $this->cityTermManager->getCityIdByName($arrayData['name'])
        );

        return $weather;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiCityName(array $userParams)
    {
        $options = get_option(Configuration::OPTION_NAME);
        $userParams['key'] = $userParams['key'] ?? $options[Configuration::OPTION_KEY];
        if (empty($userParams['city']) || empty($userParams['key'])) {
            throw new \Exception();
        }
        $params = [
            'q' => $userParams['city'],
            'appid' => $userParams['key'],
        ];
        $url = static::BASE_URL . '?' . http_build_query($params);
        $jsonData = $this->getJSONFromAPI($url);
        $arrayData = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        $apiCityName = $arrayData['name'];

        return $apiCityName;
    }

    /**
     * Gets JSON data from API resource.
     *
     * @param string $url
     *    URL ready for the query.
     *
     * @throws InvalidDataException
     *    When data for request is invalid (for example, there is no such city).
     * @throws MissingDataException
     *    When data for request is missed.
     * @throws UnauthorisedException
     *    When authorization data for request is invalid.
     * @throws \Exception
     *    When unexpected error is occurred.
     */
    private function getJSONFromAPI(string $url)
    {
        $response = wp_remote_get($url);
        $code = wp_remote_retrieve_response_code($response);
        switch ($code) {
            case 200:
                return wp_remote_retrieve_body($response);
                break;
            case 401:
                throw new UnauthorisedException();
                break;
            case 404:
                throw new InvalidDataException();
                break;
            case 400:
                throw new MissingDataException();
                break;
            default:
                throw new \Exception();
                break;
            }
    }

    /**
     * Constructs array of parameters for the query.
     * @return array
     */
    private function getParams()
    {
        $options = get_option(Configuration::OPTION_NAME);
        $key = $options[Configuration::OPTION_KEY] ?? '';
        $city = sanitize_text_field($_COOKIE[CityTermManager::COOKIE_CITY]) ?? $options[Configuration::OPTION_CITY];

        return [
            'q' => $city ?? '',
            'units' => 'metric',
            'appid' => $key,
        ];
    }
}
