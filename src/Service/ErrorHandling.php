<?php

namespace Weather\Service;

class ErrorHandling
{
    /**
     * Stops script and returns response when errors are occurred.
     *
     * @param \Exception $e
     *    Catch exception object.
     * @param string $userMessage
     *    Error message for user.
     * @param string $linkUrl
     *    URL of the link displayed with error message.
     * @param string $linkText
     *    Text of the link displayed with error message.
     * @param bool $backLink
     *    If back link is needed or not to display.
     */
    public static function dieErrorResponse(
        \Throwable $e,
        string $userMessage = 'Unexpected error.',
        int $responseCode = 500,
        string $linkUrl = '',
        string $linkText = 'Go to Home page',
        bool $backLink = true
    )
    {
        error_log($e);
        $linkUrl = $linkUrl ?: home_url();
        wp_die($userMessage, '', [
            'response' => $responseCode,
            'back_link' => $backLink,
            'link_url' => $linkUrl,
            'link_text' => $linkText,
        ]);
    }
}
