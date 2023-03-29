<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Friends\OrganizationInterface;
use Pantheon\Terminus\Friends\OrganizationTrait;
use Pantheon\Terminus\org\orgAwareTrait;
use Pantheon\Terminus\org\orgAwareInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class SecretOrganizationListCommand.
 *
 * List secrets for a given org.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class SecretOrganizationListCommand extends SecretBaseCommand
{
    /**
     * Lists secrets for a specific org.
     *
     * @authorize
     * @filter-output
     *
     * @command secret:org:list
     * @aliases secrets, secret:list
     *
     * @field-labels
     *   name: Secret name
     *   type: Secret type
     *   value: Secret value
     *   scopes: Secret scopes
     *
     * @option boolean $debug Run command in debug mode
     *
     * @usage <org> Lists all secrets for current org.
     * @usage <org> --debug List all secrets for current org (debug mode).
     *
     * @param string $org_id The name or UUID of a org to retrieve information on
     * @param array $options
     *
     * @return RowsOfFields
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function listSecrets(string $org_id, array $options = ['debug' => false,])
    {
        $org = $this->session()->getUser()->getOrganizationMemberships()->get($org_id)->getOrganization();
        if (empty($org)) {
            $this->log()->error('Either the org is unavailable or you dont have permission to access it..');
        }
        $this->warnIfEnvironmentPresent($org_id);
        $this->setupRequest();
        $secrets = $this->secretsApi->listSecrets($org->id, $options['debug'], "organizations");
        $print_options = [
            'message' => 'You have no Secrets.'
        ];
        return $this->getTableFromData($secrets, $print_options);
    }

    /**
     * @param array $data Data already serialized (i.e. not a TerminusCollection)
     * @param array $options Elements as follow
     *        string $message Message to emit if the collection is empty.
     *        array $message_options Values to interpolate into the error message.
     *        function $sort A function to sort the data using
     * @return RowsOfFields Returns a RowsOfFields-type object with applied filters
     */
    protected function getTableFromData(array $data, array $options = [])
    {
        if (count($data) === 0) {
            $message = $options['message'];
            $options = $options['message_options'] ?? [];
            $this->log()->warning($message, $options);
        }

        return (new RowsOfFields($data))->addRendererFunction(
            function ($key, $cellData) {
                if ($key == 'value' && !$cellData) {
                    return '[REDACTED]';
                }
                return $cellData;
            }
        );
    }
}
