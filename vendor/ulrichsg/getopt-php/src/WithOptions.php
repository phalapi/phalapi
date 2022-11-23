<?php

namespace GetOpt;

trait WithOptions
{
    /** @var Option[] */
    protected $options = [];

    /** @var Option[] */
    protected $optionMapping = [];

    /**
     * Add $options to the list of options
     *
     * $options can be a string as for phps `getopt()` function, an array of Option instances or an array of arrays.
     *
     * You can also mix Option instances and arrays. E.g.:
     * $getopt->addOptions([
     *   ['?', 'help', GetOpt::NO_ARGUMENT, 'Show this help'],
     *   new Option('v', 'verbose'),
     *   (new Option(null, 'version'))->setDescription('Print version and exit'),
     *   Option::create('q', 'quiet')->setDescription('Don\'t write any output')
     * ]);
     *
     * @see OptionParser::parseArray() for how to use arrays
     * @param string|array|Option[] $options
     * @return self
     */
    public function addOptions($options)
    {
        if (is_string($options)) {
            $options = OptionParser::parseString($options);
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('GetOpt(): argument must be string or array');
        }

        foreach ($options as $option) {
            $this->addOption($option);
        }

        return $this;
    }

    /**
     * Add $option to the list of options
     *
     * $option can also be a string in format of php`s `getopt()` function. But only the first option will be added.
     *
     * Otherwise it has to be an array or an Option instance.
     *
     * @see GetOpt::addOptions() for more details
     * @param string|array|Option $option
     * @return self
     */
    public function addOption($option)
    {
        if (!$option instanceof Option) {
            if (is_string($option)) {
                $options = OptionParser::parseString($option);
                // this is addOption - so we use only the first one
                $option = $options[0];
            } elseif (is_array($option)) {
                $option = OptionParser::parseArray($option);
            } else {
                throw new \InvalidArgumentException(sprintf(
                    '$option has to be a string, an array or an Option. %s given',
                    gettype($option)
                ));
            }
        }

        if ($this->conflicts($option)) {
            throw new \InvalidArgumentException('$option`s short and long name have to be unique');
        }

        $this->options[] = $option;
        $short = $option->getShort();
        $long = $option->getLong();
        if ($short) {
            $this->optionMapping[$short] = $option;
        }
        if ($long) {
            $this->optionMapping[$long] = $option;
        }

        return $this;
    }

    /**
     * Check if option conflicts with defined options.
     *
     * @param Option $option
     * @return bool
     */
    public function conflicts(Option $option)
    {
        $short = $option->getShort();
        $long = $option->getLong();
        return ($short && isset($this->optionMapping[$short])) || ($long && isset($this->optionMapping[$long]));
    }

    /**
     * Get all options
     *
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an option by $name
     *
     * @param string $name   Short or long name of the option
     * @return Option
     */
    public function getOption($name)
    {
        return isset($this->optionMapping[$name]) ? $this->optionMapping[$name] : null;
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        return !empty($this->options);
    }
}
