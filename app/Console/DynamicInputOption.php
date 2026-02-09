<?php
namespace App\Console;

use Symfony\Component\Console\Input\InputOption;

class DynamicInputOption extends InputOption
{
    /** @var callable|null */
    private $descriptionCallable;
    /** @var callable|null */
    private $defaultCallable;

    public function __construct(string $name, $shortcut = null, ?int $mode = null, string $description = '', $default = null, ?callable $defaultCallable = null, ?callable $descriptionCallable = null, array|\Closure $suggestedValues = [])
    {
        $this->descriptionCallable = $descriptionCallable;
        $this->defaultCallable = $defaultCallable;

        parent::__construct($name, $shortcut, $mode, $description, $default, $suggestedValues);
    }

    public function getDescription(): string
    {
        $description = parent::getDescription();

        if (is_callable($this->descriptionCallable)) {
            $description .= ' [' . implode(', ', call_user_func($this->descriptionCallable)) . ']';
        }

        return $description;
    }

    public function getDefault(): array|string|int|float|bool|null
    {
        if (is_callable($this->defaultCallable)) {
            return call_user_func($this->defaultCallable);
        }

        return parent::getDefault();
    }
}
