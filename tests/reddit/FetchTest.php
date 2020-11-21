<?php

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;

class FetchTest extends CIUnitTestCase
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
}
