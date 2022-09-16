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
require_once('SpecialRule.php');
require_once('Message.php');

use Blackjack\Deck;
use Blackjack\Player;
use Blackjack\DealerPlayer;
use Blackjack\ManualPlayer;
use Blackjack\AutoPlayer;
use Blackjack\Dealer;
use Blackjack\Judge;
use Blackjack\ChipCalculator;
use Blackjack\SpecialRule;
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
            new ChipCalculator(),
            new SpecialRule()
        );
        $this->players[] =  new ManualPlayer('あなた');
    }

    /**
     * デッキ を返す
     *
     * @return Deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }

    /**
     * ディーラー を返す
     *
     * @return Dealer
     */
    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    /**
     * プレイヤーの配列 を返す
     *
     * @return  array<int,ManualPlayer|AutoPlayer> $players プレイヤー
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * プレイヤーの配列 にスプリットを宣言したプレイヤーの 2 手目を追加する
     * - 特殊ルール split で利用
     *
     * @param  ManualPlayer|AutoPlayer $playerAsSecondHand スプリットを宣言したプレイヤーの 2 手目
     */
    public function addPlayerAsSecondHand(ManualPlayer|AutoPlayer $playerAsSecondHand): void
    {
        $count = 0;
        foreach ($this->players as $player) {
            ++$count;
            if ($player->getName() === $playerAsSecondHand->getName()) {
                array_splice($this->players, $count, 0, [$playerAsSecondHand]);
                break;
            }
        }
    }

    /**
     * プレイヤーの配列 にプレイヤーを追加する
     * 特殊ルール Split で利用するため
     *
     * @param  ManualPlayer|AutoPlayer $players プレイヤー
     */
    public function removeSplitPlayer(ManualPlayer|AutoPlayer $splitPlayer): void
    {
        $count = 0;
        foreach ($this->players as $player) {
            if ($player->getName() === $splitPlayer->getName() && $player->getSplitStatus() === Player::SPLIT_SECOND) {
                array_splice($this->players, $count, 1);
                break;
            }
            $count++;
        }
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
        echo 'ブラックジャックの設定をします。' . PHP_EOL;
        $inputNumOfPlayer = 0;
        while ($inputNumOfPlayer !== 1 && $inputNumOfPlayer !== 2 && $inputNumOfPlayer !== 3) {
            // プレイヤー人数について、 1, 2, 3 での入力を求める
            echo 'プレイヤーの人数を入力してください。（1〜3）' . PHP_EOL .
                '🙋‍ ';
            $inputNumOfPlayer = (int)trim(fgets(STDIN));
            if ($inputNumOfPlayer === 1 || $inputNumOfPlayer === 2 || $inputNumOfPlayer === 3) {
                for ($i = 1; $i < $inputNumOfPlayer; $i++) {
                    $nPCName = 'NPC' . (string)$i;
                    $this->players[] = new AutoPlayer($nPCName);
                }
                echo 'プレイヤー' . $inputNumOfPlayer . '名でゲームを開始します。' . PHP_EOL . PHP_EOL;
                sleep(1);
            } else {
                echo '1〜3(半角数字)で入力してください。' . PHP_EOL;
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
        foreach ($this->players as $player) {
            $player->bet();
        }
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    private function start(): void
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        sleep(1);
        $this->deck->initDeck();
        foreach ($this->players as $player) {
            $this->dealer->dealOutFirstHand($this->deck, $player);
        }
        $this->dealer->dealOutFirstHand($this->deck, $this->dealer->getDealerPlayer());

        foreach ($this->players as $player) {
            foreach ($player->getHand() as $card) {
                echo $player->getName() . 'の引いたカードは' . $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
                sleep(1);
            }
            echo PHP_EOL;
        }

        $dealersFirstCard = $this->dealer->getDealerPlayer()->getHand()[0];
        echo 'ディーラーの引いたカードは' . $dealersFirstCard['suit'] . 'の' . $dealersFirstCard['num'] . 'です。' . PHP_EOL;
        sleep(1);
        echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL . PHP_EOL;
        sleep(1);
    }

    /**
     * 各プレイヤーのアクションについて進行する
     *
     * @return void
     */
    private function action(): void
    {
        foreach ($this->players as $player) {
            $player->action($this);
            if ($player->getStatus() === Player::BURST) {
                echo Message::getScoreTotalResultMessage($player);
                echo '合計値が21を超えたので、バーストしました。' . $player->getName() . 'は負けです…' . PHP_EOL
                    . PHP_EOL;
                sleep(1);
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
        $this->dealer->getJudge()->judgeWinOrLose($this);
    }

    /**
     * 勝敗、特殊ルールに応じたプレイヤーのチップ残高を算出し、プレイヤーとディーラーのゲーム中のステータスをリセットする
     *
     * @return void
     */
    private function calcChips(): void
    {
        foreach ($this->players as $player) {
            $this->dealer->getChipCalculator()->calcChips($this, $player);
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
                sleep(1);
                $this->status = self::STOP;
            } elseif ($player->getChips() === 0) {
                echo $player->getName() . 'は、チップの残高がなくなりました。' . PHP_EOL;
                sleep(1);
                echo $player->getName() . 'は、退出しました。' . PHP_EOL;
                sleep(1);
                unset($this->players[$num]);
            }
        }
        while ($this->status === self::CONTINUE && $inputYesOrNo !== 'Y' && $inputYesOrNo !== 'N') {
            echo 'ブラックジャックゲームを続けますか？（Y/N）' . PHP_EOL .
                '👉 ';
            $inputYesOrNo = trim(fgets(STDIN));

            if ($inputYesOrNo === 'Y') {
                $this->status = self::CONTINUE;
            } elseif ($inputYesOrNo === 'N') {
                $this->status = self::STOP;
            } else {
                echo 'Y/N で入力してください。' . PHP_EOL . PHP_EOL;
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
        echo 'ブラックジャックを終了します。' . PHP_EOL . PHP_EOL;
    }
}
