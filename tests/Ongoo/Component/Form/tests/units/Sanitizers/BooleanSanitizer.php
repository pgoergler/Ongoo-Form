<?php

namespace Ongoo\Component\Form\tests\units\Sanitizers;

/**
 * Description of BooleanSanitizer
 *
 * @author paul
 */
class BooleanSanitizer extends \mageekguy\atoum\test
{

    public function testSanitizerValue()
    {
        $sanitizer = new \Ongoo\Component\Form\Sanitizers\BooleanSanitizer();

        $this->boolean($sanitizer->sanitizeValue(true))->isTrue()
                ->boolean($sanitizer->sanitizeValue(false))->isFalse()
                ->boolean($sanitizer->sanitizeValue('on'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('ON'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('yes'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('YES'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('true'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('TRUE'))->isTrue()
                ->boolean($sanitizer->sanitizeValue('1'))->isTrue()
                
                ->boolean($sanitizer->sanitizeValue('off'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('OFF'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('no'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('NO'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('false'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('FALSE'))->isFalse()
                ->boolean($sanitizer->sanitizeValue('0'))->isFalse()
                
                ->boolean($sanitizer->sanitizeValue(0))->isFalse()
                ->boolean($sanitizer->sanitizeValue(1))->isTrue()
                ->boolean($sanitizer->sanitizeValue(100))->isTrue()
                ->boolean($sanitizer->sanitizeValue(-100))->isTrue()
                
                ->boolean($sanitizer->sanitizeValue('string'))->isFalse()
                ->boolean($sanitizer->sanitizeValue(new \StdClass()))->isFalse()
            ;
    }

}
