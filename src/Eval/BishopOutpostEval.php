<?php

namespace Chess\Eval;

use Chess\Eval\SqOutpostEval;
use Chess\Piece\AbstractPiece;
use Chess\Tutor\PiecePhrase;
use Chess\Variant\Classical\PGN\AN\Piece;
use Chess\Variant\Classical\Board;

class BishopOutpostEval extends AbstractEval
{
    use ElaborateEvalTrait;
    
    const NAME = 'Bishop outpost';

    public function __construct(Board $board)
    {
        $this->board = $board;

        $sqOutpostEval = (new SqOutpostEval($this->board))->getResult();

        foreach ($sqOutpostEval as $key => $val) {
            foreach ($val as $sq) {
                if ($piece = $this->board->getPieceBySq($sq)) {
                    if ($piece->getColor() === $key && $piece->getId() === Piece::B) {
                        $this->result[$key] += 1;
                        $this->elaborate($piece);
                    }
                }
            }
        }
    }

    private function elaborate(AbstractPiece $piece): void
    {
        $phrase = PiecePhrase::create($piece);

        $this->elaboration[] = ucfirst("$phrase is nicely placed on an outpost.");
    }
}
