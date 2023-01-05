<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareTrait;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareInterface;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi;
use Pantheon\Terminus\Request\RequestAwareInterface;
use Pantheon\Terminus\Request\RequestAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class SecretBaseCommand.
 *
 * Base class for Terminus commands that deal with secrets.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
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
        parent::__construct();

        $this->setSecretsApi(new SecretsApi());
    }

    protected function setupRequest()
    {
        $this->secretsApi()->setRequest($this->request());
    }

    /**
     * Warn use if environment is present in site_id.
     *
     * @param string $site_id
     *   Site ID.
     */
    protected function warnIfEnvironmentPresent(string $site_id)
    {
        if (preg_match('/.*\..*/', $site_id)) {
            $this->log()->warning('Note: Secrets are available for all environments of a site. If you wish to specify secrets for different environments, we recommend using prefixes.');
        }
    }
}
