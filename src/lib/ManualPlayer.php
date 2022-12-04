<?php

namespace Blackjack;

require_once('Player.php');
require_once('PlayerAction.php');
require_once('PlayerBet.php');
require_once('Validator.php');

use Blackjack\Player;
use Blackjack\PlayerAction;
use Blackjack\PlayerBet;
use Blackjack\Validator;

class ManualPlayer extends Player implements PlayerAction, PlayerBet
{
    use Validator;

    /**
     * プレイヤーのタイプ別にチップをベットする行動を選択する
     *
     * @return void
     */
    public function bet(): void
    {
        while ($this->getBets() === 0) {
            echo Message::getPlaceYourBetsMessage($this);
            $input = $this->selectBets();
            $error = $this->validateInputBets($input, $this);
            if ($error === '') {
                $this->changeBets((int)$input);
                echo Message::getBetsResultMessage($this->getBets());
                sleep(Message::SECONDS_TO_DISPLAY);
            } else {
                echo $error;
            }
        }
    }

    /**
     * ベットする額を選択する
     *
     * @return string
     */
    public function selectBets(): string
    {
        $input = trim(fgets(STDIN));
        return $input;
    }

    /**
     * 選択したアクション（ヒットかスタンド）により進行する
     *
     * @param Game $game
     * @return void
     */
    public function action(Game $game): void
    {
        while ($this->hasHitStatus()) {
            echo Message::getScoreTotalMessage($this);
            sleep(Message::SECONDS_TO_DISPLAY);

            echo Message::getProgressQuestionMessage();
            $inputYesOrNo = $this->selectHitOrStand();

            if ($game->getDealer()->isFirstHand($this->getHand())) {
                $message = $game->getDealer()->getSpecialRule()->applySpecialRule($inputYesOrNo, $game, $this);
            }

            if ($inputYesOrNo === 'Y') {
                $game->getDealer()->dealOneCard($game->getDeck(), $this);
                $game->getDealer()->getJudge()->checkBurst($this);
                $message = Message::getCardDrawnMessage($this);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus(self::STAND);
                $message = Message::getStopDrawingCardsMessage();
            } elseif (!$game->getDealer()->getSpecialRule()->isSpecialRule($inputYesOrNo)) {
                $message = Message::getInputErrorMessage();
            }
            echo $message;
            sleep(Message::SECONDS_TO_DISPLAY);
        }
    }

    /**
     * ヒットかスタンドを Y/N で選択する（標準入力を求める）
     *
     * @return string
     */
    public function selectHitOrStand(): string
    {
        return trim(fgets(STDIN));
    }
}
