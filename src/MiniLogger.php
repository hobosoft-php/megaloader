<?php

namespace Hobosoft\MegaLoader;

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

class MiniLogger //extends AbstractLogger implements PsrLoggerInterface
{
    protected static array $miniLoggers;

    protected array $items = [];

    public function __construct()
    {
        if(isset(self::$miniLoggers) === false) {
            print("Created first minilogger.\n");
            self::$miniLoggers = [];
            register_shutdown_function(function () {
                print("shutting down:\n");
                foreach(\Hobosoft\MegaLoader\MiniLogger::$miniLoggers as $k => $v) {
                    print("minilogger $k: ".count($v->items)."\n");
                }
            });
        }
        print("Created minilogger number".count(self::$miniLoggers).".\n");
        self::$miniLoggers[random_bytes(16)] = $this;
    }

    public function __destruct()
    {
        if($this->items !== []) {
            print(__METHOD__.':  destructor called with '.count($this->items).' items not flushed.'."\n");
            foreach($this->items as $item) {
                print($item['message']."\n");
            }
        }
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->items[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }

    public function __call(string $name, array $arguments): void
    {
        $funcs = [ 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' ];
        if(in_array($name, $funcs)) {
            $this->log($name, ...$arguments);
            return;
        }
        throw new \InvalidArgumentException("Call to undefined method MiniLogger::$name()");
    }

    /*
        public function emergency(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::EMERGENCY, $message, $context);
        }
        public function alert(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::ALERT, $message, $context);
        }
        public function critical(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::CRITICAL, $message, $context);
        }
        public function error(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::ERROR, $message, $context);
        }
        public function warning(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::WARNING, $message, $context);
        }
        public function notice(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::NOTICE, $message, $context);
        }
        public function info(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::INFO, $message, $context);
        }
        public function debug(string|\Stringable $message, array $context = []): void
        {
            $this->log(LogLevel::DEBUG, $message, $context);
        }
    */
}