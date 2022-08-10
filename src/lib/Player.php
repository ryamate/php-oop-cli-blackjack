<?php

namespace Blackjack;

require_once('Deck.php');

use Blackjack\Deck;

class Player
{
    /**
     * コンストラクタ
     *
     * @param string $name プレイヤー名
     * @param array<int,array<string,int|string>> $hand 手札
     * @param int $scoreTotal プレイヤーの現在の得点
     * @param int $countAce プレイヤーの引いた A の枚数
     * @param string $status プレイヤーの状態
     */
    public function __construct(
        private string $name,
        private array $hand = [],
        private int $scoreTotal = 0,
        private int $countAce = 0,
        private string $status = 'hit'
    ) {
    }

    /**
     * プレイヤー名を返す
     *
     * @return string $this->name プレイヤー名
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 手札を返す
     *
     * @return array<int,array<string,int|string>> $this->hand 手札
     */
    public function getHand(): array
    {
        return $this->hand;
    }

    /**
     * 得点を返す
     *
     * @return int $this->scoreTotal 得点
     */
    public function getScoreTotal(): int
    {
        return $this->scoreTotal;
    }

    /**
     * 引いた A の枚数を返す
     *
     * @return int $this->countAce 得点
     */
    public function getCountAce(): int
    {
        return $this->countAce;
    }

    /**
     * プレイヤーの状態を返す
     *
     * @return string $this->status プレイヤーの状態
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * 選択したアクション（ヒットかスタンド）により進行する
     *
     * @param Dealer $dealer
     * @return void
     */
    public function action(Dealer $dealer)
    {
        while ($this->getStatus() === 'hit') {
            echo $this->getProgressMessage();
            $inputYesOrNo = $this->selectHitOrStand();

            if ($inputYesOrNo === 'Y') {
                $dealer->dealOneCard($this);
                $dealer->checkBurst($this);
                $message = $this->getCardDrawnMessage();
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus('stand');
                $message = PHP_EOL . PHP_EOL;
            }
            echo $message;
        }
    }

    /**
     * ヒットかスタンドを Y/N で選択する（標準入力を求める）
     *
     * @return string
     */
    protected function selectHitOrStand(): string
    {
        $inputYesOrNo = trim(fgets(STDIN));
        return $inputYesOrNo;
    }

    /**
     * 1枚カードを手札に加える
     *
     * @param array $card
     * @return void
     */
    public function addACardToHand(array $card): void
    {
        $this->hand = array_merge($this->hand, $card);
    }

    /**
     * プレイヤーの現在の得点を計算する
     *
     * @return void
     */
    public function calcScoreTotal(): void
    {
        $this->scoreTotal = 0;
        $this->countAce = 0;
        foreach ($this->hand as $card) {
            if ($card['num'] === 'A') {
                ++$this->countAce;
            }
            $this->scoreTotal += $card['score'];
        }
        unset($card);
        $this->calcAceScore();
    }

    /**
     * A の点数については、デフォルト 11 でカウントされており、
     * 得点が21点を超えている場合は、 1 でカウントする
     *
     * @return void
     */
    private function calcAceScore(): void
    {
        for ($i = 0; $i < $this->countAce; $i++) {
            if ($this->scoreTotal > 21) {
                $this->scoreTotal -= 10;
            }
        }
    }

    /**
     * ステータスを変更する
     *
     * @param string $status
     * @return void
     */
    public function changeStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * 引いたカード、現在の得点、カードを引くか、のメッセージを表示する
     *
     * @return string $message
     */
    protected function getProgressMessage(): string
    {
        $message =  $this->getName() . 'の現在の得点は' . $this->getScoreTotal() .
            'です。カードを引きますか？（Y/N）' . PHP_EOL;
        return $message;
    }

    /**
     * 配られたカードを表示する
     *
     * @return string $message
     */
    protected function getCardDrawnMessage(): string
    {
        $hand = $this->getHand();
        $cardDrawn = end($hand);
        $message = $this->getName() . 'の引いたカードは' .
            $cardDrawn['suit'] . 'の' . $cardDrawn['num'] . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * Y/N 以外の値が入力された時のメッセージを表示する
     *
     * @return string
     */
    protected function getInputErrorMessage(): string
    {
        return 'Y/N で入力してください。' . PHP_EOL;
    }

    /**
     * プレイヤーの得点結果メッセージを表示する
     *
     * @param Player $player
     * @return string $message
     */
    public function getScoreTotalResultMessage(Player $player): string
    {
        $message = $player->getName() . 'の得点は' . $player->getScoreTotal() . 'です。' . PHP_EOL;
        return $message;
    }
}
