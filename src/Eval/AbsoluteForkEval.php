<?php

namespace Chess\Eval;

use Chess\Piece\AbstractPiece;
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
                $pieceValue = self::$value[$piece->getId()];
                foreach ($piece->attackedPieces() as $attackedPiece) {
                    if ($attackedPiece->getId() !== Piece::K) {
                        $attackedPieceValue = self::$value[$attackedPiece->getId()];
                        if ($pieceValue < $attackedPieceValue) {
                            $this->result[$piece->getColor()] += $attackedPieceValue;
                            $this->elaborate($attackedPiece);
                        }
                    }
                }
            }
        }
    }

    private function elaborate(AbstractPiece $piece): void
    {
        $phrase = PiecePhrase::create($piece);

        $this->elaboration[] = "Absolute fork attack on {$phrase}.";
    }
}
