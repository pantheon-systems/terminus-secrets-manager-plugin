<?php

namespace Pantheon\TerminusSecretsManager\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Friends\OrganizationsTrait;
use Pantheon\Terminus\Friends\OrganizationTrait;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareTrait;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApiAwareInterface;
use Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi;
use Pantheon\Terminus\Request\RequestAwareInterface;
use Pantheon\Terminus\Request\RequestAwareTrait;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;

/**
 * Class SecretBaseCommand.
 *
 * Base class for Terminus commands that deal with secrets.
 *
 * @package Pantheon\TerminusSecretsManager\Commands
 */
abstract class SecretBaseCommand extends TerminusCommand implements SecretsApiAwareInterface, RequestAwareInterface
{
    use SecretsApiAwareTrait;
    use RequestAwareTrait;


    /**
     * Construct function to pass the required dependencies.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setSecretsApi(new SecretsApi());
    }

    protected function setupRequest()
    {
        $this->secretsApi()->setRequest($this->request());
    }
<<<<<<< HEAD
=======

    /**
     * Warn use if environment is present in site_id.
     *
     * @param string $site_id
     *   Site ID.
     */
    protected function warnIfEnvironmentPresent(string $site_id)
    {
        if (preg_match('/.*\..*/', $site_id)) {
            $this->log()->warning('Note: Secrets are available for all environments of a site. If you wish to specify secrets for different environments, we recommend using prefixes.');
        }
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
                        } else {
                            $rows[] = "$k";
                        }
                    }
                    return implode(', ', $rows);
                }
                return $cellData;
            }
        );
    }
>>>>>>> 8d03277 (Print lists with showall correctly)
}
