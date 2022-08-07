<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;
use PDepend\Source\AST\State;

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
     * deck プロパティを返す
     *
     * @return ?Deck $this->deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
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
     * @param Player $player
     * @return void
     */
    public function judgeWinOrLose(Player $player)
    {
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $this->getScoreTotal();

        if ($playerScoreTotal > $dealerScoreTotal) {
            $player->changeStatus('win');
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $player->changeStatus('lose');
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $player->changeStatus('draw');
        }
    }

    /**
     * スタンド（すべてのプレーヤーがカードを引くのをやめた）後に、ディーラーは自分のカードの合計値が17以上になるまで引き続ける
     *
     * @return void
     */
    public function drawAfterAllPlayerStand()
    {
        while ($this->getScoreTotal() < 17) {
            $this->dealOneCard($this);
        }
    }
}
