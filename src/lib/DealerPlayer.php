<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');

use Blackjack\Deck;
use Blackjack\Player;

class DealerPlayer extends Player
{
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
            echo Message::getProgressMessage($this);
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($deck, $this);
                $dealer->checkBurst($this);
                $message = Message::getCardDrawnMessage($this);
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
