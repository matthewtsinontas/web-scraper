<?php
namespace Application\Validator;

class Validator
{
    public function validateEmail(string $email = ''): bool
    {
      if (!$email) {
        return false;
      }

      return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
