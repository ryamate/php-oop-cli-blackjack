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
                echo $this->getBets() . 'ドルをベットしました。' . PHP_EOL;
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
     * @param Deck $deck
     * @param Dealer $dealer
     * @return void
     */
    public function action(Deck $deck, Dealer $dealer): void
    {
        $message = '';
        echo Message::getProgressMessage($this);
        echo Message::getProgressQuestionMessage();
        while ($this->getStatus() === self::HIT) {
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($deck, $this);
                $dealer->getJudge()->checkBurst($this);
                $message .= Message::getCardDrawnMessage($this);
                $message .= Message::getProgressMessage($this);
                $message .= Message::getProgressQuestionMessage();
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus(self::STAND);
                $message = PHP_EOL;
            } elseif (count($this->getHand()) === $dealer::NUM_OF_FIRST_HAND) {
                $message = $dealer->getSpecialRule()->applySpecialRule($inputYesOrNo, $deck, $dealer, $this);
            } else {
                $message .= 'Y/N（DD/SP/SR は、最初に手札が配られたときのみ）を入力してください';
            }
            echo $message;
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
