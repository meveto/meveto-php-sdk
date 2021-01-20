<?php

namespace Meveto\Client\Enum;

use Meveto\Client\Compat\Enum;

/**
 * Class Architecture.
 */
class Architecture extends Enum
{
    /**
     * Default value.
     */
    const __default = 'web';

    /**
     * architecture: web.
     */
    const WEB = 'web';

    /**
     * architecture: rest.
     */
    const REST = 'rest';
}
