<?php

namespace Weather\Entity;

/**
 * Defines the Weather entity.
 */
class Weather implements WeatherInterface
{
    /**
     * Individual entity part of database table.
     */
    const TABLE = 'weather';

    /**
     * Key for session storing.
     */
    const SESSION_NAME = 'weather';

    /**
     * Value of temperature.
     * @var float
     */
    private $temperature;

    /**
     * Value of "feels like" temperature.
     * @var float
     */
    private $feelsLikeTemperature;

    /**
     * Value of humidity.
     * @var int
     */
    private $humidity;

    /**
     * Value of wind speed.
     * @var float
     */
    private $windSpeed;

    /**
     * Unix timestamp of weather request time.
     * @var int
     */
    private $created;

    /**
     * Id of related city.
     * @var int
     */
    private $cityId;

    /**
     * Weather constructor.
     * @param float $temperature
     * @param float $feelsLikeTemperature
     * @param int $humidity
     * @param float $windSpeed
     * @param int $cityId
     */
    public function __construct(float $temperature, float $feelsLikeTemperature, int $humidity, float $windSpeed, int $cityId)
    {
        $this->temperature = $temperature;
        $this->feelsLikeTemperature = $feelsLikeTemperature;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
        $this->cityId = $cityId;
        $this->created = time();
    }

    /**
     * {@inheritdoc}
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeelsLikeTemperature()
    {
        return $this->feelsLikeTemperature;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeelsLikeTemperature($feelsLikeTemperature)
    {
        $this->feelsLikeTemperature = $feelsLikeTemperature;
    }

    /**
     * {@inheritdoc}
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * {@inheritdoc}
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;
    }

    /**
     * {@inheritdoc}
     */
    public function getWindSpeed()
    {
       return $this->windSpeed;
    }

    /**
     * {@inheritdoc}
     */
    public function setWindSpeed($windSpeed)
    {
        $this->windSpeed = $windSpeed;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->created;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($timestamp)
    {
        $this->created = $timestamp;
    }
    /**
     * {@inheritdoc}
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }
}
