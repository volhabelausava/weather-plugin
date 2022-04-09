<?php

namespace Weather;

use Weather\Entity\Weather;
use Weather\Entity\WeatherInterface;

class WeatherStorage
{
    /**
     * Full name of database table.
     * @var string
     */
    protected $tableName;

    /**
     * WeatherStorage constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $this->tableName = $wpdb->base_prefix . Weather::TABLE;
    }

    /**
     * Creates table in database after plugin has been activated.
     */
    public function createTable()
    {
        global $wpdb;
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id int UNSIGNED NOT NULL AUTO_INCREMENT,
            temperature decimal(4,2) NOT NULL,
            feels_like decimal(4,2) NOT NULL,
            humidity tinyint UNSIGNED NOT NULL,
            wind_speed decimal(4,2) NOT NULL,
            created int(10) UNSIGNED NOT NULL,
            city_id bigint UNSIGNED NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (city_id) REFERENCES {$wpdb->base_prefix}terms(term_id) ON DELETE CASCADE
        ) $charsetCollate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        //todo: how to handle errors?
        dbDelta($sql);
    }

    /**
     * Deletes table from database after plugin has been uninstalled.
     */
    public function deleteTable()
    {
        global $wpdb;
        $sql = "DROP TABLE IF EXISTS {$this->tableName}";
        $result = $wpdb->query($sql);
        if ($result === false) {
            wp_die('', '', ['back_link' => true, 'link_url' => home_url(), 'link_text' => 'Go to Home page']);
        }
    }

    /**
     * Saves weather data to database.
     *
     * @param WeatherInterface $weather
     *     Weather entity for saving.
     */
    public function save(WeatherInterface $weather)
    {
        global $wpdb;
        $data = [
            'temperature' => $weather->getTemperature(),
            'feels_like' => $weather->getFeelsLikeTemperature(),
            'humidity' => $weather->getHumidity(),
            'wind_speed' => $weather->getWindSpeed(),
            'created' => $weather->getCreatedTime(),
            'city_id' => $weather->getCityId(),
        ];
        $format = ['%f', '%f', '%d', '%f', '%d', '%d'];

        $result = $wpdb->insert($this->tableName, $data, $format);
        if ($result === false) {
            wp_die('', '', ['back_link' => true, 'link_url' => home_url(), 'link_text' => 'Go to Home page']);
        }
    }

    public function getStatisticsByCity(string $cityName)
    {
        global $wpdb;
        $sql = "
            SELECT temperature, feels_like, humidity, wind_speed, created FROM {$this->tableName} as weather
            INNER JOIN {$wpdb->base_prefix}terms as terms ON weather.city_id = terms.term_id
            WHERE terms.name = %s
            ORDER BY created
        ";
        $result = $wpdb->get_results($wpdb->prepare($sql, $cityName));

        return $result;
    }
}
