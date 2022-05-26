<?php

use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class FilterTest extends ProjectTestCase
{
    public function testProcessAddsToDatabase()
    {
        command('reddit:filter');

        $this->seeInDatabase('submissions', ['name' => 't3_jyg9ko']);
    }
}
