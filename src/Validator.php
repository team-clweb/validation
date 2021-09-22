<?php

namespace Intervention\Validation;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Intervention\Validation\Validator;

class Validator
{
    protected $rules = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Static factory method
     *
     * @param  array  $rules
     * @return Validator
     */
    public static function make(array $rules): self
    {
        return new self($rules);
    }

    /**
     * Set set of rules to validate against
     *
     * @param array $rules
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Determine if the given value is valid for the current rules
     *
     * @param  string $value
     * @return bool
     */
    public function validate($value): bool
    {
        $data = ['value' => $value];
        $rules = ['value' => $this->rules];

        return $this->validation()->make($data, $rules)->passes();
    }

    /**
     * Throw exception if the given value is not valid
     *
     * @param  string $value
     * @return void
     */
    public function assert($value): void
    {
        if (! $this->validate($value)) {
            throw new Exception\ValidationException(
                sprintf(
                    'Error validating value (%s)',
                    $value
                )
            );
        }
    }

    /**
     * Create validation engine
     *
     * @return Illuminate\Validation\Factory
     */
    protected function validation(): Factory
    {
        $loader = new FileLoader(new Filesystem(), 'lang');
        $translator = new Translator($loader, 'en');
        $validation = new Factory($translator, new Container());

        return $validation;
    }

    /**
     * Magic method for static calls
     *
     * @param  string $name
     * @param  array  $arguments
     * @return boolean
     */
    public static function __callStatic(string $name, array $arguments): bool
    {
        $delegation = (new CallDelegator($name, $arguments));
        $rules = ['required', $delegation->getRule()];

        return call_user_func_array([new self($rules), $delegation->getAction()], [$delegation->getValue()]);
    }
}
