<?php

namespace Chess\Eval;

use Chess\Eval\PressureEval;
use Chess\Tutor\PiecePhrase;
use Chess\Variant\AbstractBoard;
use Chess\Variant\AbstractPiece;
use Chess\Variant\Classical\PGN\AN\Piece;

class RelativePinEval extends AbstractEval implements
    ElaborateEvalInterface,
    ExplainEvalInterface
{
    use ElaborateEvalTrait;
    use ExplainEvalTrait;

    const NAME = 'Relative pin';

    public function __construct(AbstractBoard $board)
    {
        $this->board = $board;

        $this->range = [1, 6.8];

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

        foreach ($this->board->pieces() as $piece) {
            if (
                $piece->id !== Piece::K &&
                $piece->id !== Piece::Q &&
                !$piece->isPinned()
            ) {
                $clone = $this->board->clone();
                $clone->detach($clone->pieceBySq($piece->sq));
                $clone->refresh();
                $newPressureEval = (new PressureEval($clone))->getResult();
                foreach (array_diff($newPressureEval[$piece->oppColor()], $pressureEval[$piece->oppColor()]) as $sq) {
                    foreach ($clone->pieceBySq($sq)->attacking() as $newAttacking) {
                        foreach ($piece->attacking() as $attacking) {
                            if ($newAttacking->sq === $attacking->sq) {
                                $valDiff = self::$value[$attacking->id] - self::$value[$clone->pieceBySq($sq)->id];
                                if ($valDiff < 0) {
                                    $this->result[$piece->oppColor()] += abs(round($valDiff, 2));
                                    $this->elaborate($piece);
                                }
                            }
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
