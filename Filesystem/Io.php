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
 * Die "\LwLibrary\Filesystem\Io" Klasse ist eine Pseudoklasse, die Funktionen fÃ¼r I/O Operationen zur VerfÃ¼gung stellt
 * 
 * @package  Framework
 * @author   Dr. Andreas Eckhoff
 * @version  3.0 (beta)
 * @since    PHP 5.0
 */
class Io
{

    /**
     * dies ist eine Pseudo-Klasse und dient nur als 
     * Container fÃ¼r zusammenghÃ¶rige Funktionen
     */
    public function __construct()
    {
        
    }

    public function mkdir_recursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || \LwLibrary\Filesystem\Io::mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    /**
     * lÃ¤dt die Datei-/Verzeichnisliste des angegebenen Verzeichnisses
     *
     * @param   string
     * @return  bool
     */
    public function scandir($dir = './', $sort = 0)
    {
        $dir_open = @opendir($dir);
        if (!$dir_open) {
            return false;
        }
        while (($dir_content = readdir($dir_open)) !== false) {
            $files[] = $dir_content;
        }
        if ($sort == 1) {
            rsort($files, SORT_STRING);
        } else {
            sort($files, SORT_STRING);
        }
        return $files;
    }

    /**
     * lÃ¤dt den kompletten Inhalt eines Files und gibt diesen zurÃ¼ck
     *
     * @param   string
     * @return  string
     */
    public function loadFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("Das File (" . $file . ") existiert nicht !");
        }
        $fileopen = @fopen($file, "r");
        if (!$fileopen) {
            throw new \Exception("Das File (" . $file . ") konnte nicht geoeffnet werden !");
        }
        $file_data = @fread($fileopen, filesize($file));
        @fclose($fileopen);
        return $file_data;
    }

    public function loadCSV($file, $length = "1024", $delimiter = ",", $enclosure = "\"", $escape = "\\")
    {
        if(is_file($file)) {
            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, $length, $delimiter)) !== FALSE) {
                    $return[] = $data;
                }
                fclose($handle);
            }
            return $return;
        }
        return false;
    }

    /**
     * speichert einen text in einem File
     *
     * @param   string
     * @param   string
     * @return  bool
     */
    public function writeFile($file, $data)
    {
        $fileopen = @fopen($file, "w+");
        if (!$fileopen) {
            throw new \Exception("Das File konnte nicht geoeffnet werden !");
        }
        $ok = fwrite($fileopen, $data);
        fclose($fileopen);
        return $ok;
    }

    /**
     * speichert einen text in einem File
     *
     * @param   string
     * @param   string
     * @return  bool
     */
    public function appendFile($file, $data)
    {
        $fileopen = @fopen($file, "a+");
        if (!$fileopen) {
            throw new \Exception("Das File konnte nicht geoeffnet werden !");
        }
        $ok = fwrite($fileopen, $data);
        fclose($fileopen);
        return $ok;
    }

    public function getFileName($filepath)
    {
        $slashPos = strrpos($filepath, "/");
        $name = substr($filepath, $slashPos + 1, strlen($filepath));
        return $name;
    }

    public function getFileNameWithoutExtension($filepath)
    {
        $filename = \LwLibrary\Filesystem\Io::getFileName($filepath);
        $parts = explode(".", $filename);
        $noext = "";
        foreach($parts as $key => $value) {
            if($key != count($parts) - 1) {
                $noext.= $value;
            }
        }
        return $noext;
    }

    public function getPathByRemovingLastPathComponent($filepath) # base path ?
    {
        $slashPos = strrpos($filepath, "/");
        if($slashPos) $slashPos++;
        $path = substr($filepath, 0, $slashPos);
        return $path;
    }

    public function getPathByAddingStringToFilename($filepath, $string)
    {
        $string = str_replace(".", "_", $string);
        $string = str_replace("/", "_", $string);
        
        if(substr($string, strlen($string)-1,1) == "_") {
            $string = substr($string, 0, strlen($string) -1 );
        }
        
        $dir = \LwLibrary\Filesystem\Io::getPathByRemovingLastPathComponent($filepath);
        $ext = \LwLibrary\Filesystem\Io::getFileExtension($filepath);
        $name = \LwLibrary\Filesystem\Io::getFileNameWithoutExtension($filepath);
        if (strlen($name . $string . "." . $ext) > 254) {
            $string = "";
        }

        $name .= $string;

        return $dir . $name . "." . $ext;
    }

    public function getFreeFilepath($filepath)
    {
        if (!is_file($filepath))
            return $filepath;

        $dir = \LwLibrary\Filesystem\Io::getPathByRemovingLastPathComponent($filepath);
        $ext = \LwLibrary\Filesystem\Io::getFileExtension($filepath);
        $name = \LwLibrary\Filesystem\Io::getFileNameWithoutExtension($filepath);

        //echo "DIR:".$dir."<br>";
        //echo "EXT:".$ext."<br>";
        //echo "NAME:".$name."<br>";
        //die();

        $i = 1;
        do {
            $i++;
            $temp_path = $dir . $name . "_" . $i . "." . $ext;
        } while (is_file($temp_path));

        return $temp_path;
    }

    function humanFileSize($size, $precision = 2)
    {
        if ($size == 0) {
            return("0 Bytes");
        }
        $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), $precision) . $filesizename[$i];
    }

    function splitFilename($filename)
    {
        $file['ext'] = \LwLibrary\Filesystem\Io::getFileExtension($filename);
        $temp = str_replace('.' . $file['ext'], '', $filename);
        $file["suffix"] = "";
        if(strrpos($temp, "_")) {
            $temp = substr($temp, 0, strrpos($temp, "_"));
            $temp_suffix = str_replace($temp, "", $filename);
            $file["suffix"] = str_replace(".".$file["ext"], "", $temp_suffix);
        }
        $file['name'] = $temp;
        $file['filename'] = $file['name'] . $file["suffix"] . '.' . $file['ext'];
        return $file;
    }

    public function getFileExtension($filename)
    {
        if (strstr($filename, ".")) {
            $parts = explode(".", $filename);
            $max = count($parts) - 1;
            return $parts[$max];
        } else {
            return false;
        }
    }

    function file_perms($file, $octal = false)
    {
        if (!file_exists($file))
            return false;
        $perms = fileperms($file);
        $cut = $octal ? 2 : 3;
        return substr(decoct($perms), $cut);
    }

    function getMimeType($type)
    {
        $mimeType = array(
            "ez" => "application/andrew-inset",
            "hqx" => "application/mac-binhex40",
            "cpt" => "application/mac-compactpro",
            "doc" => "application/msword",
            "dot" => "application/msword",
            "xls" => "application/msexcel",
            "bin" => "application/octet-stream",
            "dms" => "application/octet-stream",
            "lha" => "application/octet-stream",
            "lzh" => "application/octet-stream",
            "exe" => "application/octet-stream",
            "class" => "application/octet-stream",
            "so" => "application/octet-stream",
            "dll" => "application/octet-stream",
            "oda" => "application/oda",
            "pdf" => "application/pdf",
            "ai" => "application/postscript",
            "eps" => "application/postscript",
            "ps" => "application/postscript",
            "smi" => "application/smil",
            "smil" => "application/smil",
            "wbxml" => "application/vnd.wap.wbxml",
            "wmlc" => "application/vnd.wap.wmlc",
            "wmlsc" => "application/vnd.wap.wmlscriptc",
            "bcpio" => "application/x-bcpio",
            "vcd" => "application/x-cdlink",
            "pgn" => "application/x-chess-pgn",
            "cpio" => "application/x-cpio",
            "csh" => "application/x-csh",
            "dcr" => "application/x-director",
            "dir" => "application/x-director",
            "dxr" => "application/x-director",
            "dvi" => "application/x-dvi",
            "spl" => "application/x-futuresplash",
            "gtar" => "application/x-gtar",
            "hdf" => "application/x-hdf",
            "js" => "application/x-javascript",
            "skp" => "application/x-koan",
            "skd" => "application/x-koan",
            "skt" => "application/x-koan",
            "skm" => "application/x-koan",
            "latex" => "application/x-latex",
            "nc" => "application/x-netcdf",
            "cdf" => "application/x-netcdf",
            "sh" => "application/x-sh",
            "shar" => "application/x-shar",
            "swf" => "application/x-shockwave-flash",
            "sit" => "application/x-stuffit",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc",
            "tar" => "application/x-tar",
            "tcl" => "application/x-tcl",
            "tex" => "application/x-tex",
            "texinfo" => "application/x-texinfo",
            "texi" => "application/x-texinfo",
            "t" => "application/x-troff",
            "tr" => "application/x-troff",
            "roff" => "application/x-troff",
            "man" => "application/x-troff-man",
            "me" => "application/x-troff-me",
            "ms" => "application/x-troff-ms",
            "ustar" => "application/x-ustar",
            "src" => "application/x-wais-source",
            "xhtml" => "application/xhtml+xml",
            "xht" => "application/xhtml+xml",
            "zip" => "application/zip",
            "au" => "audio/basic",
            "snd" => "audio/basic",
            "mid" => "audio/midi",
            "midi" => "audio/midi",
            "kar" => "audio/midi",
            "mpga" => "audio/mpeg",
            "mp2" => "audio/mpeg",
            "mp3" => "audio/mpeg",
            "aif" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "m3u" => "audio/x-mpegurl",
            "ram" => "audio/x-pn-realaudio",
            "rm" => "audio/x-pn-realaudio",
            "rpm" => "audio/x-pn-realaudio-plugin",
            "ra" => "audio/x-realaudio",
            "wav" => "audio/x-wav",
            "pdb" => "chemical/x-pdb",
            "xyz" => "chemical/x-xyz",
            "bmp" => "image/bmp",
            "gif" => "image/gif",
            "ief" => "image/ief",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "jpe" => "image/jpeg",
            "png" => "image/png",
            "tiff" => "image/tiff",
            "tif" => "image/tif",
            "djvu" => "image/vnd.djvu",
            "djv" => "image/vnd.djvu",
            "wbmp" => "image/vnd.wap.wbmp",
            "ras" => "image/x-cmu-raster",
            "pnm" => "image/x-portable-anymap",
            "pbm" => "image/x-portable-bitmap",
            "pgm" => "image/x-portable-graymap",
            "ppm" => "image/x-portable-pixmap",
            "rgb" => "image/x-rgb",
            "xbm" => "image/x-xbitmap",
            "xpm" => "image/x-xpixmap",
            "xwd" => "image/x-windowdump",
            "igs" => "model/iges",
            "iges" => "model/iges",
            "msh" => "model/mesh",
            "mesh" => "model/mesh",
            "silo" => "model/mesh",
            "wrl" => "model/vrml",
            "vrml" => "model/vrml",
            "css" => "text/css",
            "html" => "text/html",
            "htm" => "text/html",
            "asc" => "text/plain",
            "txt" => "text/plain",
            "rtx" => "text/richtext",
            "rtf" => "text/rtf",
            "sgml" => "text/sgml",
            "sgm" => "text/sgml",
            "tsv" => "text/tab-seperated-values",
            "wml" => "text/vnd.wap.wml",
            "wmls" => "text/vnd.wap.wmlscript",
            "etx" => "text/x-setext",
            "xml" => "text/xml",
            "xsl" => "text/xml",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpe" => "video/mpeg",
            "qt" => "video/quicktime",
            "mov" => "video/quicktime",
            "mxu" => "video/vnd.mpegurl",
            "avi" => "video/x-msvideo",
            "movie" => "video/x-sgi-movie",
            "ice" => "x-conference-xcooltalk",
            "docx" => "application/msword",
            "dotx" => "application/msword",
            "xlsx" => "application/msexcel",
            "xlax" => "application/msexcel",
            "pptx" => "application/mspowerpoint",
            "ppsx" => "application/mspowerpoint",
            "potx" => "application/mspowerpoint",
            "ppsx" => "application/mspowerpoint"
        );
        return $mimeType[$type];
    }

    public function getIconByExtension($ext, $dir)
    {
        $icons = array(
            "mdb" => "document-access.png",
            "xls" => "document-excel.png",
            "xlsx" => "document-excel.png",
            "csv" => "document-excel-csv.png",
            "swf" => "document-flash.png",
            "jpg" => "picture.png",
            "png" => "picture.png",
            "gif" => "picture.png",
            "pdf" => "document-pdf.png",
            "psd" => "document-photoshop.png",
            "ppt" => "document-photoshop.png",
            "pptx" => "document-photoshop.png",
            "doc" => "document-word.png",
            "docx" => "document-word.png",
            "dot" => "document-word.png",
            "dotx" => "document-word.png",
            "mov" => "film.png",
            "mpeg" => "film.png",
            "txt" => "edit-column.png",
            "exe" => "docuemnt-binary.png",
            "zip" => "document-zipper.png",
            "link" => "chain.png"
        );
        if (isset($icons[$ext]))
            return '<img src="' . $dir . 'pics/lw_fugue/' . $icons[$ext] . '" border="0"/>';
        else
            return '<img src="' . $dir . 'pics/lw_fugue/question-button.png" border="0"/>';
    }

}