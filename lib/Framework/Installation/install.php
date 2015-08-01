<?php

/**
 * Creates all the relevant application files for the user if they don't exist so that these are not included in
 * version control and the framework files can be updated separately
 */

/**
 * @param string $source
 * @param string $destination
 *
 * @see http://stackoverflow.com/a/2050909/736809
 */
function recurse_copy($source, $destination)
{
    $dir = opendir($source);
    @mkdir($destination);

    while(false !== ($file = readdir($dir)))
    {
        if (!in_array($file, ['.', '..']))
        {
            if (is_dir($source . '/' . $file))
            {
                recurse_copy(sprintf('%s/%s', $source, $file), sprintf('%s/%s', $destination, $file));
                //recurse_copy($source . '/' . $file,$destination . '/' . $file);
            }
            else
            {
                copy(sprintf('%s/%s', $source, $file), sprintf('%s/%s', $destination, $file));
                //copy($source . '/' . $file, $destination . '/' . $file);
            }
        }
    }

    closedir($dir);
}

if (!file_exists(__DIR__ . '/../../Application'))
{
    recurse_copy(__DIR__  . '/Application', __DIR__ . '/../../Application');
}

exec('rm -rf ' . __DIR__);