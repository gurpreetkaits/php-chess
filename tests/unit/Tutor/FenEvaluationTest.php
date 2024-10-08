<?php

namespace Chess\Tests\Unit\Tutor;

use Chess\FenToBoardFactory;
use Chess\Function\CompleteFunction;
use Chess\Play\SanPlay;
use Chess\Tutor\FenEvaluation;
use Chess\Tests\AbstractUnitTestCase;
use Chess\Variant\Capablanca\Board as CapablancaBoard;

class FenEvaluationTest extends AbstractUnitTestCase
{
    static private CompleteFunction $function;

    public static function setUpBeforeClass(): void
    {
        self::$function = new CompleteFunction();
    }

    /**
     * @test
     */
    public function A08()
    {
        $expected = [
            "Black has a slightly better control of the center.",
            "The white pieces are totally better connected.",
            "Black has a moderate space advantage.",
            "White should move one of the pawns in front of the king as long as there is a need to be guarded against back-rank threats.",
            "Overall, 1 heuristic evaluation feature is favoring White while 3 are favoring Black.",
        ];

        $A08 = file_get_contents(self::DATA_FOLDER.'/sample/A08.pgn');
        $board = (new SanPlay($A08))->validate()->board;

        $paragraph = (new FenEvaluation(self::$function, $board))->paragraph;

        $this->assertSame($expected, $paragraph);
    }

    /**
     * @test
     */
    public function capablanca_f4()
    {
        $expected = [
            "White is totally controlling the center.",
            "The black pieces are totally better connected.",
            "White has a total space advantage.",
            "The white player is pressuring a little bit more squares than its opponent.",
            "Black should move one of the pawns in front of the king as long as there is a need to be guarded against back-rank threats.",
            "Overall, 4 heuristic evaluation features are favoring White while 1 is favoring Black.",
        ];

        $board = FenToBoardFactory::create(
            'rnabqkbcnr/pppppppppp/10/10/5P4/10/PPPPP1PPPP/RNABQKBCNR b KQkq f3',
            new CapablancaBoard()
        );

        $paragraph = (new FenEvaluation(self::$function, $board))->paragraph;

        $this->assertSame($expected, $paragraph);
    }
}
