<?php

use App\Entities\Submission;
use App\Models\SubmissionModel;
use Tests\Support\ProjectTestCase;

class ExecuteTest extends ProjectTestCase
{
	private int $submissionId;

	/**
	 * Create a mock Submission to have
	 * its Actions executed.
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$_SESSION = [];
		$this->submissionId = model(SubmissionModel::class)->insert($this->getSubmission());
	}

	public function testExecuteRunsActionExecute()
	{
		$this->assertArrayNotHasKey('submissions', $_SESSION);

		command('reddit:execute');

		$this->assertIsArray($_SESSION['submissions']);
		$this->assertCount(1, $_SESSION['submissions']);

		$result = $_SESSION['submissions'][0];
		$this->assertInstanceOf(Submission::class, $result);
		$this->assertEquals('heroesofthestorm', $result->subreddit);
	}

	public function testExecuteSetsExecutedAt()
	{
		command('reddit:execute');

		$submission = model(SubmissionModel::class)->find($this->submissionId);
		$this->assertInstanceOf(Submission::class, $submission);
		$this->assertNotNull($submission->executed_at, print_r($submission, true));
	}
}
