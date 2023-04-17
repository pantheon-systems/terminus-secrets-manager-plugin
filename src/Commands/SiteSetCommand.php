<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Class SiteSetCommand.
 *
 * Set secret for a given site.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SiteSetCommand extends SecretBaseCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Set secret for a specific site.
     *
     * @authorize
     *
     * @command secret:site:set
     * @aliases secret-set, secret:set
     *
     * @option string $type Secret type
     * @option array $scope Secret scope. Available options are ic (integrated composer), user, web, and ops.
     *   Multiple options should be specified in comma separated format. Ex: --scope=ic,ops,web.
     * @option boolean $debug Run command in debug mode
     *
     * @param string $siteish <site_name>, site UUID, or <site.env> for environment-specific secrets
     * @param string $name The secret name
     * @param string $value The secret value
     * @param array $options
     *
     * @usage <site[.env]> <name> <value> Set secret <name> with value <value> for all environments.
     * @usage <site[.env]> <name> <value> --debug Set given secret (debug mode).
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setSecret($siteish, string $name, string $value, array $options = [
        'type' => null,
        'scope' => null,
        'debug' => false,
    ])
    {
        if (strpos($siteish, '.') !== false) {
            list($site_id, $env_name) = explode('.', $siteish);
        } else {
            $site_id = $siteish;
            $env_name = null;
        }

        $site = $this->getSite($site_id);
        $this->setupRequest();
        $result = $this->secretsApi->setSecret(
            $site->id,
            $name,
            $value,
            $env_name,
            $options['type'],
            $options['scope'],
            $options['debug']
        );
        if ($result->isError()) {
            $this->log()->error('An error happened when trying to set the secret.');
            throw new TerminusException($result->getData());
        }
        $this->log()->notice('Secret successfully set.');
    }
}
