<?php namespace Tests\Support;

use App\Database\Seeds\InitialSeeder;
use CodeIgniter\Config\Config;
use CodeIgniter\Test\CIDatabaseTestCase;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use Config\Project;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ProjectTestCase extends CIDatabaseTestCase
{
	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * @var vfsStreamDirectory|null
	 */
	protected $root;

	/**
	 * @var resource
	 */
	private $streamFilter;

	/**
	 * The seed file(s) used for all tests within this test case.
	 * Should be fully-namespaced or relative to $basePath
	 *
	 * @var string|array
	 */
	protected $seed = InitialSeeder::class;

	/**
	 * The path to the seeds directory.
	 * Allows overriding the default application directories.
	 *
	 * @var string
	 */
	protected $basePath = APPPATH . 'Database';

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

	/**
	 * Mocks the configuration file and buffers output to the stream filter
	 */
	protected function setUp(): void
	{
		parent::setUp();

		// Start the virtual filesystem
		$this->root = vfsStream::setup();
        vfsStream::copyFromFileSystem(SUPPORTPATH . 'submissions', $this->root);

		// Configure for testing
		$config                  = new Project();
		$config->submissionsPath = $this->root->url();
		Config::injectMock('Project', $config);

		// Set up the stream filter so commands don't output
		CITestStreamFilter::$buffer = '';
		$this->streamFilter         = stream_filter_append(STDOUT, 'CITestStreamFilter');
		$this->streamFilter         = stream_filter_append(STDERR, 'CITestStreamFilter');
	}

	protected function tearDown(): void
	{
		stream_filter_remove($this->streamFilter);

		$this->root = null;
	}
}
