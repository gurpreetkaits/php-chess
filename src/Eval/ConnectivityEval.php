<?php

namespace Chess\Eval;

use Chess\Eval\SqCount;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Piece;
use Chess\Variant\Classical\Board;

/**
 * Connectivity.
 *
 * @author Jordi Bassagaña
 * @license GPL
 */
class ConnectivityEval extends AbstractEval
{
    const NAME = 'Connectivity';

    private object $sqCount;

    protected array $phrase = [
        Color::W => [
            [
                'diff' => 4,
                'meanings' => [
                    "The white pieces are totally better connected.",
                ],
            ],
            [
                'diff' => 3,
                'meanings' => [
                    "The white pieces are remarkably better connected.",
                ],
            ],
            [
                'diff' => 2,
                'meanings' => [
                    "The white pieces are somewhat better connected.",
                ],
            ],
            [
                'diff' => 1,
                'meanings' => [
                    "The white pieces are slightly better connected.",
                ],
            ],
        ],
        Color::B => [
            [
                'diff' => -4,
                'meanings' => [
                    "The black pieces are totally better connected.",
                ],
            ],
            [
                'diff' => -3,
                'meanings' => [
                    "The black pieces are remarkably better connected.",
                ],
            ],
            [
                'diff' => -2,
                'meanings' => [
                    "The black pieces are somewhat better connected.",
                ],
            ],
            [
                'diff' => -1,
                'meanings' => [
                    "The black pieces are slightly better connected.",
                ],
            ],
        ],
    ];

    public function __construct(Board $board)
    {
        $this->board = $board;
        $this->sqCount = (new SqCount($board))->count();

        $this->color(Color::W);
        $this->color(Color::B);

        $this->explain($this->result);
    }

    private function color(string $color): void
    {
        foreach ($this->board->getPieces($color) as $piece) {
            switch ($piece->getId()) {
                case Piece::K:
                    $this->result[$color] += count(
                        array_intersect((array) $piece->getMobility(),
                        $this->sqCount->used->{$color})
                    );
                    break;
                case Piece::N:
                    $this->result[$color] += count(
                        array_intersect($piece->getMobility(),
                        $this->sqCount->used->{$color})
                    );
                    break;
                case Piece::P:
                    $this->result[$color] += count(
                        array_intersect($piece->getCaptureSqs(),
                        $this->sqCount->used->{$color})
                    );
                    break;
                default:
                    foreach ((array) $piece->getMobility() as $key => $val) {
                        foreach ($val as $sq) {
                            if (in_array($sq, $this->sqCount->used->{$color})) {
                                $this->result[$color] += 1;
                                break;
                            } elseif (in_array($sq, $this->sqCount->used->{$piece->oppColor()})) {
                                break;
                            }
                        }
                    }
                    break;
            }
        }
    }

    private function explain(array $result): void
    {
        if ($sentence = $this->predictable($result)) {
            $this->phrases[] = $sentence;
        }
    }
}
