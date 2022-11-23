<?php

namespace GetOpt;

use GetOpt\ArgumentException\Invalid;

/**
 * Class Operand
 *
 * @package GetOpt
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Operand extends Argument
{
    const TRANSLATION_KEY = 'operand';

    const OPTIONAL = 0;
    const REQUIRED = 1;
    const MULTIPLE = 2;

    /** @var bool */
    protected $required;

    /** @var string */
    protected $description;

    /**
     * Operand constructor.
     *
     * @param string $name A name for the operand
     * @param int    $mode The operand mode
     */
    public function __construct($name, $mode = self::OPTIONAL)
    {
        $this->required = (bool)($mode & self::REQUIRED);
        $this->multiple = (bool)($mode & self::MULTIPLE);

        parent::__construct(null, null, $name);
    }

    /**
     * Fluent interface for constructor
     *
     * @param string $name
     * @param int    $mode
     * @return static
     */
    public static function create($name, $mode = 0)
    {
        return new static($name, $mode);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this
     */
    public function required($required = true)
    {
        $this->required = $required;
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
        parent::setValue($value);
        return $this;
    }

    /**
     * Get the current value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();
        return $value === null || $value === [] ? $this->getDefaultValue() : $value;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @deprecated will be removed in version 4
     * @see getValue
     * @codeCoverageIgnore
     */
    public function value()
    {
        return $this->getValue();
    }

    /**
     * Get a string from value
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();
        return !is_array($value) ? (string)$value : implode(',', $value);
    }
}
