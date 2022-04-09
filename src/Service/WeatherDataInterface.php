<?php

namespace Weather\Service;

/**
 * Provides an interface for weather data getting service.
 */
interface WeatherDataInterface
{
    /**
     * Gets weather data.
     *
     * @return \Weather\Entity\WeatherInterface
     *   Weather object with data.
     */
    public function getWeatherData();

  /**
   * Gets standard city name provided by API Service.
   *
   * @param array $userParams
   *    Parameters for the query from user input.
   * @return string
   *    City name from API Service.
   */
    public function getApiCityName(array $userParams);
}
