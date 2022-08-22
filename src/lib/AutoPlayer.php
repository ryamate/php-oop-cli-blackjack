<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

/**
 * ノンプレイヤーキャラクタークラス
 */
class AutoPlayer extends Player
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
            echo Message::getProgressQuestionMessage();
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($deck, $this);
                $dealer->checkBurst($this);
                $message = Message::getCardDrawnMessage($this);
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus('stand');
                $message = PHP_EOL;
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
            echo 'Y' . PHP_EOL;
        } else {
            $inputYesOrNo = 'N';
            echo 'N' . PHP_EOL;
        }
        return $inputYesOrNo;
    }
}
