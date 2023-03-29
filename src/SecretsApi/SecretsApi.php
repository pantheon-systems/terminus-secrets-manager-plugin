<?php

namespace Pantheon\TerminusSecretsManager\SecretsApi;

use Pantheon\Terminus\Request\RequestAwareTrait;

/**
 * Temporary Secrets API client until formal PantheonAPI client is available.
 */
class SecretsApi
{

    use RequestAwareTrait;

    /**
     * Used only for testing purposes. May be removed later.
     */
    protected $secrets = [];

    /**
     * Parses the base URI for requests.
     *
     * @return string
     */
    private function getBaseURI()
    {
        $config = $this->request()->getConfig();

        $protocol = $config->get('papi_protocol') ?? $config->get('protocol');
        $port = $config->get('papi_port') ?? $config->get('port');
        $host = $config->get('papi_host');
        if (!$host && strpos($config->get('host'), 'hermes.sandbox-') !== false) {
            $host = str_replace('hermes', 'pantheonapi', $config->get('host'));
        }
        // If host is still not set, use the default host.
        if (!$host) {
            $host = 'api.pantheon.io';
        }

        return sprintf(
            '%s://%s:%s/customer-secrets/v1',
            $protocol,
            $host,
            $port
        );
    }

    /**
     * List secrets for a given site.
     *
     * @param string $site_id
     *   Site id to get secrets for.
     * @param bool $debug
     *   Whether to return the secrets in debug mode.
     *
     * @return array
     *   Secrets for given site.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function listSecrets(string $site_id, bool $debug = false): array
    {
        if (getenv('TERMINUS_PLUGIN_TESTING_MODE')) {
            if (file_exists('/tmp/secrets.json')) {
                $this->secrets = json_decode(file_get_contents('/tmp/secrets.json'), true);
            }
            return array_values($this->secrets);
        }
        $url = sprintf('%s/sites/%s/secrets', $this->getBaseURI(), $site_id);
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->request()->session()->get('session'),
            ],
            'debug' => $debug,
        ];
        $result = $this->request()->request($url, $options);
        $data = $result->getData();
        $secrets = [];
        foreach ($data->Secrets ?? [] as $secretKey => $secretValue) {
            $secrets[] = [
                'name' => $secretKey,
                'type' => $secretValue->Type,
                'value' => $secretValue->Value ?? null,
                'scopes' => implode(', ', $secretValue->Scopes),
            ];
        }
        return $secrets;
    }

    /**
     * Set secret for a given site.
     *
     * @param string $site_id
     *   Site id to set secret for.
     * @param string $name
     *   Secret name.
     * @param string $value
     *   Secret value.
     * @param string $type
     *   Secret type.
     * @param string $scopes
     *   Secret scopes.
     * @param bool $debug
     *   Whether to return the secrets in debug mode.
     *
     * @return bool
     *   Whether saving the secret was successful or not.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function setSecret(
        string $site_id,
        string $name,
        string $value,
        string $type = '',
        string $scopes = 'ic',
        bool $debug = false
    ): bool {
        if (getenv('TERMINUS_PLUGIN_TESTING_MODE')) {
            if (file_exists('/tmp/secrets.json')) {
                $this->secrets = json_decode(file_get_contents('/tmp/secrets.json'), true);
            }
            $this->secrets[$name] = [
                'name' => $name,
                'value' => $value,
            ];
            file_put_contents('/tmp/secrets.json', json_encode($this->secrets));
            return true;
        }
        $url = sprintf('%s/sites/%s/secrets/%s', $this->getBaseURI(), $site_id, $name);
        $body = [
            'value' => $value,
        ];
        if ($type) {
            $body['type'] = $type;
        }
        if ($scopes) {
            $scopes = array_map('trim', explode(',', $scopes));
            $body['scopes'] = $scopes;
        }
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->request()->session()->get('session'),
            ],
            'json' => $body,
            'method' => 'POST',
            'debug' => $debug,
        ];
        $result = $this->request()->request($url, $options);
        return !$result->isError();
    }

    /**
     * Delete secret for a given site.
     *
     * @param string $site_id
     *   Site id to set secret for.
     * @param string $name
     *   Secret name.
     * @param bool $debug
     *   Whether to return the secrets in debug mode.
     *
     * @return bool
     *   Whether saving the secret was successful or not.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Pantheon\Terminus\Exceptions\TerminusException
     */
    public function deleteSecret(string $site_id, string $name, bool $debug = false): bool
    {
        if (getenv('TERMINUS_PLUGIN_TESTING_MODE')) {
            if (file_exists('/tmp/secrets.json')) {
                $this->secrets = json_decode(file_get_contents('/tmp/secrets.json'), true);
            }
            if (isset($this->secrets[$name])) {
                unset($this->secrets[$name]);
                file_put_contents('/tmp/secrets.json', json_encode($this->secrets));
            }
            return true;
        }

        $url = sprintf('%s/sites/%s/secrets/%s', $this->getBaseURI(), $site_id, $name);
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $this->request()->session()->get('session'),
            ],
            'method' => 'DELETE',
            'debug' => $debug,
        ];
        $result = $this->request()->request($url, $options);
        return !$result->isError();
    }
}
