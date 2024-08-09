<?php

namespace Hobosoft\MegaLoader;

use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\LogLevel;

class TinyLogger extends PsrAbstractLogger implements LoggerAwareInterface
{
    public static array $tinyLoggers = [];

    private ?PsrLoggerInterface $logger = null;
    private array $items = [];

    public static function create(string $owner): TinyLogger
    {
        if(in_array($owner, TinyLogger::$tinyLoggers)) {
            throw new \Exception("Class '$owner' already registered a TinyLogger.");
        }
        $instance = new TinyLogger();
        TinyLogger::$tinyLoggers[] = [
            'owner' => $owner,
            'instance' => $instance,
        ];
        return $instance;
    }

    public function __construct()
    {
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
    
    public function setLogger(PsrLoggerInterface $logger): void
    {
        if($this->logger !== null) {
            throw new \Exception("Logger already registered in ".__CLASS__.".");
        }
        foreach($this->items as $item) {
            $logger->log($item['level'], $item['message'], $item['context']);
        }
        $this->items = [];
        $this->logger = $logger;
    }
    
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
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if($this->logger instanceof \Psr\Log\LoggerInterface) {
            $this->logger->log($level, $message, $context);
            return;
        }
        $this->items[] = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }
}
