<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Friends\OrganizationTrait;
use Pantheon\Terminus\org\orgAwareTrait;
use Pantheon\Terminus\org\orgAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class SecretOrganizationDeleteCommand.
 *
 * Delete secret by name.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SecretOrganizationDeleteCommand extends SecretBaseCommand
{
    /**
     * Delete given secret for a specific org.
     *
     * @authorize
     *
     * @command secret:org:delete
     * @aliases secret-org-delete, secret:org:delete
     *
     * @option boolean $debug Run command in debug mode
     *
     * @param string $org_id The name or UUID of an organization to retrieve information on
     * @param string $name The secret name
     * @param array $options
     *
     * @usage <org> <name> Delete given secret.
     * @usage <org> <name> --debug Delete given secret (debug mode).
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function deleteSecret(string $org_id, string $name, array $options = ['debug' => false])
    {
        $org = $this->session()->getUser()->getOrganizationMemberships()->get($org_id)->getOrganization();
        if (empty($org)) {
            $this->log()->error('Either the org is unavailable or you dont have permission to access it..');
        }
        $this->setupRequest();
        if ($this->secretsApi->deleteSecret($org->id, $name, null, $options['debug'], "organizations")) {
            $this->log()->notice('Success');
        } else {
            $this->log()->error('An error happened when trying to delete the secret.');
        }
    }
}
