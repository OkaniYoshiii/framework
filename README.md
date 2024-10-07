# LIGHTWEIGHT PHP FRAMEWORK

## Requirements

- PHP v8.2 and higher
- Composer
- Nodejs

## Initialize project

First, run the following command :

`composer require okaniyoshii/framework`

Next, you'll have to add a `.php` file that you will then run in the shell.
I recommand creating a `cmd` folder with a `shell` file inside it (without extension)

Then, add the following lines of code into that file : 

```php

<?php

use OkaniYoshiii\Framework\ShellProgram;

require_once __DIR__ . '/../vendor/autoload.php';

ShellProgram::start($argv);

```

Once it's done, run the following command :

`php cmd/shell app:init`

This should create a project folder structure for you !

Now, you should be able to see a 'Hello, world !' page in your browser.

## Troubleshooting

1 - File permissions errors : When initializing project folder structure, it's possible that permission file errors occurs. In that case, you can either change file permissions to allow copy of files or copy files in the `structure` folder of the framework and paste them in your project directory.