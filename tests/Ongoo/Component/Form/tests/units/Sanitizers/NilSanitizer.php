<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of NilSanitizer
 *
 * @author paul
 */
class NilSanitizer extends \mageekguy\atoum\test
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\NilSanitizer();

        $this->boolean($sanitizer->sanitizeValue(true))->isTrue()
            ->boolean($sanitizer->sanitizeValue(false))->isFalse()
            ->string($sanitizer->sanitizeValue("string"))->isEqualTo("string")
            ->string($sanitizer->sanitizeValue("String"))->isEqualTo("String")
                
            ->integer($sanitizer->sanitizeValue(0))->isEqualTo(0)
            ->integer($sanitizer->sanitizeValue(100))->isEqualTo(100)
            ->integer($sanitizer->sanitizeValue(-100))->isEqualTo(-100)
            ;
    }

}
