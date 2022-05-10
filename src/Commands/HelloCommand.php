<?php
/**
 * This simple command shows the basics of how to add a new top-level command to Terminus.
 *
 * To add a command simply define a class as a subclass of `Pantheon\Terminus\Commands\TerminusCommand` and place it in
 * a php file inside the 'Commands' directory inside your plugin directory. The file and command class should end with
 * 'Command' in order to be found by Terminus.
 *
 * To specify what the command name should be use the `@command` tag in the actual command function DocBlock.
 *
 * This command can be invoked by running `terminus hello`
 */

/**
 * Plugins which are to be distributed should define their own namespace in order to avoid conflicts. To do so, use
 * the PSR-4 standard and add an autoload section to your composer.json.
 *
 * Development or internal-only plugins can ommit the namespace declaration and the autoload section in composer.json.
 * The command will then use the global namespace.
 */
namespace Pantheon\TerminusHello\Commands;

/**
 * It is not strictly necessary to extend the TerminusCommand class but doing so causes a number of helpful
 * objects (logger, session, etc) to be automatically provided to your class by the dependency injection container.
 */
use Pantheon\Terminus\Commands\TerminusCommand;

/**
 * Say hello to the user
 */
class HelloCommand extends TerminusCommand
{
    /**
     * Print the classic message to the log.
     *
     * @command hello
     * @param string $name Who to say "hello" to.
     * @option $first This is the first time we've said hello.
     */
    public function sayHello($name = 'World', $options = ['first' => false])
    {
        // All commands have access to a logger to output informational messages
        // to the user.
        // By default all messages at 'notice' or above are sent to STDERR.
        // The logger should not be used to display the "results" of the command.
        // If you wish to output data, make it the the return value of the command
        // function so that it is sent to STDOUT to be piped to other programs, saved
        // to a file, etc.
        // Note that the logger can do variable replacement.
        $this->log()->notice("Hello, {user}!", ['user' => $name]);
        if ($options['first']) {
            $this->log()->notice("Pleased to meet you.");
        }
    }
}
