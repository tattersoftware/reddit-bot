<?php

use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;
use Tests\Support\ProjectTestCase;

class ProcessTest extends ProjectTestCase
{
	public function testProcessAddsToDatabase()
	{
		command('reddit:process');

		$this->seeInDatabase('submissions', ['name' => 't3_jyg9ko']);
	}
}
