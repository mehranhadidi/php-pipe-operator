<?php

namespace SebastiaanLuca\PipeOperator;

class Item
{
    /**
     * The current value being handled.
     *
     * @var mixed
     */
    protected $value;

    /**
     * A unique string that will be replaced with the actual value when calling the pipe method
     * with it.
     *
     * @var string
     */
    protected $identifier;

    /**
     * @param mixed $value The value you want to process.
     * @param string $identifier The identifier to replace the value with in method calls that
     *     don't take the value as first parameter.
     */
    public function __construct($value, $identifier = '$$')
    {
        $this->value = $value;
        $this->identifier = $identifier;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->pipe($name, ...$arguments);
    }

    /**
     * Perform an operation on the current value.
     *
     * @param callable|string $callback
     * @param array ...$arguments
     *
     * @return \SebastiaanLuca\PipeOperator\Item $this
     */
    public function pipe($callback, ...$arguments)
    {
        // No explicit use of the value identifier means it should be the first
        //argument to call the method with. If it does get used though, we should
        // replace any occurrence of it with the actual value.

        if (! in_array($this->identifier, $arguments, true)) {
            // Add the given item value as first parameter to call the pipe method with
            array_unshift($arguments, $this->value);
        }
        else {
            $arguments = array_map(function ($argument) {
                return $argument === $this->identifier ? $this->value : $argument;
            }, $arguments);
        }

        // Call the piped method
        $this->value = $callback(...$arguments);

        // Allow method chaining
        return $this;
    }

    /**
     * Get the current value.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }
}
