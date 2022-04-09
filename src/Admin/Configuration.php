<?php

namespace Weather\Admin;

use Weather\Exception\InvalidDataException;
use Weather\Exception\MissingDataException;
use Weather\Exception\UnauthorisedException;
use Weather\Service\CityTermManager;
use Weather\Service\ErrorHandling;
use Weather\Service\WeatherDataAPI;
use Weather\View;

/**
 * Define weather configuration options.
 */
class Configuration
{
    /**
     * Options group.
     */
    const OPTION_GROUP = 'weather_settings';

    /**
     * Weather option name for db.
     */
    const OPTION_NAME = 'weather_api_settings';

    /**
     * Options section name.
     */
    const OPTION_SECTION = 'weather_section';

    /**
     * Option name for city.
     */
    const OPTION_CITY = 'city';

    /**
     * Option name for API key.
     */
    const OPTION_KEY = 'key';

    /**
     * Set of required configurable text options.
     * @var array
     */
    protected $textOptions = [self::OPTION_CITY, self::OPTION_KEY];

    /**
     * Weather data getting service.
     */
    protected $weatherData;

    /**
     * City Term Manager.
     */
    protected $cityTermManager;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->weatherData = new WeatherDataAPI();
        $this->cityTermManager = new CityTermManager();
        add_action('admin_menu', [$this, 'showOptionsPage']);
        add_action('admin_init', [$this, 'optionsInit']);
        add_filter('plugin_action_links_' . PLUGIN, [$this, 'addSettingsLink']);
    }

    /**
     * Defines basic settings for configuration page.
     */
    public function showOptionsPage()
    {
        add_options_page(
            'Weather API options',
            'Weather API',
            'manage_options',
            self::OPTION_GROUP,
            [$this, 'getOptionsPageView']
        );
    }
    /**
     * Defines a template of configuration page.
     */
    public function getOptionsPageView()
    {
        if (current_user_can( 'manage_options' )) {
            try {
                View::showContent('admin/settings-page');
            } catch (\TypeError $e) {
                ErrorHandling::dieErrorResponse($e);
            }
        }
    }

    /**
     * Initialize form fields for configuration options.
     */
    public function optionsInit()
    {
        register_setting(self::OPTION_GROUP, self::OPTION_NAME, [$this, 'sanitize']);
        add_settings_section(
            self::OPTION_SECTION,
            '',
            [$this, 'showSectionDescription'],
            self::OPTION_GROUP
        );
        foreach ($this->textOptions as $option) {
            add_settings_field (
                $option,
                ucfirst($option),
                [$this, 'showTextField'],
                self::OPTION_GROUP,
                self::OPTION_SECTION,
                $option
            );
        }
    }

    /**
     * Sanitize options
     */
    public function sanitize($inputOptions)
    {
        $savedOptions = get_option(self::OPTION_NAME);
        foreach ($inputOptions as $key => &$option) {
            $option = sanitize_text_field($option);
            if (empty($option)) {
                $option = $savedOptions[$key];
                add_settings_error($key, '', ucfirst($key) . ' can\'t be empty.');
            }
        }
        if(!empty(get_settings_errors())) {
            return $inputOptions;
        }
        // Get standard city name from API resource.
        try {
            $inputOptions[self::OPTION_CITY] = $this->weatherData->getApiCityName([
                'city' => $inputOptions[self::OPTION_CITY],
                'key' => $inputOptions[self::OPTION_KEY]]);
            $this->cityTermManager->saveCityTerm($inputOptions[self::OPTION_CITY]);
            setcookie(CityTermManager::COOKIE_CITY, $inputOptions[self::OPTION_CITY], time() + 30 * 24 * 3600, COOKIEPATH, COOKIE_DOMAIN);
        } catch (UnauthorisedException $e) {
            ErrorHandling::dieErrorResponse($e, 'Can\'t get weather data. Invalid API key.', 401);
        } catch (InvalidDataException $e) {
            ErrorHandling::dieErrorResponse($e, 'City name is not correct.', 404);
        } catch (MissingDataException $e) {
            ErrorHandling::dieErrorResponse($e, 'Please enter city to get current weather.', 400);
        } catch (\TypeError | \Exception $e) {
                ErrorHandling::dieErrorResponse($e);
        }
        return $inputOptions;
    }

    /**
     * Defines a template of each option field.
     * @param $option
     *  Option name.
     */
    public function showTextField($option)
    {
        $options = get_option(self::OPTION_NAME);
        $optionName = self::OPTION_NAME . '[' . $option . ']';
        $optionValue = $options[$option] ?? '';
        try {
            View::showContent('admin/settings-text-field', [
                'option_name' => esc_attr($optionName),
                'option_value' => esc_attr($optionValue),
            ]);
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
    }

    /**
     * Defines a template of section description for configuration page.
     */
    public function showSectionDescription()
    {
        try {
            View::showContent('admin/settings-section-description');
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
    }

    /**
     * Adds setting link to plugin page.
     */
    public function addSettingsLink($links)
    {
        try {
            $links[] = View::getContent('admin/settings-link', [
                'settingsUrl' => admin_url('options-general.php?page=' . self::OPTION_GROUP),
            ]);
            return $links;
        } catch (\TypeError $e) {
            ErrorHandling::dieErrorResponse($e);
        }
    }

    /**
     * Deletes settings option when plugin has been uninstalled.
     */
    public static function deleteSettings()
    {
        global $wpdb;
        $result = $wpdb->delete('wp_options', ['option_name' => self::OPTION_NAME]);
        if (empty($result)) {
            wp_die('', '', ['back_link' => true, 'link_url' => home_url(), 'link_text' => 'Go to Home page']);
        }
    }
}
