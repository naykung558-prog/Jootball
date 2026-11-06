<?php

if (!function_exists('get_current_language')) {
    /**
     * Get current active language
     *
     * @return string
     */
    function get_current_language(): string
    {
        $session = session();
        return $session->get('language') ?? service('request')->getLocale();
    }
}

if (!function_exists('set_language')) {
    /**
     * Set language for current session
     *
     * @param string $locale
     * @return void
     */
    function set_language(string $locale): void
    {
        $session = session();
        $session->set('language', $locale);
        service('request')->setLocale($locale);
    }
}

if (!function_exists('available_languages')) {
    /**
     * Get list of available languages
     *
     * @return array
     */
    function available_languages(): array
    {
        return [
            'th' => 'ไทย',
            'en' => 'English',
        ];
    }
}

if (!function_exists('lang_url')) {
    /**
     * Generate language switch URL
     *
     * @param string $locale
     * @return string
     */
    function lang_url(string $locale): string
    {
        return base_url("language/switch/{$locale}");
    }
}
