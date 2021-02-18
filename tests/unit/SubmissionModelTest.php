<?php

use App\Models\SubmissionModel;
use Tatter\Reddit\Structures\Kind;
use Tests\Support\ProjectTestCase;

class SubmissionModelTest extends ProjectTestCase
{
	public function testFromKindLink()
	{
		$contents = file_get_contents(SUPPORTPATH . 'submissions' . DIRECTORY_SEPARATOR . 't3_jyg9ko');

		/** @var Kind $kind */
		$kind   = unserialize($contents);
		$result = model(SubmissionModel::class)->fromKind($kind);

		$this->assertIsArray($result);
		$this->assertEquals('Link', $result['kind']);
		$this->assertEquals('MrBanditFleshpound', $result['author']);
		$this->assertEquals('https://www.reddit.com/r/heroesofthestorm/comments/jyg9ko/my_decision_to_leave_hots_indefinitely/', $result['url']);
		$this->assertNull($result['thumbnail']);
	}

	public function testFromKindLinkValidThumbnail()
	{
		$contents = file_get_contents(SUPPORTPATH . 'submissions' . DIRECTORY_SEPARATOR . 't3_jxwuze');

		/** @var Kind $kind */
		$kind   = unserialize($contents);
		$result = model(SubmissionModel::class)->fromKind($kind);

		$this->assertIsArray($result);
		$this->assertEquals('https://a.thumbs.redditmedia.com/6E6dUdLUNYOSYI2E6gfLgXNV5I_8mfc4We-uddmVEg4.jpg', $result['thumbnail']);
	}

	public function testFromKindComment()
	{
		$contents = file_get_contents(SUPPORTPATH . 'submissions' . DIRECTORY_SEPARATOR . 't1_gnw6ff2');

		/** @var Kind $kind */
		$kind   = unserialize($contents);
		$result = model(SubmissionModel::class)->fromKind($kind);

		$this->assertIsArray($result);
		$this->assertEquals('Comment', $result['kind']);
		$this->assertEquals('faolopernando', $result['author']);
		$this->assertEquals('https://www.reddit.com/r/heroesofthestorm/comments/lm7ljl/so_based_off_of_my_experience_the_meta_is/gnw6ff2/', $result['url']);
		$this->assertNull($result['thumbnail']);
	}
}
