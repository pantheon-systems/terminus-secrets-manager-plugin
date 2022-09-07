<?php

namespace Pantheon\TerminusSecretsManager\Tests\Functional;

use Symfony\Component\Filesystem\Filesystem;
use Pantheon\Terminus\Tests\Functional\TerminusTestBase;

/**
 * Class SecretsCommandsTest.
 *
 * @package Pantheon\Terminus\Tests\Functional
 */
class SecretsCommandsTest extends TerminusTestBase
{
    protected const SECRET_NAME = 'foosecret';
    protected const SECRET_VALUE = 'secretbar';

    /**
     * @test
     * @covers \Pantheon\TerminusSecretsManager\Commands\SetCommand
     * @covers \Pantheon\TerminusSecretsManager\Commands\ListCommand
     * @covers \Pantheon\TerminusSecretsManager\Commands\DeleteCommand
     *
     * @group secrets
     * @group short
     */
    public function testCustomerSecretsCommands()
    {

        $this->assertCommandExists('secret:list');
        $this->assertCommandExists('secret:set');
        $this->assertCommandExists('secret:delete');

        // Set secret.
        $this->terminus(sprintf(
            'secret:set %s %s %s',
            $this->getSiteName(),
            self::SECRET_NAME,
            self::SECRET_VALUE
        ));

        // List secrets.
        $secretsList = $this->terminusJsonResponse(sprintf('secret:list %s', $this->getSiteName()));
        $this->assertIsArray($secretsList);
        $this->assertNotEmpty($secretsList);
        $secretFound = false;
        foreach ($secretsList as $secret) {
            if ($secret['name'] == self::SECRET_NAME) {
                $secretFound = true;
                break;
            }
        }
        $this->assertTrue($secretFound, 'Secret not found in list.');

        // Delete secret.
        $this->terminus(sprintf('secret:delete %s %s', $this->getSiteName(), self::SECRET_NAME));

        // List secrets again.
        $secretsList = $this->terminusJsonResponse(sprintf('secret:list %s', $this->getSiteName()));
        $this->assertIsArray($secretsList);
        $secretFound = false;
        foreach ($secretsList as $secret) {
            if ($secret['name'] == self::SECRET_NAME) {
                $secretFound = true;
                break;
            }
        }
        $this->assertFalse($secretFound, 'Secret found in list after it was deleted.');
    }
}
