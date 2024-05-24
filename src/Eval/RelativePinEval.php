<?php

namespace Chess\Eval;

use Chess\Eval\PressureEval;
use Chess\Piece\AbstractPiece;
use Chess\Tutor\PiecePhrase;
use Chess\Variant\Classical\Board;
use Chess\Variant\Classical\PGN\AN\Piece;

class RelativePinEval extends AbstractEval implements
    ElaborateEvalInterface,
    ExplainEvalInterface
{
    use ElaborateEvalTrait;
    use ExplainEvalTrait;

    const NAME = 'Relative pin';

    public function __construct(Board $board)
    {
        $this->board = $board;

        $this->range = [1, 9];

        $this->subject = [
            'White',
            'Black',
        ];

        $this->observation = [
            "has a slight relative pin advantage",
            "has a moderate relative pin advantage",
            "has a total relative pin advantage",
        ];

        $pressureEval = (new PressureEval($this->board))->getResult();

        foreach ($this->board->getPieces() as $piece) {
            if ($piece->getId() !== Piece::K && $piece->getId() !== Piece::Q) {
                if (!$piece->isPinned()) {
                    $clone = unserialize(serialize($this->board));
                    $clone->detach($clone->getPieceBySq($piece->getSq()));
                    $clone->refresh();
                    $newPressureEval = (new PressureEval($clone))->getResult();
                    $arrayDiff = array_diff($newPressureEval[$piece->oppColor()] , $pressureEval[$piece->oppColor()]);
                    foreach ($arrayDiff as $sq) {
                        $diff = self::$value[$clone->getPieceBySq($sq)->getId()] - self::$value[$piece->getId()];
                        if ($diff > 0) {
                            $this->result[$piece->oppColor()] += round($diff, 2);
                            $this->elaborate($piece);
                        }
                    }
                }
            }
        }

        $this->explain($this->result);
    }

    private function elaborate(AbstractPiece $piece): void
    {
        $phrase = PiecePhrase::create($piece);

        $this->elaboration[] = ucfirst("$phrase is pinned shielding a piece that is more valuable than the attacking piece.");
    }
}
