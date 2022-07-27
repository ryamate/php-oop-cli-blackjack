<?php

namespace Blackjack;

require_once('Deck.php');
require_once('Player.php');
require_once('Dealer.php');

use Blackjack\Deck;
use Blackjack\Player;
use Blackjack\Dealer;

class Game
{
    /**
     * コンストラクタ
     *
     * @param Deck $deck
     * @param Player $player
     * @param Dealer $dealer
     */
    public function __construct(
        private ?Deck $deck = null,
        private ?Player $player = null,
        private ?Dealer $dealer = null,
    ) {
        $this->deck = $deck ?? new Deck();
        $this->player = $player ?? new Player();
        $this->dealer = $dealer ?? new Dealer();

        $this->deck->initDeck();
        $this->player->initHand($this->deck);
        $this->dealer->initHand($this->deck);
    }

    /**
     * deck プロパティを返す
     *
     * @return Deck $this->deck
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * player プロパティを返す
     *
     * @return Player $this->player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * dealer プロパティを返す
     *
     * @return Dealer $this->dealer
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * ブラックジャックを開始する
     *
     * @return void
     */
    public function start()
    {
        $this->displayStartMessage();

        while ($this->player->getStatus() === 'hit') {
            // カードを引くか、 Y/N での入力を求める
            $inputYesOrNo = trim(fgets(STDIN));

            if ($inputYesOrNo === 'Y') {
                $this->player->drawACard($this->deck);
                $this->dealer->checkBurst($this->player);
                if ($this->player->getStatus() === 'hit') {
                    $this->displayProgressMessage();
                }
            } elseif ($inputYesOrNo === 'N') {
                $this->displayStandMessage();
                $this->player->changeStatus('stand');
            } else {
                $this->displayInputErrorMessage();
            }
        }

        // プレイヤーのカードの合計値が 21 を超えていた場合
        if ($this->player->getStatus() === 'burst') {
            $this->displayLoseByBurstMessage();
        } elseif ($this->player->getStatus() === 'stand') {
            // ディーラーは自分のカードの合計値が17以上になるまで引き続ける
            $this->dealer->drawAfterAllPlayerStand($this->deck);
            $this->dealer->checkBurst($this->dealer);
            $this->displayCardsDrawnByDealer();

            // ディーラーのカードの合計値が 21 を超えていた場合
            if ($this->dealer->getStatus() === 'burst') {
                $this->displayWinByBurstMessage();
            } else {
                // 勝敗を判定する
                $this->dealer->judgeWinOrLose($this->player, $this->dealer);
                $this->displayResultMessage();
            }
        }
    }

    /**
     * ブラックジャックの開始時メッセージを表示する
     *
     * @return void
     */
    private function displayStartMessage()
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;

        foreach ($this->player->getHand() as $card) {
            echo 'あなたの引いたカードは' .
                $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        }
        unset($card);

        $dealersFirstCard = $this->dealer->getHand()[0];
        echo 'ディーラーの引いたカードは' .
            $dealersFirstCard['suit'] . 'の' . $dealersFirstCard['num'] . 'です。' . PHP_EOL;
        echo 'ディーラーの引いた2枚目のカードはわかりません。' . PHP_EOL .
            PHP_EOL;

        echo 'あなたの現在の得点は' . $this->player->getScoreTotal() .
            'です。カードを引きますか？（Y/N）' . PHP_EOL;
    }

    /**
     * Y/N 以外の値が入力された時のメッセージを表示する
     *
     * @return void
     */
    private function displayInputErrorMessage()
    {
        echo 'Y/N で入力してください。カードを引きますか？（Y/N）' . PHP_EOL;
    }

    /**
     * プレイヤーのカードの合計値が 21 を超え、プレイヤーの負けであることを伝えるメッセージを表示する
     *
     * @return void
     */
    private function displayLoseByBurstMessage(): void
    {
        $hand = $this->player->getHand();
        $cardDrawn = end($hand);
        echo 'あなたの引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
        echo 'あなたの現在の得点は' . $this->player->getScoreTotal() . 'です。' . PHP_EOL;
        echo '合計値が21を超えたので、あなたの負けです。' . PHP_EOL;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを表示する
     *
     * @return void
     */
    private function displayWinByBurstMessage(): void
    {
        echo 'ディーラーの得点は' . $this->dealer->getScoreTotal() . 'です。' . PHP_EOL;
        echo '合計値が21を超えたので、ディーラーはバーストしました。' . PHP_EOL;
        echo 'あなたの勝ちです！' . PHP_EOL;
    }

    /**
     * 引いたカード、現在の得点、カードを引くか、のメッセージを表示する
     *
     * @return void
     */
    private function displayProgressMessage(): void
    {
        $hand = $this->player->getHand();
        $cardDrawn = end($hand);
        echo 'あなたの引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
        echo 'あなたの現在の得点は' . $this->player->getScoreTotal() .
            'です。カードを引きますか？（Y/N）' . PHP_EOL;
    }

    /**
     * これ以上カードを引かないと宣言した後のメッセージを表示する
     *
     * @return void
     */
    private function displayStandMessage(): void
    {
        $dealersHand = $this->dealer->getHand();
        $dealersSecondCard = end($dealersHand);
        echo 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' .
            $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        echo 'ディーラーの現在の得点は' .
            $this->dealer->getScoreTotal() . 'です。' . PHP_EOL;
    }

    /**
     * ディーラーがカードの合計値が17以上になるまで引いた追加のカードを表示する
     *
     * @return void
     */
    private function displayCardsDrawnByDealer(): void
    {
        $dealersHand = $this->dealer->getHand();
        $cardsDrawnByDealer = array_slice($dealersHand, 2);
        foreach ($cardsDrawnByDealer as $card) {
            echo 'ディーラーの引いたカードは' .
                $card['suit'] . 'の' . $card['num'] . 'です。' . PHP_EOL;
        }
    }

    /**
     * 各プレイヤーの得点、勝敗の結果、終了時メッセージを表示する
     *
     * @return void
     */
    private function displayResultMessage(): void
    {
        echo 'あなたの得点は' . $this->player->getScoreTotal() . 'です。' . PHP_EOL;
        echo 'ディーラーの得点は' . $this->dealer->getScoreTotal() . 'です。' . PHP_EOL;

        if ($this->player->getStatus() === 'win') {
            echo 'あなたの勝ちです！' . PHP_EOL;
        } elseif ($this->player->getStatus() === 'lose') {
            echo 'あなたの負けです…' . PHP_EOL;
        } elseif ($this->player->getStatus() === 'draw') {
            echo '引き分けです。' . PHP_EOL;
        }

        echo 'ブラックジャックを終了します。' . PHP_EOL;
    }
}
