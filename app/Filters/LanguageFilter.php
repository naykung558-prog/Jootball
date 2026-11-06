<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LanguageFilter implements FilterInterface
{
    /**
     * Set language based on session or default
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $locale = $session->get('language');

        // If no language in session, use default from config
        if (!$locale) {
            $config = config('App');
            $locale = $config->defaultLocale;
            $session->set('language', $locale);
        }

        // Set the locale for the request
        $request->setLocale($locale);

        // Load language helper
        helper('language');
    }

    /**
     * After method (not used)
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
