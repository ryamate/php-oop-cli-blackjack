<?php

namespace Blackjack;

require_once('Player.php');

use Blackjack\Player;

class Dealer extends Player
{
    private const NUM_OF_FIRST_HAND = 2;

    /**
     * コンストラクタ
     *
     * @param string $name プレイヤー名
     * @param array<int,array<string,int|string>> $hand 手札
     * @param int $scoreTotal プレイヤーの現在の得点
     * @param int $countAce プレイヤーの引いた A の枚数
     * @param string $status プレイヤーの状態
     * @param Deck $deck
     */
    public function __construct(
        private string $name,
        private array $hand = [],
        private int $scoreTotal = 0,
        private int $countAce = 0,
        private string $status = 'hit',
        private ?Deck $deck = null,
    ) {
        parent::__construct($name, $hand, $scoreTotal, $countAce, $status);
        $this->deck = $deck ?? new Deck();
        $this->deck->initDeck();
    }

    /**
     * deck プロパティを返す
     *
     * @return ?Deck $this->deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }

    /**
     * 初めの手札2枚を配る
     *
     * @param Player $player
     * @return Player $player
     */
    public function dealOutFirstHand(Player $player): Player
    {
        for ($i = 1; $i <= self::NUM_OF_FIRST_HAND; $i++) {
            $player = $this->dealOneCard($player);
        }
        return $player;
    }

    /**
     * カードを1枚配る（デッキからカードを1枚引いて、プレイヤーの手札に加える）
     *
     * @param Player $player
     * @return Player $player
     */
    public function dealOneCard(Player $player): Player
    {

        $cardDrawn = array_slice($this->deck->getDeck(), 0, 1);
        $this->deck->takeACard();
        $player->addACardToHand($cardDrawn);
        $player->calcScoreTotal();
        return $player;
    }

    /**
     * カードの合計値が 21 を超えているかを判定する
     *
     * @param Player $player
     * @return bool 21 を超えたら true
     */
    public function checkBurst(Player $player): bool
    {
        if ($player->getScoreTotal() > 21) {
            $player->changeStatus('burst');
            return true;
        }
        return false;
    }

    /**
     * 勝敗を判定する
     *
     * @param Player $player
     * @param array<int,NonPlayerCharacter> $nPCs
     * @return void
     */
    public function judgeWinOrLose(Player $player, array $nPCs)
    {
        if ($this->isStandPlayer($player, $nPCs)) {
            echo $this->getStandMessage();
            $this->action($this);

            $messages = [];
            $messages[] = $this->getScoreTotalResultMessage($this);

            $numOfNPC = count($nPCs);
            if ($this->getStatus() === 'burst') {
                $messages[] = $this->getDealerBurstMessage();

                if ($player->getStatus() === 'stand') {
                    $player->changeStatus('win');
                    $messages[] = $this->getWinByBurstMessage($player);
                }
                if ($numOfNPC > 0) {
                    for ($i = 0; $i < $numOfNPC; $i++) {
                        if ($nPCs[$i]->getStatus() === 'stand') {
                            $nPCs[$i]->changeStatus('win');
                            $messages[] = $this->getWinByBurstMessage($nPCs[$i]);
                        }
                    }
                }
            } else {
                if ($player->getStatus() === 'stand') {
                    $this->compareScoreTotal($player);
                    $messages[] = $this->getResultMessage($player);
                }
                if ($numOfNPC > 0) {
                    for ($i = 0; $i < $numOfNPC; $i++) {
                        if ($nPCs[$i]->getStatus() === 'stand') {
                            $this->compareScoreTotal($nPCs[$i]);
                            $messages[] = $this->getResultMessage($nPCs[$i]);
                        }
                    }
                }
            }
            foreach ($messages as $message) {
                echo $message;
            }
            unset($message);
        }
    }

    /**
     * Undocumented function
     *
     * @param Player $player
     * @param array $nPCs
     * @return bool
     */
    private function isStandPlayer(Player $player, array $nPCs): bool
    {
        if ($player->getStatus() === 'stand') {
            return true;
        }
        $numOfNPC = count($nPCs);
        if ($numOfNPC > 0) {
            for ($i = 0; $i < $numOfNPC; $i++) {
                if ($nPCs[$i]->getStatus() === 'stand') {
                    return true;
                }
            }
        }
        return false;
    }

    private function compareScoreTotal(Player $player)
    {
        $playerScoreTotal = $player->getScoreTotal();
        $dealerScoreTotal = $this->getScoreTotal();
        if ($playerScoreTotal > $dealerScoreTotal) {
            $player->changeStatus('win');
        } elseif ($playerScoreTotal < $dealerScoreTotal) {
            $player->changeStatus('lose');
        } elseif ($playerScoreTotal === $dealerScoreTotal) {
            $player->changeStatus('draw');
        }
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
                $dealer->dealOneCard($dealer);
                $dealer->checkBurst($dealer);
                $message = $this->getCardDrawnMessage();
            } elseif ($inputYesOrNo === 'N') {
                $this->changeStatus('stand');
                $message = PHP_EOL . PHP_EOL;
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
        } else {
            $inputYesOrNo = 'N';
        }
        return $inputYesOrNo;
    }

    /**
     * 引いたカード、現在の得点、カードを引くか、のメッセージを返す
     *
     * @return string $message
     */
    protected function getProgressMessage(): string
    {
        $message = $this->getName() . 'の現在の得点は' . $this->getScoreTotal() . 'です。' . PHP_EOL;
        return $message;
    }

    /**
     * これ以上カードを引かないと宣言した後のメッセージを返す
     *
     * @return string $message
     */
    private function getStandMessage(): string
    {
        $dealersHand = $this->getHand();
        $dealersSecondCard = end($dealersHand);
        $message = 'ディーラーの引いた2枚目のカードは' .
            $dealersSecondCard['suit'] . 'の' .
            $dealersSecondCard['num'] . 'でした。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @return string $message
     */
    private function getDealerBurstMessage(): string
    {
        $message = '合計値が21を超えたので、ディーラーはバーストしました。' . PHP_EOL;
        return $message;
    }

    /**
     * ディーラーのカードの合計値が 21 を超え、プレイヤーの勝ちであることを伝えるメッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    private function getWinByBurstMessage(Player $player): string
    {
        $message = $player->getName() . 'の勝ちです！' . PHP_EOL;
        return $message;
    }

    /**
     * プレイヤーの勝敗結果メッセージを返す
     *
     * @param Player $player
     * @return string $message
     */
    private function getResultMessage(Player $player): string
    {
        $playerName = $player->getName();
        if ($player->getStatus() === 'win') {
            $message = $playerName . 'の勝ちです！' . PHP_EOL;
        } elseif ($player->getStatus() === 'lose') {
            $message = $playerName . 'の負けです…' . PHP_EOL;
        } elseif ($player->getStatus() === 'draw') {
            $message = $playerName . 'は引き分けです。' . PHP_EOL;
        }
        return $message;
    }
}
