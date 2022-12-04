<?php

namespace Blackjack;

require_once('Game.php');
require_once('Player.php');
require_once('PlayerAction.php');
require_once('PlayerBet.php');
require_once('Validator.php');

use Blackjack\Game;
use Blackjack\Player;
use Blackjack\PlayerAction;
use Blackjack\PlayerBet;
use Blackjack\Validator;

/**
 * ノンプレイヤーキャラクタークラス
 */
class AutoPlayer extends Player implements PlayerAction, PlayerBet
{
    use Validator;

    /**
     * プレイヤーのタイプ別にチップをベットする行動を選択する
     *
     * @return void
     */
    public function bet(): void
    {
        echo Message::getPlaceYourBetsMessage($this);
        $input = $this->selectBets();
        $this->changeBets((int)$input);
        echo Message::getBetsResultMessage($this->getBets());
        sleep(Message::SECONDS_TO_DISPLAY);
    }

    /**
     * ベットする額を選択する
     *
     * @return string
     */
    public function selectBets(): string
    {
        $maxBet = $this->getChips() > Game::MAX_BET ? Game::MAX_BET : $this->getChips();
        $input = rand(1, $maxBet);
        echo $input . PHP_EOL;
        return (string)$input;
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

            $message = '';
            if ($inputYesOrNo === 'Y') {
                $game->getDealer()->dealOneCard($game->getDeck(), $this);
                $game->getDealer()->getJudge()->checkBurst($this);
                $message = Message::getCardDrawnMessage($this);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus(self::STAND);
                $message = Message::getStopDrawingCardsMessage();
            }
            echo $message;
            sleep(Message::SECONDS_TO_DISPLAY);
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
            echo 'Y' . PHP_EOL;
        } else {
            $inputYesOrNo = 'N';
            echo 'N' . PHP_EOL;
        }
        return $inputYesOrNo;
    }
}
