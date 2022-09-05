<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Exceptions\TerminusNotFoundException;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareTrait;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareInterface;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi;
use Pantheon\Terminus\Request\RequestAwareInterface;
use Pantheon\Terminus\Request\RequestAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class SecretBaseCommand
 * Base class for Terminus commands that deal with customer secrets.
 *
 * @package Pantheon\Terminus\Commands\CustomerSecrets
 */
abstract class SecretBaseCommand extends TerminusCommand implements SecretsApiAwareInterface, RequestAwareInterface, SiteAwareInterface
{
    use SecretsApiAwareTrait;
    use RequestAwareTrait;
    use SiteAwareTrait;

    /**
     * Construct function to pass the required dependencies.
     */
    public function __construct()
    {
        $this->setSecretsApi(new SecretsApi());
    }

    protected function setupRequest()
    {
        $this->secretsApi()->setRequest($this->request());
    }
}
