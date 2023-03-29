<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

/**
 * Class ListCommand.
 *
 * List secrets for a given site.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
class ListCommand extends SecretBaseCommand implements SiteAwareInterface
{
    use StructuredListTrait;
    use SiteAwareTrait;

    /**
     * Lists secrets for a specific site.
     *
     * @authorize
     * @filter-output
     *
     * @command secret:site:list
     * @aliases secrets, secret:list
     *
     * @field-labels
     *   name: Secret name
     *   type: Secret type
     *   value: Secret value
     *   scopes: Secret scopes
     *   env-values: Environment override values
     *   org-values: Org default values
     * @default-table-fields name,type,value,scopes
     *
     * @option boolean $debug Run command in debug mode
     *
     * @usage <site> Lists all secrets for current site.
     * @usage <site> --debug List all secrets for current site (debug mode).
     *
     * @param string $site_id The name or UUID of a site to retrieve information on
     * @param array $options
     *
     * @return RowsOfFields
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function listSecrets($site_id, array $options = ['debug' => false,])
    {
        $env_name = '';
        if (strpos($site_id, '.') !== false) {
            list($site_id, $env_name) = explode('.', $site_id, 2);
        }
        $site = $this->getSite($site_id);
        $this->setupRequest();
        $secrets = $this->secretsApi->listSecrets($site->id, $options['debug']);
        $print_options = [
            'message' => 'You have no Secrets.'
        ];
        // If the user requested secrets from a specific environment, then
        // filter down to just the secret values there.
        if (!empty($env_name)) {
            $secrets = $this->secretsForEnv($secrets, $env_name);
            $print_options = [
                'message' => "There are no environment overrides in the environment '$env_name'.",
            ];
        }
        return $this->getTableFromData($secrets, $print_options);
    }

    /**
     * @param array $secrets Complete secret data
     * @param string $env_name Name of environment to pull secrets from
     *
     * @return array Secret data containing only secrets with overrides
     *   in the specified environment.
     */
    protected function secretsForEnv(array $secrets, $env_name)
    {
        $result = [];

        foreach ($secrets as $key => $data) {
            if (array_key_exists($env_name, $data['env-values'] ?? [])) {
                $result[$key] = [
                    'name' => $key,
                    'type' => $data['type'],
                    'value' => $data['env-values'][$env_name],
                    'scopes' => $data['scopes'],
                ];
            }
        }

        return $result;
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
                if ($key == 'scopes') {
                    return implode(', ', $cellData);
                }
                if (($key == 'env-values') || ($key == 'org-values')) {
                    $rows = [];
                    foreach ($cellData as $k => $v) {
                        if ($v) {
                            $rows[] = "$k=$v";
                        }
                        else {
                            $rows[] = "$k";
                        }
                    }
                    return implode(', ', $rows);
                }
                return $cellData;
            }
        );
    }
}
