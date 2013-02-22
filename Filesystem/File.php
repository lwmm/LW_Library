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
 * Die "LW_FILE" Klasse ist die grundlegende Klasse, welche eine Datei repräsentiert
 * 
 * @package  Framework
 * @author   Dr. Andreas Eckhoff
 * @since    PHP 5.0
 */
class File
{

    private static $files = array();
    private $path = "";
    private $filename = "";
    private $dateformat = "d.m.Y H:i";

    /**
     * Constructor
     * hier werden die Grundvariablen gesetzt und der Klasse zur Verfügung gestellt.
     *
     */
    public function __construct($path, $filename)
    {
        $filename = str_replace(" ", "%20", $filename);
        $this->filename = $filename;
        $this->path = $path;
    }

    /**
     * getInstance
     * Singleton/Factory -> es wird die Klasse selbst instanziert und zurückgegeben
     *
     * return object eine Instanz der Registry
     */
    public static function getInstance($path, $filename)
    {
        $id = $path . $filename;
        if (self::$files[$id] == null) {
            self::$files[$id] = new \LwLibrary\Filesystem\File($path, $filename);
        }
        return self::$files[$id];
    }

    /**
     * getType
     * der Objecttyp (derzeit "file", "directory" oder "image") wird zurückgegeben
     *
     * @return string
     */
    public function getType()
    {
        return "file";
    }

    /**
     * getDateformat
     * das Datumsformat wird zurückgegeben
     *
     * @return string
     */
    public function getDateformat()
    {
        return $this->dateformat;
    }

    /**
     * getFilename
     * der Dateiname wird zurückgegeben
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * getName
     * der Dateiname wird zurückgegeben
     *
     * @return string
     */
    public function getName()
    {
        return false;
    }

    /**
     * getPath
     * der Pfad zur Datei wird zurückgegeben
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * setDateFormat
     * das Datumsformat wird gesetzt
     *
     * @param string
     */
    public function setDateFormat($format = false)
    {
        if (!$format) {
            $this->dateformat = "d.m.Y h:i";
        } else {
            $this->dateformat = $format;
        }
    }

    /**
     * delete
     * die Datei wird gelöscht
     *
     * @return bool/Exception
     */
    public function delete()
    {
        try {
            $this->check();
            return unlink($this->path . $this->filename);
        } catch (\Exception $e) {
            throw new \Exception("[Loeschen nicht moeglich] " . $e->getMessage());
        }
    }

    /**
     * rename
     * die Datei wird umbenannnt. Der optionale Parameter gibt an, 
     * ob eine eventuell bereits vorhandene Datei mit dem Namen 
     * überschrieben werden darf.
     *
     * @param $to 		string
     * @param $overwrite bool 	optional
     *
     * @return bool/Exception
     */
    public function rename($to, $overwrite = false)
    {
        $to = str_replace(" ", "_", $to);
        try {
            if ($overwrite == false) {
                try {
                    $this->check(false, $to);
                    $ok = false;
                } catch (\Exception $e) {
                    $ok = true;
                }
                if (!$ok) {
                    throw new \Exception("File existiert bereits");
                }
            } else {
                unlink($this->path . $to);
            }
            //die("from (".$this->path.$this->filename.") to (".$this->path.$to.")");			$this->check();
            return rename($this->path . $this->filename, $this->path . $to);
        } catch (\Exception $e) {
            throw new \Exception("[Umbenennen nicht moeglich] " . $e->getMessage());
        }
    }

    /**
     * move
     * die Datei wird verschoben. Der optionale Parameter gibt an, 
     * ob eine eventuell bereits vorhandene Datei mit dem Namen 
     * überschrieben werden darf.
     *
     * @param $pathto 		string
     * @param $filenameto 	string
     * @param $overwrite 	bool 	optional
     *
     * @return bool/Exception
     */
    public function move($pathto, $filenameto = false, $overwrite = false)
    {
        if ($filenameto == false) {
            $filenameto = $this->filename;
        }

        try {
            $this->check();
            if ($overwrite === false) {
                try {
                    $this->check($pathto, $filenameto);
                    $ok = false;
                } catch (\Exception $e) {
                    $ok = true;
                }
            } else {
                @unlink($pathto . $filenameto);
                $ok = true;
            }
            if ($ok) {
                return rename($this->path . $this->filename, $pathto . $filenameto);
            } else {
                throw new \Exception("Eine Datei mit dem Dateiname [ " . $filenameto . " ] existiert bereits.");
            }
        } catch (\Exception $e) {
            throw new \Exception("[Verschieben nicht moeglich] " . $e->getMessage());
        }
    }

    /**
     * copy
     * die Datei wird kopiert. Der optionale Parameter gibt an, 
     * ob eine eventuell bereits vorhandene Datei mit dem Namen 
     * überschrieben werden darf.
     *
     * @param $pathto 		string
     * @param $filenameto 	string
     * @param $overwrite 	bool 	optional
     *
     * @return bool/Exception
     */
    public function copy($pathto, $filenameto = false, $overwrite = false)
    {
        if ($filenameto == false) {
            $filenameto = $this->filename;
        }
        
        try {
            $this->check();
            if ($overwrite === false) {
                try {
                    $this->check($pathto, $filenameto);
                    $ok = false;
                } catch (\Exception $e) {
                    $ok = true;
                }
            } else {
                @unlink($pathto . $filenameto);
                $ok = true;
            }
            if ($ok) {
                return copy($this->path . $this->filename, $pathto . $filenameto);
            } else {
                throw new \Exception("Eine Datei mit dem Dateiname [ " . $filenameto . " ] existiert bereits.");
            }
        } catch (\Exception $e) {
            throw new \Exception("[Kopieren nicht moeglich] " . $e->getMessage());
        }
    }

    /**
     * getSize
     * die Größe der Datei wird ermittelt und zurückgegeben. 
     * Bei gesetztem optionalen Parameter, wird die unformatierte
     * Größe zurückgegeben, ansonsten die formatierte.
     *
     * @param $basic 	bool 	optional
     *
     * @return string
     */
    public function getSize($basic = false)
    {
        if ($basic === false) {
            return $this->humanFileSize(filesize($this->path . $this->filename));
        } else {
            return filesize($this->path . $this->filename);
        }
    }

    /**
     * getDate
     * Das Analgedatum der Datei wird im vorgegebenen Format zurückgegeben
     *
     * @return string
     */
    public function getDate()
    {
        return date($this->dateformat, filectime($this->path . $this->filename));
    }

    public function getRights()
    {
        return \lw_io::file_perms($this->path . $this->filename);
    }

    /**
     * getExtension
     * Die Dateiendung wird zurückgegeben
     *
     * @return string
     */
    public function getExtension()
    {
        return array_pop(explode('.', $this->filename));
    }

    /**
     * check
     * Es wird überprüft ob die Datei vorhanden ist. 
     * Optional kann auch eine andere Datei überprüft werden,
     * wenn die entsprechenden Parameter gesetzt sind.
     *
     * @param 	$path		string    
     * @param 	$filename	string    
     *
     * @return bool/Exception
     */
    public function check($path = false, $filename = false)
    {
        if (!$path) {
            $path = $this->path;
        }
        if (!$filename) {
            $filename = $this->filename;
        }
        if (!is_dir($path)) {
            throw new \Exception("Verzeichnis existiert nicht: " . $path);
        }
        if (!is_file($path . $filename)) {
            throw new \Exception("File existiert nicht: " . $path . $filename);
        }
        return true;
    }

    /**
     * humanFileSize
     * Die Gr��enangabe wird formatiert
     *
     * @param 	$size	int
     *
     * @return string
     */
    private function humanFileSize($size)
    {
        if ($size == 0) {
            return("0 Bytes");
        }
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
    }

    /**
     * getNextFilename
     * Es wird �berpr�ft, ob es in dem angegebenen Pfad bereits eine Datei mit dem Filenamen gibt.
     * Ist dies der Fall, so wird ein erweiterter (noch nciht vorhandener) Dateiname zur�ckgegeben.
     *
     * @param 	$filepath	string
     *
     * @return string
     */
    public function getNextFilename($filepath = false)
    {
        if(!$filepath) $filepath = $this->getPath().$this->getFilename();
        
        if (!is_file($filepath))
            return $filepath;

        $name = basename($filepath, "." . $this->getExtension());
        $ext = $this->getExtension();
        $i = 1;
        do {
            $i++;
            $temp_path = $this->path . $name . "_" . $i . "." . $ext;
        } while (is_file($temp_path));

        return $temp_path;
    }

}