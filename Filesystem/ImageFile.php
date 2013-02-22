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

namespace LwLibrary\Filesystem;

/**
 * Die "LW_IMAGEFILE" Klasse ist die grundlegende Klasse, welche eine Bilddatei repr�sentiert
 * 
 * @package  Framework
 * @author   Dr. Andreas Eckhoff
 * @since    PHP 5.0
 */
class Imagefile extends \LwLibrary\Filesystem\File
{

    private static $files = array();

    /**
     * Constructor
     * hier werden die Grundvariablen gesetzt und der Klasse zur Verf�gung gestellt.
     *
     */
    public function __construct($path, $filename, $type)
    {
        parent::__construct($path, $filename);
        $this->filename = $filename;
        $this->path = $path;
        $this->type = $type;
    }

    /**
     * getInstance
     * Singleton/Factory -> es wird die Klasse selbst instanziert und zur�ckgegeben
     *
     * return object eine Instanz der Registry
     */
    public static function getInstance($path, $filename)
    {
        $id = $path . $filename;
        $ext = strtolower(array_pop(explode('.', $filename)));
        if ($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png") {
            throw new \Exception("Es handelt sich um kein Bild des Typs jpg, gif oder png");
            return false;
        }
        if (self::$files[$id] == null) {
            self::$files[$id] = new \LwLibrary\Filesystem\Imagefile($path, $filename, $ext);
        }
        return self::$files[$id];
    }

    /**
     * getType
     * der Objecttyp (derzeit "file", "directory" oder "image") wird zur�ckgegeben
     *
     * @return string
     */
    public function getType()
    {
        return "image";
    }

    /**
     * setMaxSizes
     * Es werden die maximal zugelasenen Bilderdimensionen gesetzt
     *
     * @param 	$width 		int
     * @param 	$height 	int
     */
    public function setMaxSizes($width, $height)
    {
        $this->width_max = $width;
        $this->height_max = $height;
    }

    /**
     * resize
     * Das Bild wird geresized.
     *
     * @param 	$width_new 		int
     * @param 	$height_new 	int
     * @param 	$keepAspect		bool	optional
     * @param 	$copyIMage		bool 	optional
     *
     * @return 	bool/Exception
     */
    public function resize($width_new, $height_new, $keepAspect = false, $copyImage = false)
    {
        $image = new \lw_image($this->path . $this->filename);

        if ($width_new > $this->width_max || $height_new > $this->height_max || $width_new < 1 || $height_new < 1 || !is_numeric($width_new) || !is_numeric($height_new)) {
            throw new \Exception("Bildgroessen stimmen nicht");
        } else {
            if ($keepAspect !== false) {
                $keepAspect = true;
            }

            if ($copyImage != false) {
                $filename_noext = basename($this->path . $this->filename, "." . $this->ext);
                $newname = $this->getNextFilename($this->path . $filename_noext . "_" . $width_new . "x" . $height_new . "." . $this->ext);

                $image->scaleImage($width_new, $height_new, $newname, $keepAspect, false);
            } else {
                $image->scaleImage($width_new, $height_new, false, $keepAspect, false);
            }
            return true;
        }
        return false;
    }

}