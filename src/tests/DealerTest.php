<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/Dealer.php');
require_once(__DIR__ . '/../lib/Deck.php');
require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Message.php');

use PHPUnit\Framework\TestCase;
use Blackjack\Dealer;
use Blackjack\Player;

class DealerTest extends TestCase
{
    public function testDealOutFirstHand(): void
    {
        $player = new Player('あなた');
        $dealer = new Dealer('ディーラー');
        $dealer->dealOutFirstHand($player);

        // カードの枚数をテストする
        $this->assertSame(2, count($player->getHand()));
    }

    public function testDealOneCard(): void
    {
        $player = new Player('あなた');
        $dealer = new Dealer('ディーラー');
        $player = $dealer->dealOneCard($player);

        // 1枚引いたカードの枚数をテストする
        $this->assertSame(1, count($player->getHand()));

        // 複数枚引いたカードの枚数をテストする
        $player = $dealer->dealOneCard($player);
        $this->assertSame(2, count($player->getHand()));
    }

    public function testCheckBurst(): void
    {
        $dealer = new Dealer('ディーラー');

        $player1 = new Player('プレイヤー1', [], 20, 0, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player1));
        $player2 = new Player('プレイヤー2', [], 21, 0, 'hit');
        $this->assertSame(false, $dealer->checkBurst($player2));
        $player3 = new Player('プレイヤー3', [], 22, 0, 'hit');
        $this->assertSame(true, $dealer->checkBurst($player3));
    }

    public function testJudgeWinOrLose(): void
    {
        // ディーラーがバーストしない場合、点数を比較して勝敗を判定されることをテストする
        $dealer1 = new Dealer('ディーラー1', [
            ['suit' => 'スペード', 'num' => '10', 'score' => 10],
            ['suit' => 'スペード', 'num' => 'J', 'score' => 10],
        ], 20, 0, 'hit');

        $players = [];
        $players[] = new Player('プレイヤー1', [], 21, 0, 'stand');
        $players[] = new Player('プレイヤー2', [], 20, 0, 'stand');
        $players[] = new Player('プレイヤー3', [], 19, 0, 'stand');

        $dealer1->judgeWinOrLose($players);
        $this->assertSame('win', $players[0]->getStatus());
        $this->assertSame('draw', $players[1]->getStatus());
        $this->assertSame('lose', $players[2]->getStatus());

        // ディーラーがバーストした場合、スタンドのプレイヤーは勝ち、バーストのプレイヤーはバースト
        // と判定されることをテストする
        $dealer2 = new Dealer('ディーラー2', [
            ['suit' => 'スペード', 'num' => '10', 'score' => 10],
            ['suit' => 'スペード', 'num' => '5', 'score' => 5],
            ['suit' => 'スペード', 'num' => 'J', 'score' => 10],
        ], 25, 0, 'burst');

        $players = [];
        $players[] = new Player('プレイヤー1', [], 21, 0, 'stand');
        $players[] = new Player('プレイヤー2', [], 20, 0, 'stand');
        $players[] = new Player('プレイヤー3', [], 19, 0, 'stand');
        $players[] = new Player('プレイヤー4', [], 25, 0, 'burst');

        $dealer2->judgeWinOrLose($players);
        $this->assertSame('win', $players[0]->getStatus());
        $this->assertSame('win', $players[1]->getStatus());
        $this->assertSame('win', $players[2]->getStatus());
        $this->assertSame('burst', $players[3]->getStatus());
    }

    public function testSelectHitOrStand(): void
    {
        $dealer1 = new Dealer('ディーラー1', [], 18, 0, 'hit');
        $this->assertSame('N', $dealer1->selectHitOrStand());

        $dealer2 = new Dealer('ディーラー2', [], 17, 0, 'hit');
        $this->assertSame('N', $dealer2->selectHitOrStand());

        $dealer3 = new Dealer('ディーラー1', [], 16, 0, 'hit');
        $this->assertSame('Y', $dealer3->selectHitOrStand());
    }
}
