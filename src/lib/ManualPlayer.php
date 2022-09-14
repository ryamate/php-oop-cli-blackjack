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
            $error = $this->validateInputBets($input);
            if ($error === '') {
                $this->changeBets($input);
                echo $this->getBets() . 'ドルをベットしました。' . PHP_EOL . PHP_EOL;
                sleep(1);
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
            sleep(1);
            echo Message::getProgressQuestionMessage();
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $game->getDealer()->dealOneCard($game->getDeck(), $this);
                $game->getDealer()->getJudge()->checkBurst($this);

                echo Message::getCardDrawnMessage($this);
                sleep(1);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus(self::STAND);

                echo 'カードを引きません。' . PHP_EOL . PHP_EOL;
                sleep(1);
            } elseif (count($this->getHand()) === $game->getDealer()::NUM_OF_FIRST_HAND) {
                $message = $game->getDealer()->getSpecialRule()->applySpecialRule($inputYesOrNo, $game, $this);

                echo $message;
                sleep(1);
            } else {
                echo 'Y/N（DD/SP/SR は、最初に手札が配られたときのみ）を入力してください。' . PHP_EOL . PHP_EOL;
                sleep(1);
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
