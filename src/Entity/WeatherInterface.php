<?php

namespace Weather\Entity;

/**
 * Provides an interface for weather entity.
 */
interface WeatherInterface
{
    /**
    * Gets temperature.
    *
    * @return float
    *    The Weather temperature.
    */
    public function getTemperature();

    /**
    * Sets temperature.
    *
    * @param float $temperature
    *   The Weather temperature.
    */
    public function setTemperature($temperature);

    /**
    * Gets "feels like" temperature.
    *
    * @return float
    *    The Weather "feels like" temperature.
    */
    public function getFeelsLikeTemperature();

    /**
    * Sets "feels like" temperature.
    *
    * @param float $feelsLikeTemperature
    *   The Weather "feels like" temperature.
    */
    public function setFeelsLikeTemperature($feelsLikeTemperature);

    /**
    * Gets humidity.
    *
    * @return integer
    *    The Weather humidity.
    */
    public function getHumidity();

    /**
    * Sets humidity.
    *
    * @param integer $humidity
    *   The Weather humidity.
    */
    public function setHumidity($humidity);

    /**
    * Gets wind speed.
    *
    * @return float
    *    The Weather wind speed.
    */
    public function getWindSpeed();

    /**
    * Sets wind speed.
    *
    * @param float $windSpeed
    *   The Weather wind speed.
    */
    public function setWindSpeed($windSpeed);

    /**
    * Gets Weather entity created time.
    *
    * @return integer
    *    The Weather entity created time.
    */
    public function getCreatedTime();

    /**
    * Sets Weather entity created time.
    *
    * @param integer $timestamp
    *   The Weather entity created time.
    */
    public function setCreatedTime($timestamp);

    /**
     * Gets id of related city.
     *
     * @return integer
     *    The id of related city.
     */
    public function getCityId();

    /**
     * Sets id of related city.
     *
     * @param integer $cityId
     *   The id of related city.
     */
    public function setCityId($cityId);
}
