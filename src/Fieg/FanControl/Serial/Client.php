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

        $firstLine = true;
        $buffer =& $this->buffer;

        $this->stream->on('data', function($data, $stream) use ($that, &$firstLine, &$buffer) {
                $buffer .= $data;

                $newline = "\r\n";

                if (false !== $pos = strpos($buffer, $newline)) {
                    $line = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + strlen($newline));

                    // firstline might contain crap
                    if (!$firstLine) {
                        $that->emit('line', array($line, $that));
                    }

                    $firstLine = false;
                }

                $that->emit('data', array($data, $that));
            }
        );
    }
}
