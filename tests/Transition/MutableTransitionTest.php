<?php

/**
 * Test the mutable transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Transition;

use IronBound\State\State\StateId;
use IronBound\State\Transition\{MutableTransition, TransitionId};
use PHPUnit\Framework\TestCase;

use function IronBound\State\mapMethod;

class MutableTransitionTest extends TestCase
{
    public function testToImmutable(): void
    {
        $transition = $this->makeTransition();
        $immutable  = $transition->toImmutable();

        $this->assertEquals($transition->getId()->getName(), $immutable->getId()->getName());
        $this->assertEquals($transition->getFinalState()->getName(), $immutable->getFinalState()->getName());
        $this->assertEquals(
            mapMethod($transition->getInitialStates(), 'getName'),
            mapMethod($immutable->getInitialStates(), 'getName')
        );
    }

    public function testGetFinalState(): void
    {
        $transition = $this->makeTransition();
        $finalState = $transition->getFinalState();

        $this->assertEquals('active', $finalState->getName());
    }

    public function testAddInitialState(): void
    {
        $transition = $this->makeTransition();
        $state      = new StateId('inactive');
        $transition->addInitialState($state);

        $initialStates = $transition->getInitialStates();
        $this->assertCount(2, $initialStates);
        $this->assertEquals('inactive', $initialStates[1]->getName());
    }

    public function testGetId(): void
    {
        $transition = $this->makeTransition();
        $this->assertEquals('activate', $transition->getId()->getName());
    }

    public function testGetInitialStates(): void
    {
        $transition    = $this->makeTransition();
        $initialStates = $transition->getInitialStates();

        $this->assertCount(1, $initialStates);
        $this->assertEquals('pending', $initialStates[0]->getName());
    }

    private function makeTransition(): MutableTransition
    {
        return new MutableTransition(
            new TransitionId('activate'),
            [
                new StateId('pending'),
            ],
            new StateId('active')
        );
    }
}
