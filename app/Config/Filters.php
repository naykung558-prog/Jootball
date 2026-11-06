<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		'authGuard' => \App\Filters\AuthGuard::class,
		'adminGuard' => \App\Filters\AdminGuard::class,
		'permissionGuard' => \App\Filters\PermissionGuard::class,
		'coachGuard' => \App\Filters\CoachGuard::class,
		'apiGuard' => \App\Filters\ApiGuard::class,
		'language' => \App\Filters\LanguageFilter::class,
		'superAcademyGuard' => \App\Filters\SuperAcademyGuard::class,
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
			// 'honeypot',
			// 'csrf',
			'language',
		],
		'after'  => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [
		'adminGuard' => ['before' => [
			'admin/dashboard*',
			'admin/tournament',
			'admin/tournament/*',
			'admin/team*',
			'admin/coach*',
			'admin/report*',
			'admin/scout*',
			'admin/result*',
			'admin/role*',
			'admin/settings*',
			'admin/api-manager*',
			'admin/access-denied',
		]],
	];
}
