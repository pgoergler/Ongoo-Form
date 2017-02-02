<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of NilSanitizer
 *
 * @author paul
 */
class NilSanitizer extends CallbackSanitizer
{

    public function __construct()
    {
        parent::__construct(null);
    }

}
