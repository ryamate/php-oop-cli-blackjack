<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');
require_once('DealerPlayer.php');
require_once('ManualPlayer.php');
require_once('AutoPlayer.php');
require_once('Dealer.php');
require_once('Message.php');

use Blackjack\Deck;
use Blackjack\Player;
use Blackjack\DealerPlayer;
use Blackjack\ManualPlayer;
use Blackjack\AutoPlayer;
use Blackjack\Dealer;
use Blackjack\Message;

/**
 * ゲームクラス
 */
class Game
{
    /**
     * コンストラクタ
     *
     * @param Dealer $dealer
     * @param array<int,Player> $players
     */
    public function __construct(
        private ?Deck $deck = null,
        private ?Dealer $dealer = null,
        private array $players = [],
    ) {
        $this->deck = $deck ?? new Deck();
        $this->dealer = $dealer ?? new Dealer(new DealerPlayer('ディーラー'));
        $this->players[] =  new ManualPlayer('あなた');
    }

    /**
     * ブラックジャックをプレイする
     *
     * @return void
     */
    public function play(): void
    {
        $this->set();
        $this->start();
        $this->action();
        $this->result();
        $this->end();
    }

    /**
     * ブラックジャックの設定（人数）をする
     *
     * @return void
     */
    private function set(): void
    {
        echo Message::getSettingMessage();
        $inputNumOfPlayer = 0;
        while ($inputNumOfPlayer !== 1 && $inputNumOfPlayer !== 2 && $inputNumOfPlayer !== 3) {
            // プレイヤー人数について、 1, 2, 3 での入力を求める
            echo Message::getInputNumOfPlayerMessage();
            $inputNumOfPlayer = (int)trim(fgets(STDIN));
            if ($inputNumOfPlayer === 1 || $inputNumOfPlayer === 2 || $inputNumOfPlayer === 3) {
                $numOfNPC = $inputNumOfPlayer - 1;
                for ($i = 0; $i < $numOfNPC; $i++) {
                    $nPCName = 'NPC' . (string)($i + 1);
                    $this->players[] = new AutoPlayer($nPCName);
                }
            } else {
                echo Message::getSettingInputErrorMessage();
            }
        }
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    private function start(): void
    {
        $this->deck->initDeck();
        foreach ($this->players as &$player) {
            $this->dealer->dealOutFirstHand($this->deck, $player);
        }
        unset($player);
        $this->dealer->dealOutFirstHand($this->deck, $this->dealer->getDealerPlayer());

        $startMessage = Message::getStartMessage();
        foreach ($this->players as $player) {
            $startMessage .= Message::getFirstHandMessage($player);
        }
        unset($player);
        $startMessage .= Message::getDealerFirstHandMessage($this->dealer->getDealerPlayer());
        echo $startMessage;
    }

    /**
     * 各プレイヤーのアクションについて進行する
     *
     * @return void
     */
    private function action(): void
    {
        foreach ($this->players as &$player) {
            $player->action($this->deck, $this->dealer);
            if ($player->getStatus() === 'burst') {
                echo Message::getLoseByBurstMessage($player);
            }
        }
        unset($player);
    }

    /**
     * ディーラーは勝敗を判定する
     *
     * @return void
     */
    private function result(): void
    {
        $this->dealer->judgeWinOrLose($this->deck, $this->players);
    }

    /**
     * ディーラーは勝敗を判定する
     *
     * @return void
     */
    private function end(): void
    {
        echo Message::getEndMessage();
    }
}
