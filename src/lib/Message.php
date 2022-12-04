<?php

namespace Blackjack;

require_once('Player.php');
require_once('DealerPlayer.php');

use Blackjack\Player;
use Blackjack\DealerPlayer;

/**
 * メッセージクラス
 */
class Message
{
    /** 表示するまでの秒数 */
    public const SECONDS_TO_DISPLAY =  1;

    /**
     * ブラックジャックの設定開始のメッセージを返す
     *
     * @return string
     */
    public static function getGameSettingsMessage(): string
    {
        return 'ブラックジャックの設定をします。' . PHP_EOL;
    }

    /**
     * プレイヤー人数について、 1, 2, 3 での入力を求めるメッセージを返す
     *
     * @return string
     */
    public static function getSettingNumOfPlayersMessage(): string
    {
        return 'プレイヤーの人数を入力してください。（1〜3）' . PHP_EOL .
            '🙋‍ ';
    }

    /**
     * 決まったプレイヤー人数でゲームを開始するメッセージを返す
     *
     * @param int $inputNumOfPlayer
     * @return string
     */
    public static function getStartWithFixedNumOfPlayersMessage(int $inputNumOfPlayer): string
    {
        return 'プレイヤー' . $inputNumOfPlayer . '名でゲームを開始します。' . PHP_EOL . PHP_EOL;
    }

    /**
     * ブラックジャックの開始のメッセージを返す
     *
     * @return string
     */
    public static function getGameStartMessage(): string
    {
        return 'ブラックジャックを開始します。' . PHP_EOL;
    }



    /**
     * ベット額の入力を依頼するメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getPlaceYourBetsMessage(Player $player): string
    {
        $message = '';
        $message .= $player->getName() . 'の持っているチップは' . $player->getChips() . 'ドルです。' . PHP_EOL
            . 'ベットする額を入力してください。（1〜1000ドル）' . PHP_EOL .
            '💲 ';
        return $message;
    }

    /**
     * 確定したベット額を知らせるメッセージを返す
     *
     * @param int $bets
     * @return string
     */
    public static function getBetsResultMessage(int $bets): string
    {
        return $bets . 'ドルをベットしました。' . PHP_EOL . PHP_EOL;
    }

    /**
     * プレイヤーが最初に引いたカードについてのメッセージを返す
     *
     * @param  Player $player
     * @param  array<string, int|string> $card
     * @return string $message
     */
    public static function getPlayerFirstHandMessage(Player $player, array $card): string
    {
        $message = '';
        $message .= $player->getName() . 'の引いたカードは' . $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーが最初に引いたカードについてのメッセージを返す
     *
     * @param DealerPlayer $dealerPlayer
     * @return string $message
     */
    public static function getDealerFirstHandMessage(DealerPlayer $dealerPlayer): string
    {
        $message = '';
        $dealersFirstCard = $dealerPlayer->getHand()[0];
        $message .= 'ディーラーの引いたカードは' .
            $dealersFirstCard['suit'] . 'の' . $dealersFirstCard['num'] . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーが 2 枚目に引いたカードについてのメッセージを返す
     *
     * @return string $message
     */
    public static function getDealerSecondHandMessage(): string
    {
        $message = '';
        $message = 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL . PHP_EOL;
        return $message;
    }

    /**
     * プレイヤーのカードの合計値が 21 を超え、プレイヤーの負けであることを伝えるメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getLoseByBurstMessage(Player $player): string
    {
        $message = '';
        $message = self::getScoreTotalResultMessage($player);
        $message .= '合計値が21を超えたので、バーストしました。' . $player->getName() . 'は負けです…' . PHP_EOL
            . PHP_EOL;
        return $message;
    }

    /**
     * 現在の得点 のメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getScoreTotalMessage(Player $player): string
    {
        return $player->getName() . 'の現在の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
    }

    /**
     * カードを引くか、のメッセージを返す
     *
     * @return string
     */
    public static function getProgressQuestionMessage(): string
    {
        return  'カードを引きますか？（Y/N / DD/SP/SR）' . PHP_EOL .
            '※ 特殊ルール（DD: ダブルダウン, SP: スプリット, SR: サレンダー）は、最初に手札が配られたときのみ有効' . PHP_EOL .
            '👉 ';
    }

    /**
     * 配られたカードのメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getCardDrawnMessage(Player $player): string
    {
        $hand = $player->getHand();
        $cardDrawn = end($hand);
        $message = $player->getName() . 'の引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * 選択したアクションがスタンドの時のメッセージを返す
     *
     * @return string
     */
    public static function getStopDrawingCardsMessage(): string
    {
        return 'カードを引きません。' . PHP_EOL . PHP_EOL;
    }

    /**
     * Y/N（DD/SP/SR） 以外の値が入力された時のメッセージを返す
     *
     * @return string
     */
    public static function getInputErrorMessage(): string
    {
        return 'Y/N（DD/SP/SR は、最初に手札が配られたときのみ）を入力してください。' . PHP_EOL . PHP_EOL;
    }

    /**
     * プレイヤーの得点結果メッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getScoreTotalResultMessage(Player $player): string
    {
        $message = $player->getName() . 'の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * これ以上カードを引かないと宣言した後のメッセージを返す
     *
     * @param DealerPlayer $dealerPlayer
     * @return string $message
     */
    public static function getStandMessage(DealerPlayer $dealerPlayer): string
    {
        $dealersSecondCard = $dealerPlayer->getHand()[1];
        $message = 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' . $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @return string $message
     */
    public static function getDealerBurstMessage(): string
    {
        $message = '合計値が21を超えたので、ディーラーはバーストしました。' . PHP_EOL . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getWinByBurstMessage(Player $player): string
    {
        $playerName = $player->getName();
        switch ($player->getSplitStatus()) {
            case $player::SPLIT_FIRST:
                $playerName .= '(1手目)';
                break;
            case $player::SPLIT_SECOND:
                $playerName .= '(2手目)';
                break;
        }
        return $playerName . 'の勝ちです！🎉' . PHP_EOL;
    }

    /**
     * プレイヤーの勝敗結果メッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    public static function getResultMessage(Player $player): string
    {
        $message = '';
        $playerName = $player->getName();
        switch ($player->getSplitStatus()) {
            case $player::SPLIT_FIRST:
                $playerName .= '(1手目)';
                break;
            case $player::SPLIT_SECOND:
                $playerName .= '(2手目)';
                break;
        }

        switch ($player->getStatus()) {
            case $player::WIN:
                $message = $playerName . 'の勝ちです！🎉' . PHP_EOL;
                break;
            case $player::LOSE:
                $message = $playerName . 'の負けです…' . PHP_EOL;
                break;
            case $player::DRAW:
                $message = $playerName . 'は引き分けです。' . PHP_EOL;
                break;
        }
        return $message;
    }

    /**
     * 勝った人のチップについてのメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getWinAndGetChipsMessage(Player $player): string
    {
        $message = '';
        switch ($player->getSplitStatus()) {
            case 0:
                $message .= $player->getName() . 'は';
                break;
            case 1:
                $message .= '1 手目: ';
                break;
            case 2:
                $message .= '2 手目: ';
                break;
        }
        $message .= 'は勝ったため、チップ ' . $player->getBets() . ' ドルと同額の配当を得られます。' . PHP_EOL;

        return $message;
    }

    /**
     * 負けた人のチップについてのメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getLoseAndLoseChipsMessage(Player $player): string
    {
        $message = '';
        switch ($player->getSplitStatus()) {
            case 0:
                $message .= $player->getName() . 'は';
                break;
            case 1:
                $message .= '1 手目: ';
                break;
            case 2:
                $message .= '2 手目: ';
                break;
        }
        $message .= '負けたため、チップ ' . $player->getBets() . ' ドルは没収されます。' . PHP_EOL;

        return $message;
    }

    /**
     * 引き分けた人のチップについてのメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getDrawAndKeepChipsMessage(Player $player): string
    {
        $message = '';
        switch ($player->getSplitStatus()) {
            case 0:
                $message .= $player->getName() . 'は';
                break;
            case 1:
                $message .= '1 手目: ';
                break;
            case 2:
                $message .= '2 手目: ';
                break;
        }
        $message .= '引き分けたため、チップ ' . $player->getBets() . ' ドルはそのままです。' . PHP_EOL;

        return $message;
    }

    /**
     * チップ残高についてのメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getChipBalanceMessage(Player $player): string
    {
        return $player->getName() . 'のチップ残高は ' . $player->getChips() . ' ドルです。' . PHP_EOL;
    }

    /**
     * スプリット宣言についてのメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getSplitDeclarationMessage(Player $player): string
    {
        return $player->getName() . 'は、スプリットを宣言しています。' . PHP_EOL;
    }

    /**
     * チップの残高がなくなったメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getNoChipsMessage(Player $player): string
    {
        return $player->getName() . 'は、チップの残高がなくなりました。' . PHP_EOL;
    }

    /**
     * 退出のメッセージを返す
     *
     * @param Player $player
     * @return string
     */
    public static function getLeaveMessage(Player $player): string
    {
        return $player->getName() . 'は、退出しました。' . PHP_EOL;
    }

    /**
     * ブラックジャックゲームを続けるかのメッセージを返す
     *
     * @return string
     */
    public static function getContinueGameMessage(): string
    {
        return 'ブラックジャックゲームを続けますか？（Y/N）' . PHP_EOL .
            '👉 ';
    }

    /**
     * ゲーム終了のメッセージを返す
     *
     * @return string
     */
    public static function getGameEndMessage(): string
    {
        return 'ブラックジャックを終了します。' . PHP_EOL . PHP_EOL;
    }
}
