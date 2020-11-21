<?php

use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;
use Tests\Support\ProjectTestCase;

class ProcessTest extends ProjectTestCase
{
	/**
	 * @var boolean
	 */
	protected $refresh = true;

	private $streamFilter;

	protected function setUp(): void
	{
		parent::setUp();

		CITestStreamFilter::$buffer = '';

		$this->streamFilter = stream_filter_append(STDOUT, 'CITestStreamFilter');
		$this->streamFilter = stream_filter_append(STDERR, 'CITestStreamFilter');
	}

	protected function tearDown(): void
	{
		stream_filter_remove($this->streamFilter);
	}

	public function testProcessAddsToDatabase()
	{
		command('reddit:process');

		$this->seeInDatabase('submissions', ['name' => 't3_jyg9ko']);
	}
}