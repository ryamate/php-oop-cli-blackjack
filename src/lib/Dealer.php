<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');

use Blackjack\Deck;
use Blackjack\Player;

class Dealer extends Player
{
    private const NUM_OF_FIRST_HAND = 2;

    /**
     * 初めの手札2枚を配る
     *
     * @param Deck $deck
     * @param Player $player
     */
    public function dealOutFirstHand(Deck $deck, Player $player): void
    {
        for ($i = 1; $i <= self::NUM_OF_FIRST_HAND; $i++) {
            $this->dealOneCard($deck, $player);
        }
    }

    /**
     * カードを1枚配る（デッキからカードを1枚引いて、プレイヤーの手札に加える）
     *
     * @param Deck $deck
     * @param Player $player
     */
    public function dealOneCard(Deck $deck, Player $player): void
    {
        $cardDrawn = array_slice($deck->getDeck(), 0, 1);
        $deck->takeACard();
        $player->addACardToHand($cardDrawn);
        $player->calcScoreTotal();
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
     * @param Deck $deck
     * @param array<int,Player> $players
     * @return void
     */
    public function judgeWinOrLose(Deck $deck, array $players): void
    {
        echo Message::getStandMessage($this);

        if ($this->hasStand($players)) {
            $this->action($deck, $this);

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
     * @param Deck $deck
     * @param Dealer $dealer
     * @return void
     */
    public function action(Deck $deck, Dealer $dealer): void
    {
        $message = '';
        while ($this->getStatus() === 'hit') {
            echo Message::getProgressMessage($dealer);
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($deck, $dealer);
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
