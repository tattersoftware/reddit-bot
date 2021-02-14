<?php

use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;
use Tests\Support\ProjectTestCase;

class FilterTest extends ProjectTestCase
{
	public function testProcessAddsToDatabase()
	{
		command('reddit:filter');

		$this->seeInDatabase('submissions', ['name' => 't3_jyg9ko']);
	}
}
