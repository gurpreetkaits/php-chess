<?php

namespace Chess\Tutor;

use Chess\Function\AbstractFunction;
use Chess\UciEngine\UciEngine;
use Chess\UciEngine\Details\Limit;
use Chess\Variant\Classical\Board;

class GoodPgnEvaluation extends AbstractParagraph
{
    public Limit $limit;

    public UciEngine $uciEngine;

    public string $pgn;

    public function __construct(Limit $limit, UciEngine $uciEngine, AbstractFunction $function, Board $board)
    {
        $this->limit = $limit;
        $this->uciEngine = $uciEngine;
        $this->function = $function;
        $this->board = $board;

        $analysis = $uciEngine->analysis($this->board, $limit);
        $clone = $this->board->clone();
        $clone->playLan($clone->turn, $analysis['bestmove']);
        $last = array_slice($clone->history, -1)[0];

        $this->pgn = $last['move']['pgn'];
        $this->paragraph = (new PgnEvaluation($this->pgn, $this->function, $this->board))->paragraph;
    }
}
