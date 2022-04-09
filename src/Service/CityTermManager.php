<?php

namespace Weather\Service;

use Weather\Exception\InvalidDataException;

/**
 * Manager for city vocabulary and terms.
 */
class CityTermManager
{
    /**
     * Vocabulary id (machine_name).
     */
    const VOCABULARY_ID = 'weather_city';

    /**
     * Cookie key for city name.
     */
    const COOKIE_CITY = 'weather_city';

    /**
     * CityTermManager constructor.
     */
    public function __construct()
    {
        add_action('init', [$this, 'createCityVocabulary']);
    }

    /**
     * {@inheritdoc}
     */
    public function saveCityTerm(string $city)
    {
        $cityVocabulary = $this->getCityVocabulary();
        if (!term_exists($city, $cityVocabulary->name)) {
            wp_insert_term($city, $cityVocabulary->name);
        }
    }

    /**
     * Gets city id by its name.
     * @param string $city
     * @return int
     */
    public function getCityIdByName(string $city)
    {
        $city = get_term_by('name', $city, self::VOCABULARY_ID);
        if ($city === false) {
            throw new InvalidDataException();
        }
        return $city->term_id;
    }

    /**
     * Gets city name by its id.
     * @param string $id
     * @return string
     */
    public function getCityNameById(string $id)
    {
        $city = get_term_by('term_id', $id, self::VOCABULARY_ID);
        if (empty($city)) {
            throw new InvalidDataException();
        }
        return $city->name;
    }

    /**
     * Gets existing vocabulary or creates a new one.
     */
    protected function getCityVocabulary()
    {
        if(!taxonomy_exists(self::VOCABULARY_ID)) {
            $this->createCityVocabulary();
        }
        $cityVocabulary = get_taxonomy(self::VOCABULARY_ID);

        return $cityVocabulary;
    }

    /**
     * {@inheritdoc}
     */
    public function createCityVocabulary()
    {
        register_taxonomy(
            self::VOCABULARY_ID,
            ['page'],
            ['public' => false],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCityVocabulary()
    {
        global $wpdb;
        $sql = "SELECT terms.term_id FROM {$wpdb->base_prefix}terms as terms
                INNER JOIN {$wpdb->base_prefix}term_taxonomy as taxonomy ON terms.term_id = taxonomy.term_id
                WHERE taxonomy.taxonomy = %s
                ";
        $weatherTermIds = $wpdb->get_results($wpdb->prepare($sql, self::VOCABULARY_ID), ARRAY_A);

        foreach ($weatherTermIds as $id) {
            $deleteTerms[] = $wpdb->delete("{$wpdb->base_prefix}terms", ['term_id' => $id['term_id']], ['%d']);
            $deleteTaxonomy[] = $wpdb->delete("{$wpdb->base_prefix}term_taxonomy", ['term_id' => $id['term_id']], ['%d']);
        }
        if (empty($deleteTerms) || empty($deleteTaxonomy)) {
            wp_die('', '', ['back_link' => true, 'link_url' => home_url(), 'link_text' => 'Go to Home page']);
        }
    }
}
