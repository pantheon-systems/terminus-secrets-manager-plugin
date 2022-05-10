<?php
/**
 * This variation on the Hello command shows how use the `@authenticated`
 * attribute to signal Terminus to require an authenticated session to
 * use this command.
 */

namespace Pantheon\TerminusHello\Commands;

use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\TerminusHello\Model\Greeter;

/**
 * Say hello to the user
 */
class AuthHelloCommand extends TerminusCommand
{
    /**
     * Say hello
     *
     * @command auth:hello
     *
     * @authenticated
     */
    public function sayHello($options = ['type' => 'hello'])
    {
        $name = $this->getAuthenticatedUserName();
        $greeter = new Greeter($options['type']);
        $this->log()->notice($greeter->render($name));
    }

    protected function getAuthenticatedUserName()
    {
        // Commands can retrieve information about the currently logged in user
        // by calling the `session` function to get a copy of the session object.
        $user = $this->session()->getUser();
        if (!$user) {
            return 'Anonymous';
        }
        // For efficiency the logged in user's details are not automatically
        // fetched from the API. We call `fetch` on the object to get the
        // user's name.
        $user->fetch();
        return $user->getName();
    }
}
