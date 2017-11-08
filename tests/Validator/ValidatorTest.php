<?php
namespace Tests\Validator;

use PHPUnit\Framework\TestCase;
use Application\Validator\Validator;

class ValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp()
    {
      $this->validator = new Validator();
    }

    // Test validateEmail
    public function testValidateEmailReturnsFalseIfNoEmail()
    {
        $this->assertFalse($this->validator->validateEmail());
    }

    public function testValidateEmailReturnsCorrectResponse()
    {
        $this->assertFalse($this->validator->validateEmail('invalid-email'));
        $this->assertTrue($this->validator->validateEmail('email@domain.com'));
    }
}
