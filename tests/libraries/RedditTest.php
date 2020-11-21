<?php namespace Tatter\Reddit\Tokens;

use CodeIgniter\Test\CIUnitTestCase;

class RedditTest extends CIUnitTestCase
{
	public function testCanGetAccessToken()
	{
		$result = PasswordHandler::retrieve();

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}
}
