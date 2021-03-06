<?php

/**
 * State Machine Factory.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

use IronBound\State\Graph\{GraphId, GraphLoader};
use IronBound\State\ConcreteStateMachine;
use IronBound\State\Exception\UnsupportedSubject;
use IronBound\State\StateMachine;
use IronBound\State\StateMediator\StateMediatorFactory;

final class ConcreteStateMachineFactory implements StateMachineFactory
{
    /** @var StateMediatorFactory */
    private $mediatorFactory;

    /** @var GraphLoader */
    private $loader;

    /** @var SupportsTest */
    private $test;

    /**
     * Factory constructor.
     *
     * @param StateMediatorFactory $mediatorFactory The factory to create state mediators.
     * @param GraphLoader          $loader          The loader to create graphs.
     * @param SupportsTest         $test            Callback to test if this factory is for a given subject.
     */
    public function __construct(StateMediatorFactory $mediatorFactory, GraphLoader $loader, SupportsTest $test)
    {
        $this->mediatorFactory = $mediatorFactory;
        $this->loader          = $loader;
        $this->test            = $test;
    }

    public function make(object $subject, GraphId $graphId): StateMachine
    {
        if (! $this->supports($subject)) {
            throw new UnsupportedSubject('This state machine factory does not support the given subject.');
        }

        $mediator = $this->mediatorFactory->make($graphId);
        $graph    = $this->loader->make($graphId);

        return new ConcreteStateMachine($mediator, $graph, $subject);
    }

    public function supports(object $subject): bool
    {
        $callback = $this->test;

        return $callback($subject);
    }

    /**
     * Returns a support test for choosing a factory based on the subject's class.
     *
     * @param string $class
     *
     * @return SupportsTest
     */
    public static function classTest(string $class): SupportsTest
    {
        return new class ($class) implements SupportsTest {
            /** @var string */
            private $class;

            public function __construct(string $class)
            {
                $this->class = $class;
            }

            public function __invoke(object $subject): bool
            {
                return $subject instanceof $this->class;
            }
        };
    }
}
