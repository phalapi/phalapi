<?php

namespace GetOpt;

use GetOpt\ArgumentException\Missing;
use GetOpt\ArgumentException\Unexpected;

/**
 * Class GetOpt
 *
 * @package GetOpt
 * @author  Thomas Flori <thflori@gmail.com>
 */
class GetOpt implements \Countable, \ArrayAccess, \IteratorAggregate
{
    const NO_ARGUMENT = ':noArg';
    const REQUIRED_ARGUMENT = ':requiredArg';
    const OPTIONAL_ARGUMENT = ':optionalArg';
    const MULTIPLE_ARGUMENT = ':multipleArg';

    const SETTING_SCRIPT_NAME  = 'scriptName';
    const SETTING_DEFAULT_MODE = 'defaultMode';
    const SETTING_STRICT_OPTIONS = 'strictOptions';
    const SETTING_STRICT_OPERANDS = 'strictOperands';

    use WithOptions {
        getOption as getOptionObject;
        getOptions as getOptionObjects;
    }

    use WithOperands {
        getOperand as getOperandObject;
        getOperands as getOperandObjects;
    }

    use WithMagicGetter;

    /** @var HelpInterface */
    protected $help;

    /** @var array */
    protected $settings = [
        self::SETTING_STRICT_OPTIONS => true,
        self::SETTING_STRICT_OPERANDS => false,
    ];

    /** @var int */
    protected $operandsCount = 0;

    /** @var CommandInterface[] */
    protected $commands = [];

    /** The command that is executed determined by process
     * @var CommandInterface */
    protected $command;

    /** @var string[] */
    protected $additionalOperands = [];

    /** @var array */
    protected $additionalOptions = [];

    /** @var Translator */
    protected static $translator;

    /**
     * Creates a new GetOpt object.
     *
     * The argument $options can be either a string in the format accepted by the PHP library
     * function getopt() or an array.
     *
     * @param array|string $options
     * @param array $settings
     * @link https://www.gnu.org/s/hello/manual/libc/Getopt.html GNU GetOpt manual
     */
    public function __construct($options = null, array $settings = [])
    {
        $this->set(
            self::SETTING_SCRIPT_NAME,
            isset($_SERVER['argv'][0]) ? $_SERVER['argv'][0] : (
                isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null
            )
        );
        foreach ($settings as $setting => $value) {
            $this->set($setting, $value);
        }

        if ($options !== null) {
            $this->addOptions($options);
        }
    }

    /**
     * Set $setting to $value
     *
     * @param string $setting
     * @param mixed $value
     * @return self
     */
    public function set($setting, $value)
    {
        switch ($setting) {
            case self::SETTING_DEFAULT_MODE:
                OptionParser::$defaultMode = $value;
                break;
            default:
                $this->settings[$setting] = $value;
                break;
        }
        return $this;
    }

    /**
     * Get the current value of $setting
     *
     * @param string $setting
     * @return mixed
     */
    public function get($setting)
    {
        return isset($this->settings[$setting]) ? $this->settings[$setting] : null;
    }

    /**
     * Process the given $arguments
     *
     * Sets the value for defined options, operands and the command.
     *
     * @param array|string|Arguments $arguments
     */
    public function process($arguments = null)
    {
        if ($arguments === null) {
            $arguments = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 1) : [];
            $arguments = new Arguments($arguments);
        } elseif (is_array($arguments)) {
            $arguments = new Arguments($arguments);
        } elseif (is_string($arguments)) {
            $arguments = Arguments::fromString($arguments);
        } elseif (!$arguments instanceof Arguments) {
            throw new \InvalidArgumentException(
                '$arguments has to be an instance of Arguments, an arguments string, an array of arguments or null'
            );
        }

        $setOption = function ($name, callable $getValue) {
            $option = $this->getOptionObject($name);

            if (!$option) {
                if (!$this->get(self::SETTING_STRICT_OPTIONS)) {
                    $value = $getValue() ?: 1;
                    if (isset($this->additionalOptions[$name]) &&
                        is_int($value) && is_int($this->additionalOptions[$name])
                    ) {
                        $value += $this->additionalOptions[$name];
                    }

                    $this->additionalOptions[$name] = $value;
                    return;
                } else {
                    throw new Unexpected(sprintf(self::translate('option-unknown'), $name));
                }
            }

            $option->setValue($option->getMode() !== GetOpt::NO_ARGUMENT ? $getValue($option) : null);
        };

        $setCommand = function (CommandInterface $command) {
            $this->addOptions($command->getOptions());
            $this->addOperands($command->getOperands());
            $this->command = $command;
        };

        $addOperand = function ($value) {
            $operand = $this->nextOperand();
            if ($operand) {
                $operand->setValue($value);
            } elseif ($this->get(self::SETTING_STRICT_OPERANDS)) {
                throw new Unexpected(sprintf(
                    self::translate('no-more-operands'),
                    $value
                ));
            } else {
                $this->additionalOperands[] = $value;
            }
        };

        $this->additionalOptions  = [];
        $this->additionalOperands = [];
        $this->operandsCount = 0;

        $arguments->process($this, $setOption, $setCommand, $addOperand);

        if (($operand = $this->nextOperand()) && $operand->isRequired() &&
            (!$operand->isMultiple() || count($operand->getValue()) === 0)
        ) {
            throw new Missing(sprintf(self::translate('operand-missing'), $operand->getName()));
        }
    }

    /**
     * Get an option by $name
     *
     * @param string $name   Short or long name of the option
     * @param bool   $object Get the definition object instead of the current value.
     * @return Option|mixed
     */
    public function getOption($name, $object = false)
    {
        $option = $this->getOptionObject($name);

        if ($object) {
            return $option;
        }

        if ($option) {
            return $option->getValue();
        }

        return isset($this->additionalOptions[$name]) ? $this->additionalOptions[$name] : null;
    }

    /**
     * Returns the list of options with a value.
     *
     * @return array
     */
    public function getOptions()
    {
        $result = [];

        foreach ($this->options as $option) {
            $value = $option->getValue();
            if ($value !== null) {
                $result[$option->getShort() ?: $option->getLong()] = $value;
                if ($short = $option->getShort()) {
                    $result[$short] = $value;
                }
                if ($long = $option->getLong()) {
                    $result[$long] = $value;
                }
            }
        }

        return $result + $this->additionalOptions;
    }

    /**
     * Add an array of $commands
     *
     * @param Command[] $commands
     * @return self
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
        return $this;
    }

    /**
     * Add a $command
     *
     * @param CommandInterface $command
     * @return self
     */
    public function addCommand(CommandInterface $command)
    {
        foreach ($command->getOptions() as $option) {
            if ($this->conflicts($option)) {
                throw new \InvalidArgumentException('$command has conflicting options');
            }
        }
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * Get the current or a named command.
     *
     * @param string $name
     * @return CommandInterface
     */
    public function getCommand($name = null)
    {
        if ($name !== null) {
            return isset($this->commands[$name]) ? $this->commands[$name] : null;
        }

        return $this->command;
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Check if commands are defined
     *
     * @return bool
     */
    public function hasCommands()
    {
        return !empty($this->commands);
    }

    /**
     * Get the next operand
     *
     * @return Operand
     */
    protected function nextOperand()
    {
        if (isset($this->operands[$this->operandsCount])) {
            $operand = $this->operands[$this->operandsCount];
            if (!$operand->isMultiple()) {
                $this->operandsCount++;
            }
            return $operand;
        }

        return null;
    }

    /**
     * Returns the list of operands. Must be invoked after parse().
     *
     * @return array
     */
    public function getOperands()
    {
        $operandValues = [];
        foreach ($this->getOperandObjects() as $operand) {
            $value = $operand->getValue();

            if ($value === null) {
                continue;
            }

            if ($operand->isMultiple()) {
                $operandValues = array_merge($operandValues, $value);
            } else {
                $operandValues[] = $value;
            }
        }

        return array_merge($operandValues, $this->additionalOperands);
    }

    /**
     * Returns the nth operand (starting with 0), or null if it does not exist.
     *
     * When $index is a string it returns the current value or the default value for the named operand.
     *
     * @param int|string $index
     * @return mixed
     */
    public function getOperand($index)
    {
        $operand = $this->getOperandObject($index);
        if ($operand) {
            return $operand->getValue();
        } elseif (is_int($index)) {
            $i = $index - count($this->operands);
            return $i >= 0 && isset($this->additionalOperands[$i]) ? $this->additionalOperands[$i] : null;
        }

        return null;
    }

    /**
     * Define a custom Help object
     *
     * @param HelpInterface $help
     * @return self
     * @codeCoverageIgnore trivial
     */
    public function setHelp(HelpInterface $help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * @param string $language
     * @return bool Whether the language change was successful
     * @deprecated use GetOpt::setLang($language) instead
     */
    public function setHelpLang($language = 'en')
    {
        return self::setLang($language);
    }

    /**
     * Translate $key
     *
     * Returns the translation for the given key; falls back to English if it is
     * not localized in the configured language, and ultimately returns the key
     * itself should it not exist in the English language file.
     *
     * @param string $key
     * @return string
     */
    public static function translate($key)
    {
        return self::getTranslator()->translate($key);
    }

    /**
     * Get the translator instance
     *
     * @return Translator
     */
    protected static function getTranslator()
    {
        if (self::$translator === null) {
            self::$translator = new Translator;
        }
        return self::$translator;
    }

    /**
     * Set language to $language
     *
     * The language can either be a known language from resources/localization (feel free to contribute your language)
     * or a path to a file that returns an array like the files in resources/localization.
     *
     * @param string $language
     * @return bool Whether the language change was successful
     */
    public static function setLang($language)
    {
        return self::getTranslator()->setLanguage($language);
    }

    /**
     * Get the current Help instance
     *
     * @return HelpInterface
     */
    public function getHelp()
    {
        if (!$this->help) {
            $this->help = new Help();
        }

        return $this->help;
    }

    /**
     * Returns an usage information text generated from the given options.
     *
     * The $padding got removed due to refactoring. Help is an own class now. You can change the layout by using a
     * custom template or using a custom help formatter (has to implement HelpInterface)
     *
     * @see Help for setting a custom template
     * @see HelpInterface for creating an custom help formatter
     * @param array $data This data will be forwarded to HelpInterface::render and is available in templates
     * @return string
     */
    public function getHelpText(array $data = [])
    {
        return $this->getHelp()->render($this, $data);
    }

    // backward compatibility

    /**
     * Set script name manually
     *
     * @param string $scriptName
     * @return self
     * @deprecated Use `GetOpt::set(GetOpt::SETTING_SCRIPT_NAME, $scriptName)` instead
     * @codeCoverageIgnore
     */
    public function setScriptName($scriptName)
    {
        return $this->set(self::SETTING_SCRIPT_NAME, $scriptName);
    }

    /**
     * Process $arguments or $_SERVER['argv']
     *
     * These function is an alias for process now. Parse was not the correct verb for what
     * the function is currently doing.
     *
     * @deprecated Use `GetOpt::process($arguments)` instead
     * @param mixed $arguments optional ARGV array or argument string
     * @codeCoverageIgnore
     */
    public function parse($arguments = null)
    {
        $this->process($arguments);
    }

    // array functions

    /**
     * @inheritDoc
     *
     * @return \Traversable
     * @throws \Exception
     */
    public function getIterator()
    {
        $result = [];

        foreach ($this->options as $option) {
            if (($value = $option->getValue()) !== null) {
                $name = $option->getLong() ?: $option->getShort();
                $result[$name] = $value;
            }
        }

        return new \ArrayIterator($result + $this->additionalOptions);
    }

    /**
     * @inheritDoc
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $option = $this->getOptionObject($offset);
        if ($option && $option->getValue() !== null) {
            return true;
        }

        return isset($this->additionalOptions[$offset]);
    }

    /**
     * @inheritDoc
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $option = $this->getOptionObject($offset);
        if ($option) {
            return $option->getValue();
        }

        return isset($this->additionalOptions[$offset]) ? $this->additionalOptions[$offset] : null;
    }

    /**
     * @inheritDoc
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Read only array access');
    }

    /**
     * @inheritDoc
     *
     * @param mixed $offset
     * @throws \LogicException
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Read only array access');
    }

    /**
     * @inheritDoc
     *
     * @return int
     * @throws \Exception
     */
    public function count()
    {
        return $this->getIterator()->count();
    }
}
