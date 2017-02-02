<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of TrimSanitizer
 *
 * @author paul
 */
class TrimSanitizer extends AbstractSanitizer
{

    public function sanitizeValue($value)
    {
        if (is_string($value))
        {
            return \trim($value);
        }
        return $value;
    }

}
