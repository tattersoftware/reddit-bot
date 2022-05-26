<?php namespace Tests\Support;

use App\Database\Seeds\InitialSeeder;
use App\Entities\Submission;
use App\Models\SubmissionModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use Config\Project;
use Tatter\Reddit\Structures\Kind;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ProjectTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;
    
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
		Factories::injectMock('config', 'Project', $config);

		// Set up the stream filter so commands don't output
		CITestStreamFilter::$buffer = '';
		$this->streamFilter         = stream_filter_append(STDOUT, 'CITestStreamFilter');
		$this->streamFilter         = stream_filter_append(STDERR, 'CITestStreamFilter');
	}

	protected function tearDown(): void
	{
	    parent::tearDown();
	    
		stream_filter_remove($this->streamFilter);

		$this->root = null;
	}

	/**
	 * Creates a Submission to test with from
	 * one of the support files.
	 *
	 * @return Submission
	 */
	protected function getSubmission($file = 't3_jxwuze'): Submission
	{
		$contents = file_get_contents(SUPPORTPATH . 'submissions' . DIRECTORY_SEPARATOR . $file);

		/** @var Kind $kind */
		$kind = unserialize($contents);

		$row = model(SubmissionModel::class)->fromKind($kind);
		$row['directive'] = 'test_directive';
		$row['match']     = 'Test Match';
		$row['excerpt']   = 'Something something Test Match foo bar bam...';

		return new Submission($row);
	}
}
