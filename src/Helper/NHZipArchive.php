<?php

namespace Fuguevit\NHDownloader\Helper;

use ZipArchive;

class NHZipArchive extends ZipArchive
{
    /**
     * @param $location
     * @param $zipName
     * @param $removeOriginal
     *
     * @return mixed
     */
    public static function zipFolder($location, $zipName, $removeOriginal = true)
    {
        $dirs = explode(DIRECTORY_SEPARATOR, rtrim($location, DIRECTORY_SEPARATOR));
        $newDirName = end($dirs);

        $zip = new static();
        $zip->open($zipName, ZipArchive::CREATE);
        $zip->addDir($location, $newDirName);
        $zip->close();

        if ($removeOriginal) {
            $zip->removeDir($location);
        }
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
        $dir = opendir($location);

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $do = (filetype($location.$file) == 'dir') ? 'addDir' : 'addFile';
            $this->$do($location.$file, $name.$file);
        }
    }

    protected function removeDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        rrmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
