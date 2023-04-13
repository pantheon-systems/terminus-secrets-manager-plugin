<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Exceptions\TerminusException;

/**
 * Class SiteDeleteCommand.
 *
 * Delete secret by name.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SiteDeleteCommand extends SecretBaseCommand implements SiteAwareInterface
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
        $result = $this->secretsApi->deleteSecret($site->id, $name, $env_name, $options['debug']);
        if ($result->isError()) {
            $this->log()->error('An error happened when trying to delete the secret.');
            throw new TerminusException($result->getData());
        }
        $success_message = 'Secret successfully deleted.';
        if ($env_name) {
            $success_message = 'Secret environment override deleted if it existed.';
        }
        $this->log()->notice($success_message);
    }
}
