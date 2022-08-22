<?php

namespace Blackjack;

class ManualPlayer extends Player
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
