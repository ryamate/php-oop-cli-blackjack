<?php

namespace Blackjack;

require_once('Player.php');
require_once('Deck.php');

use Blackjack\Player;
use Blackjack\Deck;

class Dealer extends Player
{
    private const NUM_OF_FIRST_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param string $name プレイヤー名
     * @param array<int,array<string,int|string>> $hand 手札
     * @param int $scoreTotal プレイヤーの現在の得点
     * @param int $countAce プレイヤーの引いた A の枚数
     * @param string $status プレイヤーの状態
     * @param Deck $deck
     */
    public function __construct(
        private string $name,
        private array $hand = [],
        private int $scoreTotal = 0,
        private int $countAce = 0,
        private string $status = 'hit',
        private ?Deck $deck = null,
    ) {
        parent::__construct($name, $hand, $scoreTotal, $countAce, $status);
        $this->deck = $deck ?? new Deck();
        $this->deck->initDeck();
    }

    /**
     * 初めの手札2枚を配る
     *
     * @param Player $player
     * @return Player $player
     */
    public function dealOutFirstHand(Player $player): Player
    {
        for ($i = 1; $i <= self::NUM_OF_FIRST_HAND; $i++) {
            $player = $this->dealOneCard($player);
        }
        return $player;
    }

    /**
     * カードを1枚配る（デッキからカードを1枚引いて、プレイヤーの手札に加える）
     *
     * @param Player $player
     * @return Player $player
     */
    public function dealOneCard(Player $player): Player
    {
        $cardDrawn = array_slice($this->deck->getDeck(), 0, 1);
        $this->deck->takeACard();
        $player->addACardToHand($cardDrawn);
        $player->calcScoreTotal();
        return $player;
    }

    /**
     * カードの合計値が 21 を超えているかを判定する
     *
     * @param Player $player
     * @return bool 21 を超えたら true
     */
    public function checkBurst(Player $player): bool
    {
        if ($player->getScoreTotal() > 21) {
            $player->changeStatus('burst');
            return true;
        }
        return false;
    }

    /**
     * 勝敗を判定する
     *
     * @param array<int,Player> $players
     * @return void
     */
    public function judgeWinOrLose(array $players): void
    {
        echo Message::getStandMessage($this);

        if ($this->hasStand($players)) {
            $this->action($this);

            $messages = [];
            $messages[] = Message::getScoreTotalResultMessage($this);

            if ($this->getStatus() === 'burst') {
                $messages[] = Message::getDealerBurstMessage();
                foreach ($players as $player) {
                    if ($player->getStatus() === 'stand') {
                        $player->changeStatus('win');
                        $messages[] = Message::getWinByBurstMessage($player);
                    }
                }
            } else {
                foreach ($players as $player) {
                    if ($player->getStatus() === 'stand') {
                        $result = $this->compareScoreTotal($player);
                        $player->changeStatus($result);
                        $messages[] =  Message::getResultMessage($player);
                    }
                }
            }
            foreach ($messages as $message) {
                echo $message;
            }
            unset($message);
        }
    }

    /**
     * スタンドのプレイヤーがいるかについて、 bool を返す
     *
     * @param array<int,Player> $players
     * @return bool
     */
    private function hasStand(array $players): bool
    {
        foreach ($players as $player) {
            if ($player->getStatus() === 'stand') {
                return true;
            }
        }
        return false;
    }

    /**
     * プレイヤーとディーラーの得点を比較して、勝敗を返す
     *
     * @param Player $player
     * @return string $result
     */
    private function compareScoreTotal(Player $player): string
    {
        $result = '';
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $this->getScoreTotal();
        if ($playerScoreTotal > $dealerScoreTotal) {
            $result = 'win';
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $result = 'lose';
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $result = 'draw';
        }
        return $result;
    }

    /**
     * 選択したアクション（ヒットかスタンド）により進行する
     *
     * @param Dealer $dealer
     * @return void
     */
    public function action(Dealer $dealer): void
    {
        $message = '';
        while ($this->getStatus() === 'hit') {
            echo Message::getProgressMessage($dealer);
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($dealer);
                $dealer->checkBurst($dealer);
                $message = Message::getCardDrawnMessage($dealer);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus('stand');
                $message = PHP_EOL . PHP_EOL;
            }
            echo $message;
        }
    }

    /**
     * ヒットかスタンドを Y/N で選択する（カードの合計値が17以上になるまで引き続ける）
     *
     * @return string $inputYesOrNo
     */
    public function selectHitOrStand(): string
    {
        if ($this->getScoreTotal() < 17) {
            $inputYesOrNo = 'Y';
        } else {
            $inputYesOrNo = 'N';
        }
        return $inputYesOrNo;
    }
}
