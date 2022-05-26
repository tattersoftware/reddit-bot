<?php

use Tatter\Reddit\Structures\Kind;
use Tatter\Reddit\Structures\Thing;
use Tatter\Reddit\Tokens\PasswordHandler;
use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class FetchTest extends ProjectTestCase
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
        if (config('Reddit')->username === '') {
            $this->markTestSkipped('This test requires valid Reddit credentials.');
        }

        $this->assertEmpty(cache('rheroesofthestormnew'));

        command('reddit:fetch');
        $result = cache('rheroesofthestormnew');

        $this->assertNotEmpty($result);
        $this->assertMatchesRegularExpression(Kind::NAME_REGEX, $result);
    }
}
