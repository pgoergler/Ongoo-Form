<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of StringSanitizer
 *
 * @author paul
 */
class StringSanitizer extends \mageekguy\atoum\test
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\StringSanitizer();

        $this->string($sanitizer->sanitizeValue(true))->isEqualTo("1")
            ->string($sanitizer->sanitizeValue(false))->isEqualTo("")
            ->string($sanitizer->sanitizeValue("string"))->isEqualTo("string")
                
            ->string($sanitizer->sanitizeValue(0))->isEqualTo("0")
            ->string($sanitizer->sanitizeValue(100))->isEqualTo("100")
            ->string($sanitizer->sanitizeValue(-100))->isEqualTo("-100")
                
            ->string($sanitizer->sanitizeValue(["A", 1 , 4]))->isEqualTo("A,1,4")
        ;
        $this->if(eval("class Foo{ public function __toString(){ return 'FOO';}}"))
            ->and($o = new \Foo())
            ->string($sanitizer->sanitizeValue($o))->isEqualTo("FOO")
            ;
    }

}
