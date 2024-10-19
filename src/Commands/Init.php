<?php 

namespace OkaniYoshiii\Framework\Commands;

use OkaniYoshiii\Framework\App;
use OkaniYoshiii\Framework\Contracts\Abstracts\ShellCommand;

class Init extends ShellCommand
{
    public const CMD_NAME = 'app:init';

    protected static function configureRequirements(): array
    {
        return [];
    }

    public static function execute(): void
    {
        self::copyFolderContentToDestination(App::FRAMEWORK_DIR . 'structure/', './');
    }

    public static function copyFolderContentToDestination($source, $destination) : void
    {
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

        copyDirectory($source, $destination);
    }
}
