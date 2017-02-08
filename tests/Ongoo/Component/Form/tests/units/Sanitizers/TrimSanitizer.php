<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of TrimSanitizer
 *
 * @author paul
 */
class TrimSanitizer extends \Atoum\Helpers\Tester
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\TrimSanitizer();

        $this->string($sanitizer->sanitizeValue('foo'))->isEqualTo('foo')
                ->string($sanitizer->sanitizeValue('  foo  '))->isEqualTo('foo')
                ->string($sanitizer->sanitizeValue('xx  foo  '))->isEqualTo('xx  foo')
                ->string($sanitizer->sanitizeValue('xx  foo  xx'))->isEqualTo('xx  foo  xx')
                ->string($sanitizer->sanitizeValue('  foo  xx'))->isEqualTo('foo  xx')
                ->object($sanitizer->sanitizeValue(new \StdClass()))->isInstanceOf('\StdClass');
    }

}
