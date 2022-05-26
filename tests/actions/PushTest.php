<?php

use App\Actions\PushAction;
use App\Entities\Submission;
use Config\Services;
use Tatter\Pushover\Test\MockPushover;
use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class PushTest extends ProjectTestCase
{
    private Submission $submission;

    protected function setUp(): void
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
        $result = (new PushAction())->execute($this->submission);

        $this->assertInstanceOf('Tatter\Pushover\Entities\Message', $result);
        $this->assertSame('Something something Test Match foo bar bam...', $result->message);
        $this->assertSame('Reddit mention by nexusschoolhouse', $result->title);
    }
}
