<?php

namespace Blackjack;

require_once('Player.php');
require_once('NonPlayerCharacter.php');
require_once('Dealer.php');
require_once('Message.php');

use Blackjack\Player;
use Blackjack\NonPlayerCharacter;
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
        private ?Dealer $dealer = null,
        private array $players = [],
    ) {
        $this->dealer = $dealer ?? new Dealer('ディーラー');
        $this->players[] =  new Player('あなた');
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
                    $this->players[] = new NonPlayerCharacter($nPCName);
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
        foreach ($this->players as &$player) {
            $this->dealer->dealOutFirstHand($player);
        }
        unset($player);
        $this->dealer->dealOutFirstHand($this->dealer);

        $startMessage = Message::getStartMessage();
        foreach ($this->players as $player) {
            $startMessage .= Message::getFirstHandMessage($player);
        }
        unset($player);
        $startMessage .= Message::getDealerFirstHandMessage($this->dealer);
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
            $player->action($this->dealer);
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
        $this->dealer->judgeWinOrLose($this->players);
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
