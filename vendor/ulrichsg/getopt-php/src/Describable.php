<?php

namespace GetOpt;

/**
 * An object that can be described
 *
 * @package GetOpt
 */
interface Describable
{
    /**
     * Returns a human readable string representation of the object
     *
     * @return string
     */
    public function describe();
}
