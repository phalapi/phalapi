<?php

namespace GetOpt;

trait WithOperands
{
    /** @var Operand[] */
    protected $operands = [];

    /**
     * Add an array of $operands
     *
     * @param Operand[] $operands
     * @return self
     */
    public function addOperands(array $operands)
    {
        foreach ($operands as $operand) {
            $this->addOperand($operand);
        }

        return $this;
    }

    /**
     * Add an $operand
     *
     * @param Operand $operand
     * @return self
     */
    public function addOperand(Operand $operand)
    {
        if ($operand->isRequired()) {
            foreach ($this->operands as $previousOperand) {
                $previousOperand->required();
            }
        }

        if ($this->hasOperands()) {
            /** @var Operand $lastOperand */
            $lastOperand = array_slice($this->operands, -1)[0];
            if ($lastOperand->isMultiple()) {
                throw new \InvalidArgumentException(sprintf(
                    'Operand %s is multiple - no more operands allowed',
                    $lastOperand->getName()
                ));
            }
        }

        $this->operands[] = $operand;

        return $this;
    }

    /**
     * Returns the list of operands.
     *
     * @return Operand[]
     */
    public function getOperands()
    {
        return $this->operands;
    }

    /**
     * Returns the nth operand (starting with 0), or null if it does not exist.
     *
     * When $index is a string it returns the current value or the default value for the named operand.
     *
     * @param int|string $index
     * @return Operand
     */
    public function getOperand($index)
    {
        if (is_string($index)) {
            $name = $index;
            foreach ($this->operands as $operand) {
                if ($operand->getName() === $name) {
                    return $operand;
                }
            }
            return null;
        }

        return isset($this->operands[$index]) ? $this->operands[$index] : null;
    }

    /**
     * Check if operands are defined
     *
     * @return bool
     */
    public function hasOperands()
    {
        return !empty($this->operands);
    }
}
