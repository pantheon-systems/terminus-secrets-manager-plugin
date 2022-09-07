<?php

namespace Pantheon\TerminusSecretsManager\SecretsApi;

/**
 * Class SecretsApiAwareTrait.
 *
 * @package Pantheon\Terminus\Request
 */
trait SecretsApiAwareTrait
{
    /**
     * @var \Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi
     */
    protected $secretsApi;

    /**
     * Inject a pre-configured SecretsApi object.
     *
     * @param \Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi $secretsApi
     */
    public function setSecretsApi(SecretsApi $secretsApi): void
    {
        $this->secretsApi = $secretsApi;
    }

    /**
     * Return the SecretsApi object.
     *
     * @return \Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi
     */
    public function secretsApi(): SecretsApi
    {
        return $this->secretsApi;
    }
}
