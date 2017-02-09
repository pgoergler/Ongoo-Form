<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of EmptyAsSanitizer
 *
 * @author paul
 */
class EmptyAsSanitizer extends \mageekguy\atoum\test
{
    public function testSanitize()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\EmptyAsSanitizer('empty value');
        $this
            ->string($sanitizer->sanitizeValue('not empty'))->isEqualTo('not empty')
            ->string($sanitizer->sanitizeValue('     '))->isEqualTo('     ')
            ->object($sanitizer->sanitizeValue(new \StdClass()))->isInstanceOf('\StdClass')
            //-
            ->string($sanitizer->sanitizeValue(''))->isEqualTo('empty value')
            ->string($sanitizer->sanitizeValue(null))->isEqualTo('empty value')
            ->string($sanitizer->sanitizeValue(array()))->isEqualTo('empty value')
        ;
    }
}
