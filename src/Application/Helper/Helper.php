<?php
namespace Application\Helper;

class Helper
{
    public function validateEmail(string $email = ''): bool
    {
      if (!$email) {
        return false;
      }

      return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getDomainFromEmail(string $email): string
    {
      $splitEmail = explode("@", $email);
      return $splitEmail[1];
    }
}
