<?php

use Tatter\Reddit\Structures\Kind;
use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;
use Tests\Support\ProjectTestCase;

class FetchTest extends ProjectTestCase
{
	public function testCanGetAccessToken()
	{
		$result = PasswordHandler::retrieve();

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testCanUnserializeThing()
	{
		$file     = SUPPORTPATH . 'submissions' . DIRECTORY_SEPARATOR . 't3_jxwuze';
		$contents = file_get_contents($file);

		$result = unserialize($contents);

		$this->assertInstanceOf(Thing::class, $result);
	}

	public function testFetchStoresAfterSetting()
	{
		$this->assertEmpty(cache('reddit_last_link'));

		command('reddit:fetch');
		$result = cache('reddit_last_link');

		$this->assertNotEmpty($result);
		$this->assertEquals(1, preg_match(Kind::NAME_REGEX, $result));
	}
}
