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
use Blackjack\SpecialRule;

class JudgeTest extends TestCase
{
    public function testCheckBurst(): void
    {
        $judge =  new Judge();

        $player1 = new ManualPlayer('プレイヤー1', 0, 0, [], 20, 0, 'hit');
        $this->assertSame(false, $judge->checkBurst($player1));
        $player2 = new ManualPlayer('プレイヤー2', 0, 0, [], 21, 0, 'hit');
        $this->assertSame(false, $judge->checkBurst($player2));
        $player3 = new ManualPlayer('プレイヤー3', 0, 0, [], 22, 0, 'hit');
        $this->assertSame(true, $judge->checkBurst($player3));
    }

    public function testJudgeWinOrLose(): void
    {
        $deck = new Deck();
        $deck->initDeck();

        // ディーラーがバーストしない場合、点数を比較して勝敗を判定されることをテストする
        $dealer1 = new Dealer(
            new DealerPlayer('ディーラー1', 0, 0, [
                ['suit' => 'スペード', 'num' => '10', 'score' => 10],
                ['suit' => 'スペード', 'num' => 'J', 'score' => 10],
            ], 20, 0, 'hit'),
            new Judge(),
            new ChipCalculator(),
            new SpecialRule()
        );

        $players = [];
        $players[] = new ManualPlayer('プレイヤー1', 0, 0, [], 21, 0, 'stand');
        $players[] = new ManualPlayer('プレイヤー2', 0, 0, [], 20, 0, 'stand');
        $players[] = new ManualPlayer('プレイヤー3', 0, 0, [], 19, 0, 'stand');

        $dealer1->getJudge()->judgeWinOrLose($deck, $dealer1, $players);
        $this->assertSame('win', $players[0]->getStatus());
        $this->assertSame('draw', $players[1]->getStatus());
        $this->assertSame('lose', $players[2]->getStatus());

        // ディーラーがバーストした場合、スタンドのプレイヤーは勝ち、バーストのプレイヤーはバースト
        // と判定されることをテストする
        $dealer2 = new Dealer(
            new DealerPlayer('ディーラー2', 0, 0, [
                ['suit' => 'スペード', 'num' => '10', 'score' => 10],
                ['suit' => 'スペード', 'num' => '5', 'score' => 5],
                ['suit' => 'スペード', 'num' => 'J', 'score' => 10],
            ], 25, 0, 'burst'),
            new Judge(),
            new ChipCalculator(),
            new SpecialRule()
        );

        $players = [];
        $players[] = new ManualPlayer('プレイヤー1', 0, 0, [], 21, 0, 'stand');
        $players[] = new ManualPlayer('プレイヤー2', 0, 0, [], 20, 0, 'stand');
        $players[] = new ManualPlayer('プレイヤー3', 0, 0, [], 19, 0, 'stand');
        $players[] = new ManualPlayer('プレイヤー4', 0, 0, [], 25, 0, 'burst');

        $dealer2->getJudge()->judgeWinOrLose($deck, $dealer2, $players);
        $this->assertSame('win', $players[0]->getStatus());
        $this->assertSame('win', $players[1]->getStatus());
        $this->assertSame('win', $players[2]->getStatus());
        $this->assertSame('burst', $players[3]->getStatus());
    }
}
