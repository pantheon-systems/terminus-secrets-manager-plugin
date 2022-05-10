<?php

namespace Pantheon\TerminusCustomerSecrets\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Exceptions\TerminusNotFoundException;
use Pantheon\Terminus\Exceptions\TerminusException;
use Pantheon\TerminusCustomerSecrets\SecretsApi\SecretsApiAwareTrait;
use Pantheon\TerminusCustomerSecrets\SecretsApi\SecretsApiAwareInterface;
use Pantheon\TerminusCustomerSecrets\SecretsApi\SecretsApi;

/**
 * Class CustomerSecretsBaseCommand
 * Base class for Terminus commands that deal with customer secrets.
 *
 * @package Pantheon\Terminus\Commands\CustomerSecrets
 */
abstract class CustomerSecretsBaseCommand extends TerminusCommand implements SecretsApiAwareInterface
{
    use SecretsApiAwareTrait;

    /**
     * Construct function to pass the required dependencies.
     */
    public function __construct()
    {
        $this->setSecretsApi(new SecretsApi());
    }
}
