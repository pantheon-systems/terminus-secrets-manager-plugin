<?php

namespace Pantheon\TerminusSecretsManager\SecretsApi;

/**
 * Interface SecretsApiAwareInterface.
 *
 * @package Pantheon\Terminus\SecretsApi
 */
interface SecretsApiAwareInterface
{
    /**
     * Inject a pre-configured SecretsApi object.
     *
     * @param \Pantheon\TerminusSecretsManager\SecretsApi\SecretsApi $secretsApi
     */
    public function setSecretsApi(SecretsApi $secretsApi): void;

    /**
     * Return the SecretsApi object.
     *
     * @return SecretsApi
     */
    public function secretsApi(): SecretsApi;
}
