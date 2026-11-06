<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Language extends BaseController
{
    /**
     * Switch language
     *
     * @param string $locale
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function switch($locale = 'th')
    {
        // Load language helper
        helper('language');

        // Validate locale
        $availableLanguages = available_languages();
        if (!array_key_exists($locale, $availableLanguages)) {
            $locale = 'th'; // Default to Thai if invalid locale
        }

        // Set language in session
        set_language($locale);

        // Redirect back to previous page or home
        $redirect = $this->request->getGet('redirect');
        if ($redirect && strpos($redirect, base_url()) === 0) {
            return redirect()->to($redirect);
        }

        return redirect()->back();
    }
}