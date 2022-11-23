<?php

namespace GetOpt;

use GetOpt\ArgumentException\Invalid;

/**
 * Class Argument
 *
 * @package GetOpt
 * @author  Ulrich Schmidt-Goertz
 */
class Argument implements Describable
{
    use WithMagicGetter;

    const CLASSNAME       = __CLASS__;
    const TRANSLATION_KEY = 'argument';

    /** @var mixed */
    protected $default;

    /** @var callable */
    protected $validation;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $multiple;

    /** @var mixed */
    protected $value;

    /** @var Option */
    protected $option;

    /** @var string|callable */
    protected $validationMessage;

    /**
     * Creates a new argument.
     *
     * @param mixed    $default    Default value or NULL
     * @param callable $validation A validation function
     * @param string   $name       A name for the argument
     */
    public function __construct($default = null, callable $validation = null, $name = "arg")
    {
        if (!is_null($default)) {
            $this->setDefaultValue($default);
        }
        if (!is_null($validation)) {
            $this->setValidation($validation);
        }
        $this->name = $name;
    }

    /**
     * Set the default value
     *
     * @param mixed $value The value has to be a scalar value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDefaultValue($value)
    {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException("Default value must be scalar");
        }
        $this->default = $value;
        return $this;
    }

    /**
     * Set a validation function.
     * The function must take a string and return true if it is valid, false otherwise.
     *
     * @param callable        $callable
     * @param string|callable $message
     * @return $this
     */
    public function setValidation(callable $callable, $message = null)
    {
        $this->validation        = $callable;
        $this->validationMessage = $message;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    protected function getValidationMessage($value)
    {
        if (is_callable($this->validationMessage)) {
            return call_user_func($this->validationMessage, $this->option ?: $this, $value);
        }

        return ucfirst(sprintf(
            $this->validationMessage ?: GetOpt::translate('value-invalid'),
            $this->describe(),
            $value
        ));
    }

    /**
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     * @return $this
     */
    public function multiple($multiple = true)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * Set the option where this argument belongs to
     *
     * @param Option $option
     * @return $this
     */
    public function setOption(Option $option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     *  Internal method to set the current value
     *
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->validation && !$this->validates($value)) {
            throw new Invalid($this->getValidationMessage($value));
        }

        if ($this->isMultiple()) {
            $this->value = $this->value === null ? [ $value ] : array_merge($this->value, [ $value ]);
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * Get the current value
     *
     * @return mixed
     */
    public function getValue()
    {
        if ($this->value === null && $this->isMultiple()) {
            return [];
        }

        return $this->value;
    }

    /**
     * Check if an argument validates according to the specification.
     *
     * @param string $arg
     * @return bool
     */
    public function validates($arg)
    {
        return (bool)call_user_func($this->validation, $arg);
    }

    /**
     * Check if the argument has a validation function
     *
     * @return bool
     */
    public function hasValidation()
    {
        return isset($this->validation);
    }

    /**
     * Check whether the argument has a default value
     *
     * @return boolean
     */
    public function hasDefaultValue()
    {
        return !is_null($this->default);
    }

    /**
     * Retrieve the default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isMultiple()) {
            return $this->default ? [$this->default] : [];
        }

        return $this->default;
    }

    /**
     * Retrieve the argument name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a human readable string representation of the object
     *
     * @return string
     */
    public function describe()
    {
        return $this->option ? $this->option->describe() :
            sprintf('%s \'%s\'', GetOpt::translate(static::TRANSLATION_KEY), $this->getName());
    }
}
