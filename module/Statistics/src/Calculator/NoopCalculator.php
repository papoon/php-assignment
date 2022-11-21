<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class NoopCalculator extends AbstractCalculator
{
    protected const UNITS = 'posts';
    /**
     * @var array
     */
    private $postsPerUser = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $authorId = $postTo->getAuthorId();

        $this->postsPerUser[$authorId] = ($this->postsPerUser[$authorId] ?? 0) + 1;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $value =  empty($this->postsPerUser) ? 0 : array_sum($this->postsPerUser) / count($this->postsPerUser);

        return (new StatisticsTo())->setValue(round($value,2));
    }
}
