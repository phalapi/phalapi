<?php

namespace GetOpt;

/**
 * Class Command
 *
 * @package GetOpt
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Command implements CommandInterface
{
    use WithOptions, WithOperands, WithMagicGetter;

    /** @var string */
    protected $name;

    /** @var string */
    protected $shortDescription;

    /** @var string */
    protected $longDescription;

    /** @var mixed */
    protected $handler;

    /**
     * Command constructor.
     *
     * @param string $name
     * @param mixed  $handler
     * @param mixed  $options
     */
    public function __construct($name, $handler, $options = null)
    {
        $this->setName($name);
        $this->handler = $handler;

        if ($options !== null) {
            $this->addOptions($options);
        }
    }

    /**
     * Fluent interface for constructor so commands can be added during construction
     *
     * @param string $name
     * @param mixed  $handler
     * @param mixed  $options
     * @return static
     */
    public static function create($name, $handler, $options = null)
    {
        return new static($name, $handler, $options);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        if (empty($name) || preg_match('/(^| )-/', $name)) {
            throw new \InvalidArgumentException(sprintf(
                'Command name has to be an alphanumeric string not starting with dash, found \'%s\'',
                $name
            ));
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $handler
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param string $longDescription
     * @return $this
     */
    public function setDescription($longDescription)
    {
        $this->longDescription = $longDescription;
        if ($this->shortDescription === null) {
            $this->shortDescription = $longDescription;
        }
        return $this;
    }

    /**
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
        if ($this->longDescription === null) {
            $this->longDescription = $shortDescription;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @deprecated will be removed in version 4
     * @see getName
     * @codeCoverageIgnore
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @deprecated will be removed in version 4
     * @see getHandler
     * @codeCoverageIgnore
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->longDescription;
    }

    /**
     * @deprecated will be removed in version 4
     * @see getDescription
     * @codeCoverageIgnore
     */
    public function description()
    {
        return $this->longDescription;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @deprecated will be removed in version 4
     * @see getShortDescription
     * @codeCoverageIgnore
     */
    public function shortDescription()
    {
        return $this->shortDescription;
    }
}
