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
     * @param string $site_id The name or UUID of a site to retrieve information on
     * @param string $name The secret name
     * @param array $options
     *
     * @usage <site> <name> Delete given secret.
     * @usage <site> <name> --debug Delete given secret (debug mode).
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function deleteSecret($site_id, string $name, array $options = ['debug' => false])
    {
        $site = $this->getSite($site_id);
        // TODO: respect environment
        $this->setupRequest();
        if ($this->secretsApi->deleteSecret($site->id, $name, $options['debug'])) {
            $this->log()->notice('Success');
        } else {
            $this->log()->error('An error happened when trying to delete the secret.');
        }
    }
}
