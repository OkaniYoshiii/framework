<?php 

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\Contracts\Interfaces\ShellCommand;

class Init implements ShellCommand
{
    public const CMD_NAME = 'app:init';

    public static function execute(): void
    {
        self::copyFolderContentToDestination('framework/structure', './');
    }

    public static function copyFolderContentToDestination($sourceDirectory, $destinationDirectory) : void
    {
        copyDirectory($sourceDirectory, $destinationDirectory);

        function copyDirectory($source, $destination) {
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $files = scandir($source);
            if(empty($files)) mkdir($source, $destination);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $sourceFile = $source . '/' . $file;
                    $destinationFile = $destination . '/' . $file;
                    if (is_dir($sourceFile)) {
                        copyDirectory($sourceFile, $destinationFile);
                    } else {
                        copy($sourceFile, $destinationFile);
                    }
                }
            }
        }
    }
}
