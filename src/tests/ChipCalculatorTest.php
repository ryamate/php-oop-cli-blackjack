<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Game.php');
require_once(__DIR__ . '/../lib/ChipCalculator.php');
require_once(__DIR__ . '/../lib/ManualPlayer.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Game;
use Blackjack\ChipCalculator;
use Blackjack\ManualPlayer;

class ChipCalculatorTest extends TestCase
{
    public function testCalcChips(): void
    {
        // ディーラーがバーストしない場合、点数を比較して勝敗を判定されることをテストする

        $game = new Game();

        $players = [];
        $players[] = new ManualPlayer('プレイヤー1', 100, 10, [], 21, 0, 'win');
        $players[] = new ManualPlayer('プレイヤー2', 100, 10, [], 20, 0, 'draw');
        $players[] = new ManualPlayer('プレイヤー3', 100, 10, [], 19, 0, 'lose');

        $chipCalculator = new ChipCalculator();

        $chipCalculator->calcChips($game, $players[0]);
        $this->assertSame(110, $players[0]->getChips());

        $chipCalculator->calcChips($game, $players[1]);
        $this->assertSame(100, $players[1]->getChips());

        $chipCalculator->calcChips($game, $players[2]);
        $this->assertSame(90, $players[2]->getChips());
    }
}
