<?php

namespace Chess\Eval;

use Chess\Eval\SqOutpostEval;
use Chess\Tutor\PiecePhrase;
use Chess\Variant\AbstractBoard;
use Chess\Variant\AbstractPiece;
use Chess\Variant\Classical\PGN\AN\Piece;

class KnightOutpostEval extends AbstractEval implements ElaborateEvalInterface
{
    use ElaborateEvalTrait;

    const NAME = 'Knight outpost';

    public function __construct(AbstractBoard $board)
    {
        $this->board = $board;

        $sqOutpostEval = (new SqOutpostEval($board))->getResult();

        foreach ($sqOutpostEval as $key => $val) {
            foreach ($val as $sq) {
                if ($piece = $this->board->pieceBySq($sq)) {
                    if ($piece->color === $key && $piece->id === Piece::N) {
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
