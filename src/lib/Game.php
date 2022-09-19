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
require_once('Validator.php');

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
use Blackjack\Validator;

/**
 * ゲームクラス
 */
class Game
{
    use Validator;

    public const CONTINUE = 'continue';
    public const STOP = 'stop';
    public const GAME_STATUS = [
        self::CONTINUE => 1,
        self::STOP => 0,
    ];
    public const MAX_NUM_OF_PLAYERS = 3;
    public const MAX_BET = 1000;

    /**
     * コンストラクタ
     *
     * @param Deck $deck デッキ
     * @param Dealer $dealer ディーラー
     * @param array<int,ManualPlayer|AutoPlayer> $players プレイヤー
     * @param int $status ゲームを続けるか、やめるかの状態
     */
    public function __construct(
        private ?Deck $deck = null,
        private ?Dealer $dealer = null,
        private array $players = [],
        private int $status = self::GAME_STATUS[self::CONTINUE],
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
     * ブラックジャックをプレイする
     *
     * @return void
     */
    public function play(): void
    {
        $this->set();
        while ($this->status === self::GAME_STATUS[self::CONTINUE]) {
            $this->placeYourBets();
            $this->startGame();
            $this->action();
            $this->resultGame();
            $this->resultCalcChips();
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
        $inputNumOfPlayer = '';
        while ($inputNumOfPlayer === '') {
            // プレイヤー人数について、 1, 2, 3 での入力を求める
            echo 'プレイヤーの人数を入力してください。（1〜3）' . PHP_EOL .
                '🙋‍ ';
            $inputNumOfPlayer = trim(fgets(STDIN));
            $error = $this->validateInputNumOfPlayer($inputNumOfPlayer, self::MAX_NUM_OF_PLAYERS);
            if ($error === '') {
                for ($i = 1; $i < $inputNumOfPlayer; $i++) {
                    $nPCName = 'NPC' . (string)$i;
                    $this->players[] = new AutoPlayer($nPCName);
                }
                echo 'プレイヤー' . $inputNumOfPlayer . '名でゲームを開始します。' . PHP_EOL . PHP_EOL;
                sleep(1);
            } else {
                echo $error . PHP_EOL;
                $inputNumOfPlayer = '';
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
    private function startGame(): void
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
    private function resultGame(): void
    {
        $this->dealer->getJudge()->judgeWinOrLose($this);
    }

    /**
     * 勝敗、特殊ルールに応じたプレイヤーのチップ残高を算出し、結果を表示する。
     * - プレイヤーとディーラーのゲーム中のステータスをリセットする。
     *
     * @return void
     */
    private function resultCalcChips(): void
    {
        foreach ($this->players as $player) {
            $this->dealer->getChipCalculator()->calcChips($this, $player);
        }
        $this->dealer->getDealerPlayer()->reset();

        foreach ($this->players as $num => $player) {
            if ($player->getChips() === 0 && $player->getName() === 'あなた') {
                echo 'あなたは、チップの残高がなくなりました。' . PHP_EOL;
                sleep(1);
                $this->status = self::GAME_STATUS[self::STOP];
            } elseif ($player->getChips() === 0) {
                echo $player->getName() . 'は、チップの残高がなくなりました。' . PHP_EOL;
                sleep(1);
                echo $player->getName() . 'は、退出しました。' . PHP_EOL;
                sleep(1);
                unset($this->players[$num]);
            }
        }
    }

    /**
     * ゲームを続けるか、やめるかを選択する
     *
     * @return void
     */
    private function selectContinueOrStop(): void
    {
        $inputYesOrNo = '';
        while ($this->status === self::GAME_STATUS[self::CONTINUE] && $inputYesOrNo === '') {
            echo 'ブラックジャックゲームを続けますか？（Y/N）' . PHP_EOL .
                '👉 ';
            $inputYesOrNo = trim(fgets(STDIN));
            $error = $this->validateInputYesOrNo($inputYesOrNo);
            if ($error === '') {
                if ($inputYesOrNo === 'Y') {
                    $this->status = self::GAME_STATUS[self::CONTINUE];
                } elseif ($inputYesOrNo === 'N') {
                    $this->status = self::GAME_STATUS[self::STOP];
                }
            } else {
                echo $error . PHP_EOL;
                $inputYesOrNo = '';
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
     * スプリットを宣言したプレイヤーの 2 手目をプレイヤーの配列から削除する
     * - 特殊ルール split で利用
     *
     * @param Player $splitPlayer プレイヤー
     */
    public function removeSplitPlayer(Player $splitPlayer): void
    {
        $countPlayers = count($this->players);
        for ($i = 0; $i < $countPlayers; $i++) {
            if (
                $this->players[$i]->getName() === $splitPlayer->getName() &&
                $this->players[$i]->getSplitStatus() === Player::SPLIT_SECOND
            ) {
                array_splice($this->players, $i, 1);
                break;
            }
        }
    }
}
