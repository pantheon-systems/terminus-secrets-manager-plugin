<?php

namespace Pantheon\TerminusCustomerSecrets\SecretsApi;

use Pantheon\Terminus\Request\RequestAwareTrait;

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
        return sprintf(
            '%s://%s:%s',
            $config->get('papi_protocol'),
            $config->get('papi_host'),
            $config->get('papi_port')
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
                'value' => $secretValue,
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
     * @param array $scopes
     *   Secret scopes.
     * @param bool $debug
     *   Whether to return the secrets in debug mode.
     *
     * @return bool
     *   Whether saving the secret was successful or not.
     */
    public function setSecret(
        string $site_id,
        string $name,
        string $value,
        string $type = 'variable',
        array $scopes = ['integrated-composer'],
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
        }
        return true;
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
        }
        return true;
    }
}
