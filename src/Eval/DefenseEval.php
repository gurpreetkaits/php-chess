<?php

namespace Chess\Eval;

use Chess\Eval\ProtectionEval;
use Chess\Piece\AbstractPiece;
use Chess\Tutor\PiecePhrase;
use Chess\Variant\Classical\Board;
use Chess\Variant\Classical\PGN\AN\Piece;

class DefenseEval extends AbstractEval implements ElaborateEvalInterface
{
    use ElaborateEvalTrait;

    const NAME = 'Defense';

    public function __construct(Board $board)
    {
        $this->board = $board;

        $protectionEval = new ProtectionEval($this->board);

        foreach ($this->board->getPieces() as $piece) {
            if ($piece->getId() !== Piece::K) {
                if (!empty($piece->attackingPieces())) {
                    $diffPhrases = [];
                    $clone = unserialize(serialize($this->board));
                    $clone->detach($clone->getPieceBySq($piece->getSq()));
                    $clone->refresh();
                    $newProtectionEval = new ProtectionEval($clone);
                    $diffResult = $newProtectionEval->getResult()[$piece->oppColor()] - $protectionEval->getResult()[$piece->oppColor()];
                    if ($diffResult > 0) {
                        foreach ($newProtectionEval->getElaboration() as $key => $val) {
                            if (!in_array($val, $protectionEval->getElaboration())) {
                                $diffPhrases[] = $val;
                            }
                        }
                        $this->result[$piece->oppColor()] += round($diffResult, 2);
                        $this->elaborate($piece, $diffPhrases);
                    }
                }
            }
        }
    }

    /**
     * Elaborate on the result.
     *
     * @param \Chess\Piece\AbstractPiece $piece
     * @param array $diffPhrases
     */
    private function elaborate(AbstractPiece $piece, array $diffPhrases): void
    {
        $phrase = PiecePhrase::create($piece);
        $phrase = "If $phrase moved, ";
        $count = count($diffPhrases);
        if ($count === 1) {
            $diffPhrase = mb_strtolower($diffPhrases[0]);
            $rephrase = str_replace('is unprotected', 'may well be exposed to attack', $diffPhrase);
            $phrase .= $rephrase;
        } elseif ($count > 1) {
            $phrase .= 'these pieces may well be exposed to attack: ';
            $rephrase = '';
            foreach ($diffPhrases as $diffPhrase) {
                $rephrase .= str_replace(' is unprotected.', ', ', $diffPhrase);
            }
            $phrase .= $rephrase;
            $phrase = str_replace(', The', ', the', $phrase);
            $phrase = substr_replace(trim($phrase), '.', -1);
        }

        $this->elaboration[] = $phrase;
    }
}
