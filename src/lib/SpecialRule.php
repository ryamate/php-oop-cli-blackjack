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
    public function applySpecialRule(string $inputYesOrNo, Game $game, Player $player): string
    {
        $message = '';
        if ($inputYesOrNo === 'DD') {
            $message = $this->doubleDown(
                $game->getDeck(),
                $game->getDealer(),
                $player
            );
        } elseif ($inputYesOrNo === 'SP') {
            $message = $this->split($game, $player);
        } elseif ($inputYesOrNo === 'SR') {
            $message = $this->surrender($player);
        } else {
            $message .= 'Y/N（DD/SP/SR は、最初に手札が配られたときのみ）を入力してください。' . PHP_EOL;
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
            $message .= Message::getScoreTotalMessage($player);
        }
        return $message;
    }

    /**
     * スプリット(Split)
     * 最初に配られたカードが同数の場合、カードを2つに分けてそれぞれ別のハンドとしてゲームをプレイする。
     * 最初に掛けた金額と同額の掛け金が必要となる。
     *
     * @param Game $game
     * @param ManualPlayer|AutoPlayer $player
     * @return string
     */
    private function split(Game $game, ManualPlayer|AutoPlayer $player): string
    {
        $message = '';
        if ($player->getHand()[0]['score'] !== $player->getHand()[1]['score']) {
            $message .= 'スプリットを宣言するには、最初に配られたカードが同数である必要があります。' . PHP_EOL;
        }
        if ($player->getChips() < ($player->getBets() * 2)) {
            $message .= 'スプリットを宣言するための、チップ残高が足りません。' . PHP_EOL;
        }
        if ($message === '') {
            // $player を複製する（$player → $playerAsSecondHand
            $playerAsSecondHand = clone $player;

            // $player → 手札の1枚目を持つ, $playerSplit → 手札の2枚目を持つ
            $player->changeHand(array_slice($player->getHand(), 0, 1));
            $playerAsSecondHand->changeHand(array_slice($playerAsSecondHand->getHand(), 1));

            $game->addPlayerAsSecondHand($playerAsSecondHand);

            $player->changeSplitStatus(Player::SPLIT_FIRST);
            $playerAsSecondHand->changeSplitStatus(Player::SPLIT_SECOND);

            foreach ([$player, $playerAsSecondHand] as $playerAfterSplit) {
                $game->getDealer()->dealOneCard($game->getDeck(), $playerAfterSplit);
                echo $playerAfterSplit->getSplitStatus() . ' 手目: ' . Message::getCardDrawnMessage($playerAfterSplit);
                echo $playerAfterSplit->getSplitStatus() . ' 手目: ' . Message::getScoreTotalMessage($playerAfterSplit);
            }

            foreach ([$player, $playerAsSecondHand] as $playerAfterSplit) {
                // 一度だけ、HITかSTANDかを選択する
                while ($playerAfterSplit->getStatus() === Player::HIT) {
                    echo $playerAfterSplit->getSplitStatus() . ' 手目: ' . 'カードを引きますか？（Y/N）' . PHP_EOL;
                    $inputYesOrNo = $playerAfterSplit->selectHitOrStand();

                    if ($inputYesOrNo === 'Y') {
                        $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' . 'ヒットを宣言しました。（カードを引きます）' . PHP_EOL;
                        $game->getDealer()->dealOneCard($game->getDeck(), $playerAfterSplit);
                        $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' . Message::getCardDrawnMessage($playerAfterSplit);
                        $playerAfterSplit->changeStatus(Player::STAND);
                        $game->getDealer()->getJudge()->checkBurst($playerAfterSplit);
                        $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' . Message::getScoreTotalMessage($playerAfterSplit);
                    } elseif ($inputYesOrNo === 'N') {
                        $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' . 'スタンドを宣言しました。（カードを引きません）' . PHP_EOL;
                        $playerAfterSplit->changeStatus(Player::STAND);
                        $message .= $playerAfterSplit->getSplitStatus() . ' 手目:' . Message::getScoreTotalMessage($playerAfterSplit) . PHP_EOL;
                    } else {
                        $message .= 'Y/Nを入力してください' . PHP_EOL;
                    }
                    echo $message;
                    $message = '';
                }
            }
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
