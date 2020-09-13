<?php

namespace PGNChess\Event;

use PGNChess\PGN\Move;

/**
 * Promotion.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
class Promotion extends AbstractEvent
{
    const NAME = 'promotion';

    public function capture(string $color): int
    {
        if ($this->board->getHistory()) {
            $last = array_slice($this->board->getHistory(), -1)[0];
            $this->result = (int) (
              ($last->move->type == Move::PAWN_PROMOTES || $last->move->type == Move::PAWN_CAPTURES_AND_PROMOTES) &&
              $last->move->color === $color
            );
        }

        return $this->result;
    }
}
