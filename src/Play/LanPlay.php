<?php

namespace Chess\Play;

use Chess\Exception\PlayException;
use Chess\Variant\Classical\Board;

class LanPlay extends AbstractPlay
{
    protected array $moves;

    public function __construct(string $movetext, Board $board = null)
    {
        $this->initialBoard = $board ?? new Board();
        $this->board = $board ?? new Board();
        $this->fen = [$this->board->toFen()];
        $this->moves = array_values(array_filter(explode(' ', $movetext)));
    }

    public function validate(): LanPlay
    {
        foreach ($this->moves as $key => $val) {
            if ($key % 2 === 0) {
                if (!$this->board->playLan('w', $val)) {
                    throw new PlayException();
                }
            } else {
                if (!$this->board->playLan('b', $val)) {
                    throw new PlayException();
                }
            }
            $this->fen[] = $this->board->toFen();
        }

        return $this;
    }
}
