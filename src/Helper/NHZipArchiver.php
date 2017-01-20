<?php

namespace Fuguevit\NHDownloader\Helper;

use ZipArchive;

class NHZipArchiver extends ZipArchive
{
    /**
     * @param $location
     * @param $name
     * 
     * @return mixed
     */
    public static function zipFolder($location, $name)
    {
        return (new static)->addDir($location, $name);
    }
    
    public function addDir($location, $name)
    {
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name);
    }
    
    protected function addDirDo($location, $name)
    {
        $name .= '/';
        $location .= '/';
        $dir = opendir ($location);
        
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') continue;
            $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location . $file, $name . $file);
        }
    }
}

