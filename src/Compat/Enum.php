<?php

namespace Meveto\Client\Compat;

use Meveto\Client\Exceptions\Enum\InvalidEnumValueException;
use UnexpectedValueException;
use ReflectionClass;

/**
 * Class Enum.
 *
 * Mimic a bit SplEnum but way simpler.
 */
class Enum
{
    /**
     * @const The default value of the enum.
     */
    const __default = null;

    /**
     * @var mixed|string|null The current value of the enum.
     */
    protected $current;

    /**
     * splEnum constructor.
     *
     * @param mixed $current The initial value of the enum.
     *
     * @throws InvalidEnumValueException
     */
    public function __construct($current = null)
    {
        // set current as being either a provided value or default if missing.
        $this->setCurrent($current ?: static::__default);
    }

    /**
     * Get a list of all the constants in the enum
     *
     * @param boolean $include_default Whether to include the default value in the list or no.
     *
     * @return array                    The list of constants defined in the enum.
     */
    public static function listOptions(bool $include_default = false): array
    {
        // reflect on the enum itself.
        $reflected = new \ReflectionClass(new static(null));

        // get constants.
        $constants = $reflected->getConstants();

        // unset __default if marked as so.
        if (!$include_default) {
            unset($constants['__default']);
        }

        // return constant list.
        return $constants;
    }

    /**
     * String casting.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->current;
    }

    /**
     * Set current enum value.
     *
     * @param $current
     *
     * @throws InvalidEnumValueException
     */
    protected function setCurrent($current)
    {
        // get all constants.
        $options = (new ReflectionClass($this))->getConstants();

        // check if the value is valid.
        $validValue = in_array($current, array_values($options), true);

        // throw if invalid.
        if (!$validValue) {
            throw new InvalidEnumValueException();
        }

        // assign current value.
        $this->current = $current;
    }
}
