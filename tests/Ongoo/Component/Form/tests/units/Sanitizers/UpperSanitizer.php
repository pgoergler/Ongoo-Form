<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of UpperSanitizer
 *
 * @author paul
 */
class UpperSanitizer extends \mageekguy\atoum\test
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\UpperSanitizer();

        $this->string($sanitizer->sanitizeValue(true))->isEqualTo("1")
            ->string($sanitizer->sanitizeValue(false))->isEqualTo("")
            ->string($sanitizer->sanitizeValue("string"))->isEqualTo("STRING")
            ->string($sanitizer->sanitizeValue("String"))->isEqualTo("STRING")
            ->string($sanitizer->sanitizeValue("STRING"))->isEqualTo("STRING")
            ->string($sanitizer->sanitizeValue("strinG"))->isEqualTo("STRING")
                
            ->string($sanitizer->sanitizeValue(0))->isEqualTo("0")
            ->string($sanitizer->sanitizeValue(100))->isEqualTo("100")
            ->string($sanitizer->sanitizeValue(-100))->isEqualTo("-100")
            ;
    }

}
