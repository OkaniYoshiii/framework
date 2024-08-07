<?php

namespace Framework;

use App\Traits\SingletonTrait;
use Throwable;

class ExceptionHandler
{
    private array $handlers = [];

    use SingletonTrait;

    public function start()
    {
        set_exception_handler(function(Throwable $exception) {
            foreach($this->handlers as $handler)
            {
                if($exception instanceof $handler['exception']) {
                    $handler['callback']($exception);
                    return;
                }
            }

            $this->logError($exception);

            http_response_code(500);
        });
    }

    public function addHandler(string $exception, callable $callback)
    {
       $this->handlers[] = ['exception' => $exception, 'callback' => $callback];
    }

    private function logError(Throwable $exception) : void
    {
        $logMessage = date('Y-m-d H:s', strtotime('now')) . ' : ';
        $logMessage .= 'Uncaught Exception in ' . $exception->getFile() . ', line ' . $exception->getLine() . ' : ' . $exception->getMessage();
        $logMessage .= PHP_EOL;
        $logMessage .= $exception->getTraceAsString();
        $logMessage .= PHP_EOL;
        $logMessage .= PHP_EOL;

        error_log($logMessage, 3, '../logs/app.exceptions.txt');
    }
}