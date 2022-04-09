<?php

namespace Weather\Service;

class PagesManager
{
    /**
     * Slug for main weather page (shows current weather data), used in url.
     */
    const WEATHER_SLUG = 'weather';

    /**
     * Slug for the save weather request, used in url.
     */
    const SAVE_WEATHER_SLUG = 'weather-save';

    /**
     * Slug for the change city page, used in url.
     */
    const CHANGE_CITY_SLUG = 'change-city';

    /**
     * Slug for the weather statistics page, used in url.
     */
    const WEATHER_STATISTICS_SLUG = 'weather-statistics';

    /**
     * PagesManager constructor.
     */
    public function __construct()
    {
        add_filter('template_include', [self::class, 'defineTemplate']);
    }

    /**
     * Creates the page, where current weather data will be displayed.
     * The page is created during plugin activation.
     */
    public static function addPage(string $title, string $slug)
    {
        global $user_ID;
        wp_insert_post([
            'post_type' => 'page',
            'post_title' => $title,
            'post_content' => 'Content for this page will be generated automatically.',
            'comment_status' => 'closed',
            'post_status'=> 'publish',
            'post_author' => $user_ID,
            'post_name' => $slug,
        ]);
    }

    /**
     * Deletes the page, where current weather data was displayed.
     * The page is deleted during plugin deactivation.
     */
    public static function deletePage(string $slug)
    {
        $page = get_page_by_path($slug);
        wp_delete_post($page->ID, true);
    }

    /**
     * Defines template for "current weather data" page.
     *
     * @param string $template
     * @return string
     */
    public static function defineTemplate(string $template)
    {
        if (self::isCurrentWeatherRequest() || self::isChangeCityRequest() || self::isWeatherStatisticsRequest()) {
            $template = PLUGIN_PATH . 'templates/weather.php';
        }
        return $template;
    }

    /**
     * Checks if user has requested "current weather data" page.
     *
     * @return bool
     */
    public static function isCurrentWeatherRequest()
    {
        if (is_page(self::WEATHER_SLUG)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if user has requested "save weather data" action.
     *
     * @return bool
     */
    public static function isSaveWeatherRequest()
    {
        if (
            ($_SERVER['REQUEST_URI'] === '/' . self::SAVE_WEATHER_SLUG) ||
            ($_SERVER['REQUEST_URI'] === '/' . self::SAVE_WEATHER_SLUG . '/')
        ){
            return true;
        }
        return false;
    }

    /**
     * Checks if user has requested "change city" page.
     *
     * @return bool
     */
    public static function isChangeCityRequest()
    {
        if (is_page(self::CHANGE_CITY_SLUG)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if user has requested "weather statistics" page.
     *
     * @return bool
     */
    public static function isWeatherStatisticsRequest()
    {
        if (is_page(self::WEATHER_STATISTICS_SLUG)) {
            return true;
        }
        return false;
    }
}
