<?php
namespace ObzoraNMS;

/**
 * Reursive Filter Iterator to iterate directories and locate .rrd files.
 *
 * @method bool isDir()
 *
 **/
class RRDRecursiveFilterIterator extends \RecursiveFilterIterator
{
    public function accept(): bool
    {
        $filename = $this->current()->getFilename();
        if ($filename[0] === '.') {
            // Ignore hidden files and directories
            return false;
        }
        if ($this->isDir()) {
            // We want to search into directories
            return true;
        }

        // Matches files with .rrd in the filename.
        // We are only searching rrd folder, but there could be other files and we don't want to cause a stink.
        return strpos($filename, '.rrd') !== false;
    }
}
