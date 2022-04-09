<?php
namespace Weather\Controller;

use Weather\Admin\Configuration;
use Weather\Entity\Weather;
use Weather\Exception\InvalidDataException;
use Weather\Exception\MissingDataException;
use Weather\Exception\UnauthorisedException;
use Weather\Service\CityTermManager;
use Weather\Service\ErrorHandling;
use Weather\Service\PagesManager;
use Weather\Service\WeatherDataAPI;
use Weather\View;
use Weather\WeatherStorage;

class WeatherController
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
     * Weather Storage.
     */
    protected $weatherStorage;

    /**
     * Action to get data for "current weather data" request.
     */
    protected $getDataAction;

    /**
     * WeatherController constructor.
     */
    public function __construct()
    {
        $this->weatherData = new WeatherDataAPI();
        $this->cityTermManager = new CityTermManager();
        $this->weatherStorage = new WeatherStorage();
        $this->getDataAction = new GetDataAction();
        add_filter('the_content', [$this, 'getData']);
        add_filter('the_content', [$this, 'showChangeCityForm']);
        add_filter('the_content', [$this, 'showStatistics']);
        add_action('init', [$this, 'saveData']);
        add_action('admin_post_change_city', [$this, 'handleChangeCityForm']);
        add_action('admin_post_nopriv_change_city', [$this, 'handleChangeCityForm']);
    }

    /**
     * Gets weather data for "current weather data" request.
     *
     * @param string $content
     *     Default content for "current weather data" page.
     * @return string
     *     HTML markup ready to output in template.
     */
    public function getData(string $content)
    {
        if (!PagesManager::isCurrentWeatherRequest()) {
            return $content;
        }
        return ($this->getDataAction)();
    }

    /**
     * Saves weather data to database.
     */
    public function saveData()
    {
        if (!PagesManager::isSaveWeatherRequest()) {
            return;
        }
        if (current_user_can('manage_options') && isset($_SESSION[Weather::SESSION_NAME])) {
            $weather = $_SESSION[Weather::SESSION_NAME];
            try {
                $this->weatherStorage->save($weather);
            } catch (\TypeError $e) {
                ErrorHandling::dieErrorResponse($e);
            }
        }
        wp_redirect(home_url( '/' . PagesManager::WEATHER_SLUG));
        exit();
    }

    /**
     * Gets data for "change city" request.
     *
     * @param string $content
     *     Default content for "current weather data" page.
     * @return string
     *     HTML markup ready to output in template.
     */
    public function showChangeCityForm(string $content)
    {
        if (!PagesManager::isChangeCityRequest()) {
            return $content;
        }
        $options = get_option(Configuration::OPTION_NAME);
        $defaultCity = esc_attr($_COOKIE[CityTermManager::COOKIE_CITY]) ??
            esc_attr($options[Configuration::OPTION_CITY]);
        $redirect = $_SERVER['HTTP_REFERER'] ?: home_url( '/' . PagesManager::WEATHER_SLUG);
        return View::getContent('change-city-form', [
            'defaultCity' => $defaultCity ?? '',
            'redirect' => $redirect,
        ]);
    }

    /**
     * Handles the data from "change city" form.
     */
    public function handleChangeCityForm()
    {
        if (isset($_POST['city'])) {
            $city = sanitize_text_field($_POST['city']);
            try {
                $cityApi = $this->weatherData->getApiCityName(['city' => $city]);
                $this->cityTermManager->saveCityTerm($cityApi);
                setcookie(CityTermManager::COOKIE_CITY, $cityApi, time() + 30 * 24 * 3600, COOKIEPATH, COOKIE_DOMAIN);
            } catch (UnauthorisedException $e) {
                ErrorHandling::dieErrorResponse($e, 'Can\'t get weather data. Invalid API key.', 401);
            } catch (InvalidDataException $e) {
                ErrorHandling::dieErrorResponse($e, 'City name is not correct.', 404);
            } catch (MissingDataException $e) {
                ErrorHandling::dieErrorResponse($e, 'Please enter city to get current weather.', 400);
            } catch (\TypeError | \Exception $e) {
                ErrorHandling::dieErrorResponse($e);
            }
        }
        $redirect = esc_url($_POST['redirect']) ?? home_url( '/' . PagesManager::WEATHER_SLUG);
        wp_redirect($redirect);
        exit();
    }

    /**
     * Gets statistics of weather data for the city from current weather page.
     *
     * @param string $content
     *     Default content for "statistics of weather data" page.
     * @return string
     *     HTML markup ready to output in template.
     */
    public function showStatistics(string $content)
    {
        if (!PagesManager::isWeatherStatisticsRequest()) {
            return $content;
        }
        $options = get_option(Configuration::OPTION_NAME);
        $cityName = esc_attr($_COOKIE[CityTermManager::COOKIE_CITY]
                    ?? esc_attr($options[Configuration::OPTION_CITY]));
        try {
            $statisticsData = $this->weatherStorage->getStatisticsByCity($cityName);
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
        foreach ($statisticsData as &$item) {
            $item->created = wp_date('m.d.Y H:i:s', $item->created);
        }
        return View::getContent('statistics', [
            'cityName' => $cityName,
            'statisticsData' => $statisticsData
        ]);
    }
}
