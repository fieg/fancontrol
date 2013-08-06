<?php

/*
 * @author Jeroen Fiege <jeroen@webcreate.nl>
 * @copyright Webcreate (http://webcreate.nl)
 */

namespace Fieg\FanControl\Serial;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;

class Client extends EventEmitter
{
    protected $stream;
    protected $loop;
    protected $buffer = '';

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function listen($port)
    {
        $stream = @fopen($port, 'r');
        if (false === $stream) {
            $message = "Could not bind to $port";
            throw new \Exception($message);
        }
        stream_set_blocking($stream, 0);

        $this->stream = new Stream($stream, $this->loop);

        $that = $this;

        $this->stream->on('data', function($data, $stream) use ($that) {
                $that->buffer .= $data;

                $newline = "\r\n";

                if (false !== $pos = strpos($this->buffer, $newline)) {
                    $line = substr($this->buffer, 0, $pos);
                    $this->buffer = substr($this->buffer, $pos + strlen($newline));

                    $that->emit('line', array($line, $that));
                }

                $that->emit('data', array($data, $that));
            }
        );
    }
}