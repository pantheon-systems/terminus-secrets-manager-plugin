<?php

namespace Pantheon\TerminusSecretsManager\Tests\Functional;

use Pantheon\Terminus\Tests\Functional\TerminusTestBase;

/**
 * Class SecretsOrgCommandsTest.
 *
 * @package Pantheon\Terminus\Tests\Functional
 */
class SecretsOrgCommandsTest extends TerminusTestBase
{
    protected const SECRET_NAME = 'foosecret';
    protected const SECRET_VALUE = 'secretbar';

    /**
     * @test
     * @covers \Pantheon\TerminusSecretsManager\Commands\SiteSetCommand
     * @covers \Pantheon\TerminusSecretsManager\Commands\SiteListCommand
     * @covers \Pantheon\TerminusSecretsManager\Commands\SiteDeleteCommand
     *
     * @group secrets
     * @group short
     */
    public function testSecretsCommands()
    {

        $this->assertCommandExists('secret:org:list');
        $this->assertCommandExists('secret:org:set');
        $this->assertCommandExists('secret:org:delete');

        // Set secret.
        $this->terminus(sprintf(
            'secret:site:set %s %s %s',
            $this->getOrgName(),
            self::SECRET_NAME,
            self::SECRET_VALUE
        ));

        // List secrets.
        $secretsList = $this->terminusJsonResponse(sprintf('secret:org:list %s', $this->getSiteName()));
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
        $this->terminus(sprintf('secret:site:delete %s %s', $this->getSiteName(), self::SECRET_NAME));

        // List secrets again.
        $secretsList = $this->terminusJsonResponse(sprintf('secret:site:list %s', $this->getSiteName()));
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
