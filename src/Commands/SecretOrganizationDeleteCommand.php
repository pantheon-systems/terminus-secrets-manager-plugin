<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\org\orgAwareTrait;
use Pantheon\Terminus\org\orgAwareInterface;

/**
 * Class SecretOrganizationDeleteCommand.
 *
 * Delete secret by name.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SecretOrganizationDeleteCommand extends SecretBaseCommand
{
    use OrgAwareTrait;

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
        $org = $this->getOrg($org_id);
        $this->warnIfEnvironmentPresent($org_id);
        $this->setupRequest();
        if ($this->secretsApi->deleteSecret($org->id, $name, $options['debug'])) {
            $this->log()->notice('Success');
        } else {
            $this->log()->error('An error happened when trying to delete the secret.');
        }
    }
}
