<?php

/* * ************************************************************************
 *  Copyright notice
 *
 *  Copyright 1998-2013 Logic Works GmbH
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *  
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *  
 * ************************************************************************* */

namespace LwLibrary\Validation;

class PasswordStrength
{

    public function __construct($login, $password)
    {
        $this->login = trim($login);
        if (!empty($this->login)) {
            $password = str_replace($this->login, '', $password);
        }
        $this->password = trim($password);
        $this->strength = 0;
        $this->length = strlen($this->password);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPasswordStrength()
    {

        $ok = $this->_checkLength();
        if (!$ok)
            return $this->strength;

        $this->_checkChunks();
        $this->_checkNumbers();
        $this->_checkSymbols();
        $this->_checkCases();

        if (($this->numbers > 0) && ($this->symbols > 0)) {
            $this->strength += 15;
        }

        if (($this->numbers > 0) && ($this->characters > 0)) {
            $this->strength += 15;
        }

        if (($this->symbols > 0) && ($this->characters > 0)) {
            $this->strength += 15;
        }

        if (($this->numbers == 0) && ($this->symbols == 0)) {
            $this->strength -= 10;
        }

        if (($this->symbols == 0) && ($this->characters == 0)) {
            $this->strength -= 10;
        }

        if ($this->strength < 0) {
            $this->strength = 0;
        }

        if ($this->strength > 100) {
            $this->strength = 100;
        }

        return $this->strength;
    }

    private function _checkLength()
    {
        if ($this->length < 5) {
            return false;
        } else {
            $this->strength = $this->length * 4;
            return true;
        }
    }

    private function _checkChunks()
    {
        for ($i = 2; $i <= 4; $i++) {
            $temp = str_split($this->password, $i);
            $this->strength -= ( ceil($this->length / $i) - count(array_unique($temp)));
        }
    }

    private function _checkNumbers()
    {
        preg_match_all('/[0-9]/', $this->password, $this->numbers);

        if (!empty($this->numbers)) {
            $this->numbers = count($this->numbers[0]);

            if ($this->numbers >= 3) {
                $this->strength += 5;
            }
        } else {
            $this->numbers = 0;
        }
    }

    private function _checkSymbols()
    {
        preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^¨\\\]/', $this->password, $this->symbols);

        if (!empty($this->symbols)) {
            $this->symbols = count($this->symbols[0]);

            if ($this->symbols >= 2) {
                $this->strength += 5;
            }
        } else {
            $this->symbols = 0;
        }
    }

    private function _checkCases()
    {
        preg_match_all('/[a-z]/', $this->password, $lowercase_characters);
        preg_match_all('/[A-Z]/', $this->password, $uppercase_characters);

        if (!empty($lowercase_characters)) {
            $lowercase_characters = count($lowercase_characters[0]);
        } else {
            $lowercase_characters = 0;
        }

        if (!empty($uppercase_characters)) {
            $uppercase_characters = count($uppercase_characters[0]);
        } else {
            $uppercase_characters = 0;
        }

        if (($lowercase_characters > 0) && ($uppercase_characters > 0)) {
            $this->strength += 10;
        }

        $this->characters = $lowercase_characters + $uppercase_characters;
    }

}