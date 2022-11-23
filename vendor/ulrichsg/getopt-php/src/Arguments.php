<?php

namespace GetOpt;

use GetOpt\ArgumentException\Unexpected;

/**
 * Class Arguments
 *
 * @package GetOpt
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Arguments
{
    /** @var string[] */
    protected $arguments;

    /**
     * Create an Arguments object
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Process the arguments for $getopt
     *
     * Stores operands using $addOperand callback.
     *
     * @param GetOpt   $getopt
     * @param callable $setOption
     * @param callable $setCommand
     * @param callable $addOperand
     * @return bool
     */
    public function process(GetOpt $getopt, callable $setOption, callable $setCommand, callable $addOperand)
    {
        $operands = [];
        while (($arg = array_shift($this->arguments)) !== null) {
            if ($this->isMeta($arg)) {
                // everything from here are operands
                $this->flushOperands($operands, $addOperand);
                $this->flushOperands($this->arguments, $addOperand);
                return true;
            }

            if ($this->isValue($arg)) {
                $operands[] = $arg;
                if (count($getopt->getOperands()) === 0 && $command = $getopt->getCommand(implode(' ', $operands))) {
                    $setCommand($command);
                    $operands = [];
                }
            } else {
                $this->flushOperands($operands, $addOperand);
            }

            if ($this->isLongOption($arg)) {
                $setOption($this->longName($arg), function (Option $option = null) use ($arg) {
                    return $this->value($arg, null, $option);
                });
                continue;
            }

            // the only left is short options
            foreach ($this->shortNames($arg) as $name) {
                $requestedValue = false;
                $setOption($name, function (Option $option = null) use ($arg, $name, &$requestedValue) {
                    $requestedValue = true;
                    return $this->value($arg, $name, $option);
                });

                if ($requestedValue) {
                    // when there is a value it was the last option
                    break;
                }
            }
        }

        $this->flushOperands($operands, $addOperand);

        return true;
    }

    /**
     * Add operands and reset the array
     *
     * @param array    $operands
     * @param callable $addOperand
     */
    protected function flushOperands(array &$operands, callable $addOperand)
    {
        foreach ($operands as $operand) {
            $addOperand($operand);
        }

        $operands = [];
    }

    /**
     * Check if $arg is an option
     *
     * @param string $arg
     * @return bool
     */
    protected function isOption($arg)
    {
        return !$this->isValue($arg) && !$this->isMeta($arg);
    }

    /**
     * Check if $arg is a value
     *
     * @param string $arg
     * @return bool
     */
    protected function isValue($arg)
    {
        return (empty($arg) || $arg === '-' || 0 !== strpos($arg, '-'));
    }

    /**
     * Check if $arg is meta '--'
     *
     * @param string $arg
     * @return bool
     */
    protected function isMeta($arg)
    {
        return $arg && $arg === '--';
    }

    /**
     * Check if $arg is a long option
     *
     * @param $arg
     * @return bool
     */
    protected function isLongOption($arg)
    {
        return $this->isOption($arg) && $arg[1] === '-';
    }

    /**
     * Get the long option name from $arg
     *
     * @param string $arg
     * @return string
     */
    protected function longName($arg)
    {
        $name = substr($arg, 2);
        $p    = strpos($name, '=');
        return $p ? substr($name, 0, $p) : $name;
    }

    /**
     * Get all short option names from $arg
     *
     * @param string $arg
     * @return string[] (single character string multi byte safe)
     */
    protected function shortNames($arg)
    {
        if (!$this->isOption($arg) || $this->isLongOption($arg)) {
            return [];
        }

        return array_map(function ($i) use ($arg) {
            return mb_substr($arg, $i, 1);
        }, range(1, mb_strlen($arg) -1));
    }

    /**
     * Get the value for an option
     *
     * $name might be the short name to separate the value from the argument in `-abcppassword`.
     *
     * Returns the value inside $arg or the next argument when it is a value.
     *
     * @param string $arg
     * @param string $name
     * @param Option $option
     * @return string
     */
    protected function value($arg, $name = null, Option $option = null)
    {
        $p = strpos($arg, $this->isLongOption($arg) ? '=' : $name);
        if ($this->isLongOption($arg) && $p || !$this->isLongOption($arg) && $p < strlen($arg)-1) {
            return substr($arg, $p+1);
        }

        if (!empty($this->arguments) && (
                $option && in_array($option->getMode(), [GetOpt::REQUIRED_ARGUMENT, GetOpt::MULTIPLE_ARGUMENT]) ||
                $this->isValue($this->arguments[0])
            )) {
            return array_shift($this->arguments);
        }

        return null;
    }

    /**
     * Parse arguments from argument string
     *
     * @param string $argsString
     * @return Arguments
     */
    public static function fromString($argsString)
    {
        $argv = [ '' ];
        $argsString = trim($argsString);
        $argc = 0;

        if (empty($argsString)) {
            return new self([]);
        }

        $state = 'n'; // states: n (normal), d (double quoted), s (single quoted)
        for ($i = 0; $i < strlen($argsString); $i++) {
            $char = $argsString[$i];
            switch ($state) {
                case 'n':
                    if ($char === '\'') {
                        $state = 's';
                    } elseif ($char === '"') {
                        $state = 'd';
                    } elseif (in_array($char, [ "\n", "\t", ' ' ])) {
                        $argc++;
                        $argv[$argc] = '';
                    } else {
                        $argv[$argc] .= $char;
                    }
                    break;

                case 's':
                    if ($char === '\'') {
                        $state = 'n';
                    } elseif ($char === '\\') {
                        $i++;
                        $argv[$argc] .= $argsString[$i];
                    } else {
                        $argv[$argc] .= $char;
                    }
                    break;

                case 'd':
                    if ($char === '"') {
                        $state = 'n';
                    } elseif ($char === '\\') {
                        $i++;
                        $argv[$argc] .= $argsString[$i];
                    } else {
                        $argv[$argc] .= $char;
                    }
                    break;
            }
        }

        return new self($argv);
    }
}
