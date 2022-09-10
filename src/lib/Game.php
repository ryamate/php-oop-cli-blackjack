<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');
require_once('DealerPlayer.php');
require_once('ManualPlayer.php');
require_once('AutoPlayer.php');
require_once('Dealer.php');
require_once('Judge.php');
require_once('ChipCalculator.php');
require_once('Message.php');

use Blackjack\Deck;
use Blackjack\Player;
use Blackjack\DealerPlayer;
use Blackjack\ManualPlayer;
use Blackjack\AutoPlayer;
use Blackjack\Dealer;
use Blackjack\Judge;
use Blackjack\ChipCalculator;
use Blackjack\Message;

/**
 * ゲームクラス
 */
class Game
{
    public const CONTINUE = 'continue';
    public const STOP = 'stop';

    /**
     * コンストラクタ
     *
     * @param Deck $deck デッキ
     * @param Dealer $dealer ディーラー
     * @param array<int,ManualPlayer|AutoPlayer> $players プレイヤー
     * @param string $status ゲームを続けるか、やめるかの状態
     */
    public function __construct(
        private ?Deck $deck = null,
        private ?Dealer $dealer = null,
        private array $players = [],
        private string $status = self::CONTINUE,
    ) {
        $this->deck = $deck ?? new Deck();
        $this->dealer = $dealer ?? new Dealer(
            new DealerPlayer('ディーラー'),
            new Judge(),
            new ChipCalculator()
        );
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
        while ($this->status === self::CONTINUE) {
            $this->placeYourBets();
            $this->start();
            $this->action();
            $this->result();
            $this->calcChips();
            $this->selectContinueOrStop();
        }
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
     * ベットする額を決める
     *
     * @return void
     */
    private function placeYourBets(): void
    {
        // TODO: 追記）chips = 0 になった人の処理

        foreach ($this->players as &$player) {
            $player->bet();
        }
        unset($player);
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    private function start(): void
    {
        $this->deck->initDeck();
        foreach ($this->players as $player) {
            $this->dealer->dealOutFirstHand($this->deck, $player);
        }
        $this->dealer->dealOutFirstHand($this->deck, $this->dealer->getDealerPlayer());

        $startMessage = Message::getStartMessage();
        foreach ($this->players as $player) {
            $startMessage .= Message::getFirstHandMessage($player);
        }
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
        foreach ($this->players as $player) {
            $player->action($this->deck, $this->dealer);
            if ($player->getStatus() === Player::BURST) {
                echo Message::getLoseByBurstMessage($player);
            }
        }
    }

    /**
     * ディーラーは勝敗を判定する
     *
     * @return void
     */
    private function result(): void
    {
        $this->dealer->getJudge()->judgeWinOrLose(
            $this->deck,
            $this->dealer,
            $this->players
        );
    }

    /**
     * 勝敗、特殊ルールに応じたプレイヤーのチップ残高を算出し、プレイヤーとディーラーのゲーム中のステータスをリセットする
     *
     * @return void
     */
    private function calcChips(): void
    {
        foreach ($this->players as $player) {
            $this->dealer->getChipCalculator()->calcChips($player);
        }
        $this->dealer->getDealerPlayer()->reset();
    }

    /**
     * ゲームを続けるか、やめるかを選択する
     *
     * @return void
     */
    private function selectContinueOrStop(): void
    {
        $inputYesOrNo = '';
        foreach ($this->players as $num => $player) {
            if ($player->getChips() === 0 && $player->getName() === 'あなた') {
                echo 'あなたは、チップの残高がなくなりました。' . PHP_EOL;
                $this->status = self::STOP;
            } elseif ($player->getChips() === 0) {
                echo $player->getName() . 'は、チップの残高がなくなりました。' . PHP_EOL;
                echo $player->getName() . 'は、退出しました。' . PHP_EOL;
                unset($this->players[$num]);
            }
        }
        while ($this->status === self::CONTINUE && $inputYesOrNo !== 'Y' && $inputYesOrNo !== 'N') {
            echo 'ブラックジャックゲームを続けますか？（Y/N）' . PHP_EOL;
            $inputYesOrNo = trim(fgets(STDIN));

            if ($inputYesOrNo === 'Y') {
                $this->status = self::CONTINUE;
            } elseif ($inputYesOrNo === 'N') {
                $this->status = self::STOP;
            } else {
                echo 'Y/N で入力してください。' . PHP_EOL;
            }
        }
    }

    /**
     * ゲームを終了する
     *
     * @return void
     */
    private function end(): void
    {
        echo Message::getEndMessage();
    }
}
