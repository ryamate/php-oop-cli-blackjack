<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class SpecialRule
{
    /**
     * 特殊ルールを適用する
     *
     * @param string $inputYesOrNo
     * @param Deck $deck
     * @param Dealer $dealer
     * @param Player $player
     * @return string $message
     */
    public function applySpecialRule(string $inputYesOrNo, Deck $deck, Dealer $dealer, Player $player): string
    {
        $message = '';
        if ($inputYesOrNo === 'DD') {
            $message = $this->doubleDown($deck, $dealer, $player);
        } elseif ($inputYesOrNo === 'SP') {
            $message = $this->split($deck, $dealer, $player);
        } elseif ($inputYesOrNo === 'SR') {
            $message = $this->surrender($player);
        } else {
            $message .= 'Y/N / DD/SP/SR を入力してください' . PHP_EOL;
            $message .= Message::getProgressQuestionMessage();
        }
        return $message;
    }

    /**
     * ダブルダウン(Double Down)
     * 最初に賭けた賭け金と同額を追加して3枚目のカードを追加する。
     * また、ダブルダウンを宣言するとヒットは1回のみ行う。
     *
     * @param Deck $deck
     * @param Dealer $dealer
     * @param Player $player
     * @return string
     */
    private function doubleDown(Deck $deck, Dealer $dealer, Player $player): string
    {
        $message = '';
        $firstBets = $player->getBets();
        $doubleDownBets = $firstBets * 2;
        $player->changeBets($doubleDownBets);
        $player->changeStatus(Player::STAND);
        $message .= 'ダブルダウンを宣言しました。' . PHP_EOL .
            '最初に賭けたチップ ' . $firstBets . ' ドルと同額を追加して、チップ ' . $doubleDownBets . ' ドルを賭けます。 ' . PHP_EOL .
            'ヒットは 1 回のみ行い 3 枚目のカードを追加します。' . PHP_EOL;

        $dealer->dealOneCard($deck, $player);
        $dealer->getJudge()->checkBurst($player);
        $message .= Message::getCardDrawnMessage($player);
        if ($player->getStatus() === Player::STAND) {
            $message .= Message::getProgressMessage($player);
        }
        return $message;
    }

    /**
     * スプリット(Split)
     * 最初に配られたカードが同数の場合、カードを2つに分けてそれぞれ別のハンドとしてゲームをプレイする。
     * 最初に掛けた金額と同額の掛け金が必要となる。
     *
     * @param Deck $deck
     * @param Dealer $dealer
     * @param Player $player
     * @return string
     */
    private function split(Deck $deck, Dealer $dealer, Player $player): string
    {
        $message = '';
        if ($player->getHand()[0]['score'] !== $player->getHand()[1]['score']) {
            $message .= 'スプリットを宣言するには、最初に配られたカードが同数である必要があります。' . PHP_EOL;
        }
        if ($player->getChips() >= $player->getBets() * 2) {
            $message .= 'スプリットを宣言するための、チップ残高が足りません。' . PHP_EOL;
        }
        if ($message !== '') {
            // TODO: 処理を書く
            // $player を複製する（$player, $playerSplit）
            // $playerSplit を $player->split として持たせたい
            // $player → 手札の1枚目を持つ, $playerSplit → 手札の2枚目を持つ
            // カードを1枚ずつ引く
            // 一度だけ、HITかSTANDを選択する
            // while ($this->getStatus() === self::HIT) {
            //     $inputYesOrNo = $this->selectHitOrStand();

            //     if ($inputYesOrNo === 'Y') {
            //         $dealer->dealOneCard($deck, $this);
            //         $this->changeStatus(self::STAND);
            //         $dealer->getJudge()->checkBurst($this);
            //         $message .= Message::getCardDrawnMessage($this);
            //     } elseif ($inputYesOrNo === 'N') {
            //         $this->changeStatus(self::STAND);
            //         $message = PHP_EOL;
            //     } else {
            //         $message .= 'Y/Nを入力してください';
            //     }
            //     echo $message;
            // }
        }
        return $message;
    }

    /**
     * サレンダー(Surrender)
     * サレンダーを宣言したら、掛け金の半分を戻してもらいゲームを降りる。
     *
     * @param Player $player
     * @return string
     */
    private function surrender(Player $player): string
    {
        $message = '';
        $firstBets = $player->getBets();
        $surrenderBets = $firstBets - bcmul("0.5", $player->getBets(), 0);
        $player->changeBets($surrenderBets);
        $player->changeStatus(Player::LOSE);
        $message .= 'サレンダーを宣言しました。' . PHP_EOL .
            'ベットしたチップ' . $firstBets . 'ドルの半分' . $surrenderBets . 'ドルを戻してゲームを降ります。' . PHP_EOL;
        return $message;
    }
}
