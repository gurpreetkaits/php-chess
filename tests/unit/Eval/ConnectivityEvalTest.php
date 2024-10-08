<?php

namespace Chess\Tests\Unit\Eval\Material;

use Chess\Eval\ConnectivityEval;
use Chess\Play\SanPlay;
use Chess\Tests\AbstractUnitTestCase;
use Chess\Variant\Classical\Board;
use Chess\Variant\Classical\FEN\StrToBoard;

class ConnectivityEvalTest extends AbstractUnitTestCase
{
    /**
     * @test
     */
    public function start()
    {
        $expected = [
            'w' => 20,
            'b' => 20,
        ];

        $result = (new ConnectivityEval(new Board()))->getResult();

        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function C60()
    {
        $expectedResult = [
            'w' => 19,
            'b' => 23,
        ];

        $expectedPhrase = [
            "The black pieces are totally better connected.",
        ];

        $C60 = file_get_contents(self::DATA_FOLDER.'/sample/C60.pgn');
        $board = (new SanPlay($C60))->validate()->board;
        $connectivityEval = new ConnectivityEval($board);

        $this->assertSame($expectedResult, $connectivityEval->getResult());
        $this->assertSame($expectedPhrase, $connectivityEval->getExplanation());
    }
}
