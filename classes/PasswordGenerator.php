<?php
class PasswordGenerator {
    private $length;
    private $lowercase;
    private $uppercase;
    private $digits;
    private $specials;

    private $lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
    private $uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $digitChars = '0123456789';
    private $specialChars = '!@#$%^&*()-_=+[]{};:,.<>?/';

    public function __construct($length, $lowercase, $uppercase, $digits, $specials) {
        $this->length = $length;
        $this->lowercase = $lowercase;
        $this->uppercase = $uppercase;
        $this->digits = $digits;
        $this->specials = $specials;
    }

    public function generate() {
        $password = [];

        $password = array_merge($password, $this->getRandomChars($this->lowercaseChars, $this->lowercase));
        $password = array_merge($password, $this->getRandomChars($this->uppercaseChars, $this->uppercase));
        $password = array_merge($password, $this->getRandomChars($this->digitChars, $this->digits));
        $password = array_merge($password, $this->getRandomChars($this->specialChars, $this->specials));

        $remaining = $this->length - count($password);
        $allChars = $this->lowercaseChars . $this->uppercaseChars . $this->digitChars . $this->specialChars;

        $password = array_merge($password, $this->getRandomChars($allChars, $remaining));

        shuffle($password);

        return implode('', $password);
    }

    private function getRandomChars($chars, $count) {
        $result = [];
        $len = strlen($chars);
        for ($i = 0; $i < $count; $i++) {
            $result[] = $chars[rand(0, $len - 1)];
        }
        return $result;
    }
}

