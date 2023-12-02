<?php

namespace Chess\Eval;

use Chess\Tutor\PiecePhrase;
use Chess\Variant\Classical\Board;
use Chess\Variant\Classical\PGN\AN\Piece;

class AbsoluteForkEval extends AbstractEval
{
    const NAME = 'Absolute fork';

    public function __construct(Board $board)
    {
        $this->board = $board;

        foreach ($this->board->getPieces() as $piece) {
            if ($piece->isAttackingKing()) {
                foreach ($piece->attackedPieces() as $attackedPiece) {
                    if ($attackedPiece->getId() !== Piece::K) {
                        $this->result[$piece->getColor()] += self::$value[$attackedPiece->getId()];
                        $this->explain($attackedPiece);
                    }
                }
            }
        }
    }

    private function explain($subject, $target = null)
    {
        $phrase = PiecePhrase::predictable($subject);
        $this->phrases[] = "Absolute fork attack on {$phrase}.";

        return $this->phrases;
    }
}
