<?php

use App\Actions\EmailAction;
use App\Entities\Submission;
use App\Models\SubmissionModel;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;
use Tatter\Outbox\Models\EmailModel;
use Tests\Support\ProjectTestCase;

class EmailTest extends ProjectTestCase
{
	/**
	 * @var Submission
	 */
	private $submission;

	/**
	 * Mock Email so nothing really sends.
	 */
	public function setUp(): void
	{
		parent::setUp();

		Services::injectMock('email', new MockEmail(config('Email')));

		$this->submission = $this->getSubmission();
	}

	public function testCreatesEmail()
	{
		(new EmailAction)->execute($this->submission);

		$result = model(EmailModel::class)->findAll();

		$this->assertIsArray($result);
		$this->assertCount(1, $result);

		$email = $result[0];
		$this->assertInstanceOf('Tatter\Outbox\Entities\Email', $email);
	}

	public function testUsesDefaultValues()
	{
		(new EmailAction)->execute($this->submission);

		$email = model(EmailModel::class)->first();

		$this->assertStringContainsString('<title>Reddit Mention</title>', $email->body);
	}

	public function testUsesSubmissionValues()
	{
		(new EmailAction)->execute($this->submission);

		$email = model(EmailModel::class)->first();

		$this->assertStringContainsString('User "nexusschoolhouse" mentioned "Test Match" in the following Reddit Link.', $email->body);
	}
}
