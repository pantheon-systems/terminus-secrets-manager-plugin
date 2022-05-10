<?php
/**
 * Example of creating a model in order to improve testability of a plugin.
 *
 * To write testable code:
 *
 *   - Write methods that take parameters and return results.
 *   - Avoid side-effects.
 *
 * Use your model to determine the results of your operations. Prefer placing
 * effects (logging, network access, etc.) in the code that calls the model.
 * Above all else, avoid storing a reference to the dependency injection container,
 * or any object that contains such a reference.
 */

namespace Pantheon\TerminusHello\Model;

/**
 * Say hello to the user
 */
class Greeter
{
    protected $greetingType;

    /**
     * Construct an immutable greeter. It will always be the same kind of greeter.
     */
    public function __construct($greetingType)
    {
        $this->greetingType = $greetingType;
    }

    /**
     * Render a plesent greeting of a predetermined type.
     */
    public function render($name = 'World')
    {
        $template = $this->getGreetingTemplate($this->greetingType);
        return strtr($template, ['{name}' => $name]);
    }

    protected function getGreetingTemplate($greetingType)
    {
        $templates = $this->greetingTemplates();
        if (!isset($templates[$greetingType])) {
            $greetingType = 'hello';
        }
        return $templates[$greetingType];
    }

    protected function greetingTemplates()
    {
        return [
            'hello' => 'Hello, {name}!',
            'morning' => 'Good morning, {name}!',
            'evening' => 'Good evening, {name}!',
        ];
    }
}
