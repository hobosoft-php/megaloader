<?php

namespace Hobosoft\MegaLoader;

use Psr\Log\LoggerInterface;

define('LOGGER_DEBUG', 1);

//use Psr\Log\AbstractLogger;
//use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * @method void emergency(string|\Stringable $message, array $context = [])
 * @method void alert(string|\Stringable $message, array $context = [])
 * @method void critical(string|\Stringable $message, array $context = [])
 * @method void error(string|\Stringable $message, array $context = [])
 * @method void warning(string|\Stringable $message, array $context = [])
 * @method void notice(string|\Stringable $message, array $context = [])
 * @method void info(string|\Stringable $message, array $context = [])
 * @method void debug(string|\Stringable $message, array $context = [])
 */
class MiniLogger extends MiniDecorator
{
    const string CLASSNAME = __CLASS__;

    protected static array $miniLoggers;
    protected array $items = [];

    public function __construct(protected string $name)
    {
        if(isset(self::$miniLoggers) === false) {
            $this->info("Created first minilogger.");
            self::$miniLoggers = [];
            if(LOGGER_DEBUG) {
                register_shutdown_function(function () {

                    $this->info("shutting down:");
                    //print_r(self::$miniLoggers);
                    
                });
            }
        }
        $this->info("Created minilogger name: {$this->name}, number: ".count(self::$miniLoggers).".");
        self::$miniLoggers[md5(random_bytes(16), false)] = $this;
    }

    public function __destruct()
    {
        if($this->items !== []) {
            if(LOGGER_DEBUG) {
                $this->info(__METHOD__ . ':  destructor called with ' . count($this->items) . ' items not flushed.' . "");
                foreach ($this->items as $item) {
                    $this->info($item['message'] . "");
                }
            }
        }
        else if(LOGGER_DEBUG) {
            $this->info(__METHOD__ . ':  destructor called with all items flushed.' . "");
        }
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->items[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context + ['channel' => 'minilogger-'.$this->name],
        ];
    }

    public function setLogger(mixed $logger): void
    {
        $this->setDecoratedObject($logger);
        foreach ($this->items as $item) {
            $logger->info($item['message'], $item['context']);
        }
    }

    public function __call(string $name, array $arguments): mixed
    {
        $funcs = [ 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' ];
        if(in_array($name, $funcs)) {
            if(isset($this->decorated)) {
                $this->decorated->log($name, ...$arguments);
            }
            else {
                $this->log($name, ...$arguments);
            }
            return null;
        }
        throw new \InvalidArgumentException("Call to undefined method MiniLogger::$name()");
    }
}
