<?php

namespace GetOpt;

/**
 * Interface CommandInterface
 *
 * @package GetOpt
 * @author Olivier Cecillon <arcesilas@neutre.email>
 */
interface CommandInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getShortDescription();

    /**
     * Returns the list of operands.
     *
     * @return Operand[]
     */
    public function getOperands();

    /**
     * Get all options
     *
     * @return Option[]
     */
    public function getOptions();
}
