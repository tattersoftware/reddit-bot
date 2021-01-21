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
}
