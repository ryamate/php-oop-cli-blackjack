<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Dealer.php');
require_once(__DIR__ . '/../lib/DealerPlayer.php');
require_once(__DIR__ . '/../lib/Judge.php');
require_once(__DIR__ . '/../lib/ChipCalculator.php');
require_once(__DIR__ . '/../lib/Deck.php');
require_once(__DIR__ . '/../lib/ManualPlayer.php');
require_once(__DIR__ . '/../lib/Message.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Dealer;
use Blackjack\DealerPlayer;
use Blackjack\Judge;
use Blackjack\ChipCalculator;
use Blackjack\Deck;
use Blackjack\ManualPlayer;

class ChipCalculatorTest extends TestCase
{
    public function testCalcChips(): void
    {
        // ディーラーがバーストしない場合、点数を比較して勝敗を判定されることをテストする

        $players = [];
        $players[] = new ManualPlayer('プレイヤー1', 100, 10, [], 21, 0, 'win');
        $players[] = new ManualPlayer('プレイヤー2', 100, 10, [], 20, 0, 'draw');
        $players[] = new ManualPlayer('プレイヤー3', 100, 10, [], 19, 0, 'lose');

        $chipCalculator = new ChipCalculator();

        $chipCalculator->calcChips($players[0]);
        $this->assertSame(110, $players[0]->getChips());

        $chipCalculator->calcChips($players[1]);
        $this->assertSame(100, $players[1]->getChips());

        $chipCalculator->calcChips($players[2]);
        $this->assertSame(90, $players[2]->getChips());
    }
}
