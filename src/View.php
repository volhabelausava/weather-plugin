<?php

namespace Weather;

class View
{
    /**
     * Prints prepared template.
     *
     * @param string $template
     *     Name of template file.
     * @param array $args
     *     Arguments which will be passed to template.
     */
    public static function showContent(string $template, array $args = [])
    {
        echo self::getContent($template, $args);
    }

    /**
     * Returns prepared template.
     *
     * @param string $template
     *     Name of template file.
     * @param array $args
     *     Arguments which will be passed to template.
     * @return string
     *     Template prepared to output.
     */
    public static function getContent($template, $args = [])
    {
        foreach($args as $key => $value) {
            $$key = $value;
        }
        $file = __DIR__ . "/../templates/{$template}.php";
        if (!file_exists($file)) {
            die('The file does not exist.');
        }
        ob_start();
        include($file);
        $content = ob_get_clean();
        return $content;
    }

}