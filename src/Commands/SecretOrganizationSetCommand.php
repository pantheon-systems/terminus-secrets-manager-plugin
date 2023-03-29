<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Friends\OrganizationInterface;
use Pantheon\Terminus\Friends\OrganizationTrait;
use Pantheon\Terminus\org\orgAwareTrait;
use Pantheon\Terminus\org\orgAwareInterface;

/**
 * Class orgSetCommand.
 *
 * Set secret for a given org.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SecretOrganizationSetCommand extends SecretBaseCommand
{
    /**
     * Set secret for a specific org.
     *
     * @authorize
     *
     * @command secret:org:set
     * @aliases secret-org-set, secret:org:set
     *
     * @option string $type Secret type
     * @option array $scope Secret scope. Available options are ic (integrated composer), user, web, and ops.
     *   Multiple options should be specified in comma separated format. Ex: --scope=ic,ops,web.
     * @option boolean $debug Run command in debug mode
     *
     * @param string $org_id The name or UUID of a org to retrieve information on
     * @param string $name The secret name
     * @param string $value The secret value
     * @param array $options
     *
     * @usage <org> <name> <value> Set secret <name> with value <value>.
     * @usage <org> <name> <value> --debug Set given secret (debug mode).
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setSecret(
        string $org_id,
        string $name,
        string $value,
        array $options = [
        'type' => 'env',
        'scope' => 'ic',
        'debug' => false,
        ]
    ) {
        $org = $this->session()->getUser()->getOrganizationMemberships()->get($org_id)->getOrganization();
        if (empty($org)) {
            $this->log()->error('Either the org is unavailable or you dont have permission to access it..');
        }
        $this->setupRequest();
        if (
            $this->secretsApi->setSecret(
                $org->id,
                $name,
                $value,
                $options['type'],
                $options['scope'],
                $options['debug'],
                "organizations"
            )
        ) {
            $this->log()->notice('Success');
        } else {
            $this->log()->error('An error happened when trying to set the secret.');
        }
    }
}
