<?php

use App\Actions\PushAction;
use App\Entities\Submission;
use App\Models\SubmissionModel;
use Config\Services;
use Tatter\Pushover\Test\MockPushover;
use Tests\Support\ProjectTestCase;

class PushTest extends ProjectTestCase
{
	/**
	 * @var Submission
	 */
	private $submission;

	public function setUp(): void
	{
		parent::setUp();

		// Mock Pushover so nothing really sends.
		$config = config('Pushover');
		$client = Services::curlrequest([
			'base_uri'    => $config->baseUrl,
			'http_errors' => false,
		]);
		Services::injectMock('pushover', new MockPushover($config, $client));

		$this->submission = $this->getSubmission();
	}

	public function testUsesSubmissionValues()
	{
		$result = (new PushAction)->execute($this->submission);

		$this->assertInstanceOf('Tatter\Pushover\Entities\Message', $result);
		$this->assertEquals('Something something Test Match foo bar bam...', $result->message);
		$this->assertEquals('Reddit mention by nexusschoolhouse', $result->title);
	}
}
