<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of LowerSanitizer
 *
 * @author paul
 */
class LowerSanitizer extends \mageekguy\atoum\test
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\LowerSanitizer();

        $this->string($sanitizer->sanitizeValue(true))->isEqualTo("1")
            ->string($sanitizer->sanitizeValue(false))->isEqualTo("")
            ->string($sanitizer->sanitizeValue("string"))->isEqualTo("string")
            ->string($sanitizer->sanitizeValue("String"))->isEqualTo("string")
            ->string($sanitizer->sanitizeValue("STRING"))->isEqualTo("string")
            ->string($sanitizer->sanitizeValue("strinG"))->isEqualTo("string")
                
            ->string($sanitizer->sanitizeValue(0))->isEqualTo("0")
            ->string($sanitizer->sanitizeValue(100))->isEqualTo("100")
            ->string($sanitizer->sanitizeValue(-100))->isEqualTo("-100")
            ;
    }

}
