<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;

/**
 * Class DeleteCommand.
 *
 * Delete secret by name.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class DeleteCommand extends SecretBaseCommand implements SiteAwareInterface
{
    use SiteAwareTrait;

    /**
     * Delete given secret for a specific site.
     *
     * @authorize
     *
     * @command secret:site:delete
     * @aliases secret-delete, secret:delete
     *
     * @option boolean $debug Run command in debug mode
     *
     * @param string $siteish <site_name>, site UUID, or <site.env> for environment-specific secrets
     * @param string $name The secret name
     * @param array $options
     *
     * @usage <site[.env]> <name> Delete given secret.
     * @usage <site[.env]> <name> --debug Delete given secret (debug mode).
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function deleteSecret($siteish, string $name, array $options = ['debug' => false])
    {
        if (strpos($siteish, '.') !== false) {
            list($site_id, $env_name) = explode('.', $siteish);
        } else {
            $site_id = $siteish;
            $env_name = null;
        }

        $site = $this->getSite($site_id);
        $this->setupRequest();
        if ($this->secretsApi->deleteSecret($site->id, $name, $env_name, $options['debug'])) {
            $this->log()->notice('Success');
        } else {
            $this->log()->error('An error happened when trying to delete the secret.');
        }
    }
}
