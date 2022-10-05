<?php

namespace Chess\Variant\Classical\FEN;

/**
 * FEN string to PGN.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
class StrToPgn extends AbstractStrToPgn
{
    public function __construct(string $fromFen, string $toFen)
    {
        $this->board = (new StrToBoard($fromFen))->create();

        parent::__construct($fromFen, $toFen);
    }
    
    protected function find(array $legal): ?string
    {
        foreach ($legal as $key => $val) {
            if (str_starts_with($this->toFen, current($val))) {
                return key($val);
            }
        }

        return null;
    }
}
