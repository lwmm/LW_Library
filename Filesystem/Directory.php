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
 * Die "LW_DIRECTORY" Klasse ist die grundlegende Klasse, welche ein Verzeichnis repräsentiert
 * 
 * @package  Framework
 * @author   Dr. Andreas Eckhoff
 * @since    PHP 5.0
 */
class Directory
{
    private static $dir = array();

    /**
     * Constructor
     * hier werden die Grundvariablen gesetzt und der Klasse zur Verfügung gestellt.
     *
     */
    protected function __construct($path)
    {
        $this->invalid = false;
        
        if (substr($path, strlen($path) - 1, strlen($path)) != "/") {
            $path = $path . "/";
        }
        $path = str_replace("//", "/", $path);
        $this->path = $path;


        $this->name = basename($this->path) . "/";

        #$this->basepath = str_replace($this->name, "", $this->path);

        $this->basepath = substr($this->path, 0, strlen($this->path) - strlen($this->name));
        // hinzugefuegt am 2008-06-20
    }

    /**
     * getInstance
     * Singleton/Factory -> es wird die Klasse selbst instanziert und zurückgegeben
     *
     */
    public static function getInstance($path)
    {
        $id = $path;

        if (!isset(self::$dir[$id]) || self::$dir[$id] == null) {
            self::$dir[$id] = new \LwLibrary\Filesystem\Directory($path);
        }
        return self::$dir[$id];
    }

    /**
     * getType
     * der Objecttyp (derzeit "file", "directory" oder "image") wird zurückgegeben
     *
     * @return string
     */
    public function getType()
    {
        return "dir";
    }

    /**
     * addInvalidContent
     * dem Array unerwünschter Dateien/Verzeichnisse wird ein Eintrag hinzugefügt
     *
     * @param 	$name 	string
     */
    public function addInvalidContent($name)
    {
        $this->invalid[$name] = true;
    }

    /**
     * removeInvalidContent
     * aus dem Array unerwünschter Dateien/Verzeichnisse wird ein Eintrag entfernt
     *
     * @param 	$name 	string
     */
    public function removeInvalidContent($name)
    {
        $this->invalid[$name] = false;
    }

    /**
     * delete
     * das Verezichnis Datei wird gelöscht. 
     * Mit dem optionalen Paramter wird festgelegt, ob enthaltene Dateien auch 
     * gelöscht werden dürfen.
     *
     * @param 	$includeFiles 	bool
     * 
     * @return bool/Exception
     */
    public function delete($includeFiles = false)
    {
        $this->check();
        $currentFolder = opendir($this->path);
        $i = 0;
        while ($sFile = readdir($currentFolder)) {
            if ($sFile != '.' && $sFile != '..') {
                if (is_file($this->path . $sFile)) {
                    if ($includeFiles !== false) {
                        unlink($this->path . $sFile);
                    } else {
                        throw new \Exception("[L&ouml;schen nicht m&ouml;glich] Das Verzeichnis ist nicht leer");
                        return false;
                    }
                }
            }
        }
        closedir($currentFolder);
        return rmdir($this->path);
    }

    /**
     * rename
     * das Verzeichnis wird umbenannnt. Der optionale Parameter gibt an, 
     * ob eine eventuell bereits vorhandene Datei mit dem Namen 
     * überschrieben werden darf.
     *
     * @param 	$to 			string
     *
     * @return 	bool/Exception
     */
    public function rename($to)
    {
        $to = str_replace(" ", "_", $to);

        try {
            $ok = $this->check($this->basepath . $to);
            if (!$ok) {
                return rename($this->path, $this->basepath . $to);
            }
            throw new \Exception("Das Verzeichnis existiert bereits");
        } catch (\Exception $e) {
            throw new \Exception("[Umbennenen nicht m&ouml;glich] " . $e->getMessage());
        }
    }

    public function renameFile($oldname, $newname)
    {
        if ($this->fileExists($oldname)) {
            return rename($this->path . $oldname, $this->path . $newname);
        }
    }

    /**
     * add
     * es wird ein neues Verzeichnis angelegt
     *
     * @param 	$name 		string
     *
     * @return 	bool/Exception
     */
    public function add($name)
    {
        try {
            $this->check();
            if (!$this->check($this->path . $name)) {
                return mkdir($this->path . $name);
            }
            throw new \Exception("Das Verzeichnis existiert bereits");
        } catch (\Exception $e) {
            throw new \Exception("[Hinzuf&uuml;gen nicht m&ouml;glich] " . $e->getMessage());
        }
    }

    /**
     * move
     * es wird ein neues Verzeichnis umbenannnt
     *
     * @param 	$name 		string
     *
     * @return 	bool/Exception
     */
    public function move($to)
    {
        return $this->rename($to);
    }

    /**
     * addFile
     * dem Verzeichnis wird eine Datei hinzugefügt
     *
     * @param 	$tmp 					string
     * @param 	$destination_filename 	string
     *
     * @return 	bool/Exception
     */
    public function addFile($tmp, $destination_filename, $flag = false)
    {
        $tmp = realpath($tmp);
        if (is_file($tmp)) {
            if (!$flag) {
                $destination_filename = str_replace(" ", "_", $destination_filename);
            }
            return copy($tmp, $this->path . $destination_filename);
        }
        return false;
    }

    public function chmodFile($name, $permissions)
    {
        $mode = 0;

        if ($permissions[1] == 'r')
            $mode += 0400;
        if ($permissions[2] == 'w')
            $mode += 0200;
        if ($permissions[3] == 'x')
            $mode += 0100;
        else if ($permissions[3] == 's')
            $mode += 04100;
        else if ($permissions[3] == 'S')
            $mode += 04000;

        if ($permissions[4] == 'r')
            $mode += 040;
        if ($permissions[5] == 'w')
            $mode += 020;
        if ($permissions[6] == 'x')
            $mode += 010;
        else if ($permissions[6] == 's')
            $mode += 02010;
        else if ($permissions[6] == 'S')
            $mode += 02000;

        if ($permissions[7] == 'r')
            $mode += 04;
        if ($permissions[8] == 'w')
            $mode += 02;
        if ($permissions[9] == 'x')
            $mode += 01;
        else if ($permissions[9] == 't')
            $mode += 01001;
        else if ($permissions[9] == 'T')
            $mode += 01000;

        //printf('Mode is %d decimal and %o octal', $mode, $mode); 

        return @chmod($this->path . $name, $mode);
    }

    public function chgrpFile($name, $chgrp)
    {
        //echo "chgrp to: ".$chgrp."<br>";
        return @chgrp($this->path . $name, $chgrp);
    }

    public function deleteFile($name)
    {
        return unlink($this->path . $name);
    }

    /**
     * getDirectoryContents
     * die Inhalte des Verzeichnis werden ausgelesen und als Array aus Objekten zurückgegeben
     *
     * @param 	$type	string	optional
     *
     * @return 	array of objects
     */
    public function getDirectoryContents($type = false)
    {
        $items = false;
        $currentFolder = opendir($this->path);
        $i = 0;
        while ($sFile = readdir($currentFolder)) {
            if ($sFile != '.' && $sFile != '..') {
                if (is_dir($this->path . $sFile) && ($type != "file") && $this->invalid[$sFile] != true) {
                    $items["d" . $i] = \LwLibrary\Filesystem\Directory::getInstance($this->path . $sFile);
                    $i++;
                }
                if (is_file($this->path . $sFile) && ($type != "dir") && $this->invalid[$sFile] != true) {
                    $ext = strtolower(array_pop(explode('.', $sFile)));
                    if ($ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "png") {
                        $items["f" . $i] = new \lw_file($this->path, $sFile);
                    } else {
                        $items["i" . $i] = new \lw_imagefile($this->path, $sFile);
                    }
                    $i++;
                }
            }
        }
//        if (is_array($items)) {
//            usort($items, array($this, "lw_strcasecmp"));
//        }
        closedir($currentFolder);
        return $items;
    }

    private function lw_strcasecmp($a, $b)
    {
        if (strtolower($a->getName()) < strtolower($b->getName())) {
            return -1;
        } elseif (strtolower($a->getName()) == strtolower($b->getName())) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * getName
     * der Verzeichnisname wird zurückgegeben
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getSize
     * es wird "0" zurückgegeben
     *
     * @return string
     */
    public function getSize()
    {
        return "0";
    }

    /**
     * getDate
     * Das Analgedatum der Datei wird im vorgegebenen Format zurückgegeben
     *
     * @return string
     */
    public function getDate()
    {
        return false;
    }

    /**
     * getBasePath
     * der Pfad zum Verzeichnis wird zur�ckgegeben (ohne das Verzeichnis selbst)
     *
     * @return string
     */
    public function getBasepath()
    {
        return $this->basepath;
    }

    /**
     * getPath
     * der Pfad zum Verzeichnis wird zurückgegeben (inklusive das Verzeichnis selbst)
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * check
     * Es wird überprüft ob das Verzeichnis vorhanden ist. 
     * Optional kann auch ein anderes Verzeichnis überprüft werden,
     * wenn der entsprechende Parameter gesetzt ist.
     *
     * @param 	$path		string    
     *
     * @return bool/Exception
     */
    public function check($path = false)
    {
        if (!$path) {
            $path = $this->path;
        }
        if (!is_dir($path)) {
            return false;
        }
        return true;
    }

    /**
     * getNextFilename
     * Es wird überprüft, ob es in dem angegebenen Pfad bereits eine Datei mit dem Filenamen gibt.
     * Ist dies der Fall, so wird ein erweiterter (noch nicht vorhandener) Dateiname zurückgegeben.
     *
     * @param 	$filepath	string
     *
     * @return string
     */
    public function getNextFilename($filename)
    {
        if (!is_file($this->path . $filename)) return $filename;
        
        $ext = array_pop(explode('.', $filename));
        
        $name = basename($filename, "." . $ext);
        $i = 1;
        do {
            $i++;
            $tempname = $name . "_" . $i . "." . $ext;
        } while (is_file($this->path . $tempname));

        return $tempname;
    }

    public function fileExists($name)
    {
        if (is_file($this->path . $name))
            return true;
        return false;
    }

    public function isWritable($name)
    {
        if (is_writable($this->path . $name))
            return true;
        return false;
    }

}