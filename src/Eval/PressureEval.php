<?php

namespace Chess\Eval;

use Chess\Eval\SqCount;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Piece;
use Chess\Variant\Classical\Board;

/**
 * Pressure evaluation.
 *
 * Squares being threatened.
 *
 * @author Jordi Bassagaña
 * @license MIT
 */
class PressureEval extends AbstractEval implements
    DiscreteEvalInterface,
    ExplainEvalInterface
{
    use ExplainEvalTrait;

    const NAME = 'Pressure';

    /**
     * Count squares.
     *
     * @var object
     */
    private object $sqCount;

    /**
     * Constructor.
     *
     * @param \Chess\Variant\Classical\Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;

        $this->sqCount = (new SqCount($board))->count();

        $this->result = [
            Color::W => [],
            Color::B => [],
        ];

        $this->range = [1, 4];

        $this->subject = [
            'The white player',
            'The black player',
        ];

        $this->observation = [
            "is pressuring a little bit more squares than its opponent",
            "is somewhat pressuring more squares than its opponent",
            "is really pressuring more squares than its opponent",
            "is utterly pressuring more squares than its opponent",
        ];

        foreach ($pieces = $this->board->getPieces() as $piece) {
            if ($piece->getId() === Piece::K) {
                $this->result[$piece->getColor()] = [
                    ...$this->result[$piece->getColor()],
                    ...array_intersect(
                        (array) $piece->getMobility(),
                        $this->sqCount->used->{$piece->oppColor()}
                    )
                ];
            } elseif ($piece->getId() === Piece::P) {
                $this->result[$piece->getColor()] = [
                    ...$this->result[$piece->getColor()],
                    ...array_intersect(
                        $piece->getCaptureSqs(),
                        $this->sqCount->used->{$piece->oppColor()}
                    )
                ];
            } else {
                $this->result[$piece->getColor()] = [
                    ...$this->result[$piece->getColor()],
                    ...array_intersect(
                        $piece->sqs(),
                        $this->sqCount->used->{$piece->oppColor()}
                    )
                ];
            }
        }

        $this->explain([
            Color::W => count($this->result[Color::W]),
            Color::B => count($this->result[Color::B]),
        ]);
    }
}
