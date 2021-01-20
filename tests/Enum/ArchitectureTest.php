<?php

namespace Tests\Enum;

use Meveto\Client\Enum\Architecture;
use Meveto\Client\Exceptions\Enum\InvalidEnumValueException;
use Tests\MevetoTestCase;

/**
 * Class ArchitectureTest.
 */
class ArchitectureTest extends MevetoTestCase
{
    public function testArchitectureConstants()
    {
        // list all possible values.
        $values = Architecture::listOptions();
        // assert possible choices.
        static::assertEquals(['WEB' => 'web', 'REST' => 'rest'], $values);

        // assert constants.
        static::assertEquals('web', Architecture::WEB);
        static::assertEquals('rest', Architecture::REST);
        static::assertEquals('web', Architecture::__default);
    }

    public function testArchitectureConstantsIncludingDefault()
    {
        // list all possible values.
        $values = Architecture::listOptions(true);

        // assert possible choices.
        static::assertEquals(['__default' => 'web', 'WEB' => 'web', 'REST' => 'rest'], $values);
    }

    /**
     * Test enum instance.
     */
    public function testArchitectureEnumInstance()
    {
        // generate instance.
        $default = new Architecture();
        // assert equals.
        static::assertEquals(Architecture::__default, (string) $default);

        // generate instance.
        $web = new Architecture(Architecture::WEB);
        // assert equals.
        static::assertEquals(Architecture::WEB, (string) $web);

        // generate instance.
        $rest = new Architecture(Architecture::REST);
        // assert equals.
        static::assertEquals(Architecture::REST, (string) $rest);

        // use invalid and watch exception being thrown.
        try {
            $invalid = new Architecture('other-value');
        } catch (InvalidEnumValueException $e) {
            static::assertStringHasString('Provided value for enum is not valid.', $e->getMessage());
        }
    }
}
