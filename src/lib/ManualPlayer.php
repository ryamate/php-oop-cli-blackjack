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
        while ($this->getStatus() === self::HIT) {
            echo Message::getScoreTotalMessage($this);
            sleep(Message::SECONDS_TO_DISPLAY);

            echo Message::getProgressQuestionMessage();
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
            } elseif (count($this->getHand()) === $game->getDealer()::NUM_OF_FIRST_HAND) {
                $message = $game->getDealer()->getSpecialRule()->applySpecialRule($inputYesOrNo, $game, $this);

                echo $message;
                sleep(Message::SECONDS_TO_DISPLAY);
            } else {
                echo Message::getInputErrorMessage();
                sleep(Message::SECONDS_TO_DISPLAY);
            }
        }
    }

    /**
     * ヒットかスタンドを Y/N で選択する（標準入力を求める）
     *
     * @return string
     */
    public function selectHitOrStand(): string
    {
        $inputYesOrNo = trim(fgets(STDIN));
        return $inputYesOrNo;
    }
}
