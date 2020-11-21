<?php namespace Tests\Support;

use CodeIgniter\Config\Config;
use CodeIgniter\Test\CIDatabaseTestCase;
use Config\Reddit as RedditConfig;

class ProjectTestCase extends CIDatabaseTestCase
{
	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = false;

	/**
	 * The namespace(s) to help us find the migration classes.
	 * Empty is equivalent to running `spark migrate -all`.
	 * Note that running "all" runs migrations in date order,
	 * but specifying namespaces runs them in namespace order (then date)
	 *
	 * @var string|array|null
	 */
	protected $namespace = null;

	/**
	 * @var RedditConfig
	 */
	protected $config;

	protected function setUp(): void
	{
		parent::setUp();

		// Configure for testing
		$config = new RedditConfig();
		$config->directory = SUPPORTPATH . 'submissions';
		Config::injectMock('Reddit', $config);
	}
}
