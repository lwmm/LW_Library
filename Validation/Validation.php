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

// function isInt missing param fixed
namespace LwLibrary\Validation;

class Validation
{

    static function hasMaxlength($value, $options)
    {
        if (strlen(trim($value)) > intval($options['value']))
            return false;
        return true;
    }

    static function hasMinlength($value, $options)
    {
        if (strlen(trim($value)) < intval($options['value']))
            return false;
        return true;
    }

    static function isRequired($value)
    {
        if (strlen(trim($value)) < 1)
            return false;
        return true;
    }

    static function isEmail($value)
    {
        if (!$value)
            return true;
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    static function isDate($value, $options = 'de')
    {
        if($options == "de") {
            $day = substr($value, 0, 2);
            $month = substr($value, 3, 2);
            $year = substr($value, 6, 4);            
        } else {
            $month = substr($value, 0, 2);
            $day = substr($value, 3, 2);
            $year = substr($value, 6, 4);
        }
        
        if(checkdate($month, $day, $year)) {
            return true;
        }
        return false;
    }

    static function minDate($value, $date)
    {
        if (!$value) return false;
        if (!$date) return false;
        if (!self::isDate($date)) return false;
        if (!self::isDate($value)) return false;

        $d1 = substr($date, 0, 2);
        $m1 = substr($date, 3, 2);
        $y1 = substr($date, 6, 4);
        $date1 = $y1 . $m1 . $d1;

        $d2 = substr($value, 0, 2);
        $m2 = substr($value, 3, 2);
        $y2 = substr($value, 6, 4);
        $date2 = $y2 . $m2 . $d2;

        if ($date2 >= $date1) return true;
        return false;
    }

    static function maxDate($value, $date)
    {
        if (!$value) return false;
        if (!$date) return false;
        if (!self::isDate($date)) return false;
        if (!self::isDate($value)) return false;

        $d1 = substr($date, 0, 2);
        $m1 = substr($date, 3, 2);
        $y1 = substr($date, 6, 4);
        $date1 = $y1 . $m1 . $d1;

        $d2 = substr($value, 0, 2);
        $m2 = substr($value, 3, 2);
        $y2 = substr($value, 6, 4);
        $date2 = $y2 . $m2 . $d2;
        
        if ($date2 <= $date1) return true;
        return false;
    }

    static function checkFileExtensions($value, $extensions)
    {
        $value = trim($value);
        if (strlen($value) == 0) return true;
        
        $extensions = str_replace(';', ',', $extensions);
        $extensions = strtolower($extensions);
        $exts = explode(',', $extensions);

        $filename = strtolower($value);
        $ext = substr($filename, strrpos($filename, '.') + 1, strlen($filename));

        if (strlen($ext) == 0) return false;
        if (in_array($ext, $exts)) return true;
        
        return false;
    }

    static function isAlnum($value)
    {
        $test = preg_replace('/[^a-zA-Z0-9\s]/', '', (string) $value);
        if (strlen(trim($value)) > 0 && ($value != $test))
            return false;
        return true;
    }

    static function isBetween($value, $options)
    {
        if (strlen(trim($value)) > 0 && ($value < strval($options["value1"]) || $value > strval($options["value2"])))
            return false;
        return true;
    }

    static function isDigits($value)
    {
        if(ctype_digit($value)) return true;
        return false;
    }

    static function isGreaterThan($value, $options)
    {
        if (strlen(trim($value)) > 0 && (intval($value) > intval($options["value"]))) return true;
        return false;
    }

    static function isLessThan($value, $options)
    {
        if (strlen(trim($value)) > 0 && (intval($value) < intval($options["value"]))) return true;
        return false;
    }

    static function isInt($value)
    {
        if (strlen(trim($value)) > 0 && (!is_int($value))) return false;
        return true;
    }

    static function isRegex($value, $options)
    {
        if (strlen(trim($value)) > 0 && (!eregi(strval($options["value"]), $value)))
            return false;
        return true;
    }

    function setIoFactory($IoFactory)
    {
        $this->ioFactory = $IoFactory;
    }
    
    function getIoFactory()
    {
        $ioFactory = new \LwLibrary\Filesystem\IoFactory();
        return $ioFactory;
    }


    function isFiletype($value, $options)
    {
        $Io = $this->ioFactory->createObject();
        if (strlen(trim($value)) > 0) {
            $ext = strtolower($Io->getFileExtension($value));
            if (!strstr($options["value"], ":" . $ext . ":")) return false;
            return true;
        }
        return false;
    }

    function isImage($value)
    {
         $Io = $this->ioFactory->createObject();
        if (strlen(trim($value)) > 0) {
            $ext = strtolower($Io->getFileExtension($value));
            if (!strstr(':jpg:jpeg:png:gif:', ":" . $ext . ":")) return false;
            return true;
        }
        return false;
    }

    static function isCustom($value, $options)
    {
        $function = strval($options["function"]);
        if (method_exists($options["delegate"], $function)) {
            if (!call_user_func(array($options["delegate"], $function), $value))
                return false;
            return true;
        }
    }
}