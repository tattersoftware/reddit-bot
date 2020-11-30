<?php

namespace Config;

use Tatter\Reddit\Config\Reddit as BaseConfig;

class Reddit extends BaseConfig
{
	/**
	 * User Agent to use for API requests. Format:
	 * <platform>:<app ID>:<version string> (by /u/<reddit username>)
	 *
	 * @var string
	 */
	public $userAgent = 'linux:com.heroesshare.bot:v1.0.0 (by /u/mgatner)';

	/**
	 * Regex pattern to match for notifications.
	 * Case-insensitive.
	 *
	 * @var string
	 */
	public $pattern = '/(Heroes.?Share)|(Hots.?Api)|(Heroes.?Profile)|(Hots.?Logs)/i';

	/**
	 * Directory for submission caching.
	 *
	 * @var string
	 */
	public $directory = WRITEPATH . 'submissions';
}
