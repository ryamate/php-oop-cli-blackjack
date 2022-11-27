<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');
require_once('PlayerAction.php');

use Blackjack\Deck;
use Blackjack\Player;
use Blackjack\PlayerAction;

class DealerPlayer extends Player implements PlayerAction
{
    /**
     * 選択したアクション（ヒットかスタンド）により進行する
     *
     * @param Game $game
     * @return void
     */
    public function action(Game $game): void
    {
        while ($this->getStatus() === self::HIT) {
            echo Message::getScoreTotalMessage($this);
            sleep(Message::SECONDS_TO_DISPLAY);
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $game->getDealer()->dealOneCard($game->getDeck(), $this);
                $game->getDealer()->getJudge()->checkBurst($this);

                echo Message::getCardDrawnMessage($this);
                sleep(Message::SECONDS_TO_DISPLAY);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus(self::STAND);

                echo Message::getStopDrawingCardsMessage();
                sleep(Message::SECONDS_TO_DISPLAY);
            }
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
