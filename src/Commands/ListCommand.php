<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Pantheon\Terminus\Commands\StructuredListTrait;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

/**
 * Class ListCommand
 * List secrets for a given site.
 *
 * @package Pantheon\Terminus\Commands\CustomerSecrets
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
     * @command secret:list
     * @aliases secrets
     *
     * @field-labels
     *   name: Secret name
     *   type: Secret type
     *   value: Secret value
     *   scopes: Secret scopes
     *
     * @option boolean $debug Run command in debug mode
     * @param string $site_id The name or UUID of a site to retrieve information on
     * @param array $options
     * @return RowsOfFields
     *
     * @usage <site> Lists all secrets for current site.
     * @usage <site> --debug List all secrets for current site (debug mode).
     *
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function listSecrets($site_id, array $options = ['debug' => false,])
    {
        $site = $this->getSite($site_id);
        $this->setupRequest();
        $secrets = $this->secretsApi->listSecrets($site->id, $options['debug']);
        $print_options = [
            'message' => 'You have no Customer Secrets.'
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
    protected function getTableFromData(
        array $data,
        array $options = [],
        $date_attributes = []
    ) {
        if (count($data) === 0) {
            $message = $options['message'];
            $options = isset($options['message_options']) ? $options['message_options'] : [];
            $this->log()->warning($message, $options);
        }
        $table = new RowsOfFields($data);
        return $table;
    }
}
