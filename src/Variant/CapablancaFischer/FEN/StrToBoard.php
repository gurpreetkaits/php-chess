<?php

namespace Chess\Variant\CapablancaFischer\FEN;

use Chess\Exception\UnknownNotationException;
use Chess\Piece\PieceArray;
use Chess\Variant\CapablancaFischer\Board;
use Chess\Variant\CapablancaFischer\Rule\CastlingRule;
use Chess\Variant\Capablanca\FEN\Str;
use Chess\Variant\Capablanca\PGN\AN\Square;
use Chess\Variant\Classical\FEN\StrToBoard as ClassicalFenStrToBoard;

/**
 * StrToBoard
 *
 * Converts a FEN string to a chessboard object.
 *
 * @author Jordi Bassagaña
 * @license GPL
 */
class StrToBoard extends ClassicalFenStrToBoard
{
    private array $startPos;

    public function __construct(string $string, array $startPos)
    {
        $this->size = Square::SIZE;
        $this->fenStr = new Str();
        $this->string = $this->fenStr->validate($string);
        $this->fields = array_filter(explode(' ', $this->string));
        $this->castlingAbility = $this->fields[2];
        $this->startPos = $startPos;
        $this->castlingRule =  (new CastlingRule($this->startPos))->getRule();
    }

    public function create(): Board
    {
        try {
            $pieces = (new PieceArray(
                $this->fenStr->toAsciiArray($this->fields[0]),
                $this->size,
                $this->castlingRule
            ))->getArray();
            $board = (new Board(
                $this->startPos,
                $pieces,
                $this->castlingAbility
            ))->setTurn($this->fields[1])->setStartFen($this->string);
        } catch (\Throwable $e) {
            throw new UnknownNotationException();
        }

        return $this->enPassant($board);
    }
}
