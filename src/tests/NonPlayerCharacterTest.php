<?php

namespace Blackjack\Tests;

require_once(__DIR__ . '/../lib/NonPlayerCharacter.php');

use PHPUnit\Framework\TestCase;
use Blackjack\NonPlayerCharacter;

// class NonPlayerCharacterTest extends TestCase
// {
//     public function testSelectHitOrStand()
//     {
//         $NPC1 = new NonPlayerCharacter('ディーラー1', [], 18, 0, 'hit');
//         $this->assertSame('N', $NPC1->selectHitOrStand());

//         $NPC2 = new NonPlayerCharacter('ディーラー2', [], 17, 0, 'hit');
//         $this->assertSame('N', $NPC2->selectHitOrStand());

//         $NPC3 = new NonPlayerCharacter('ディーラー1', [], 16, 0, 'hit');
//         $this->assertSame('Y', $NPC3->selectHitOrStand());
//     }
// }
