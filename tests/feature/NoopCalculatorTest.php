<?php

declare(strict_types=1);

namespace Tests\feature;

use DateTime;
use PHPUnit\Framework\TestCase;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Calculator\Factory\StatisticsCalculatorFactory;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;
use Statistics\Extractor\StatisticsToExtractor;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class NoopCalculatorTest extends TestCase
{
    private $noopCalculator;
    private $posts;
    private $fictionalPostHydrator;
    private $extractor;
    public function setUp(): void
    {

        $this->extractor = new StatisticsToExtractor();
        $this->fictionalPostHydrator = new FictionalPostHydrator();
        $this->posts = $this->buildPosts();


        $params = [(new ParamsTo())
            ->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER)
            ->setStartDate(new DateTime('2018-08-01 00:00:00'))
            ->setEndDate(new DateTime('2018-08-30 23:59:59'))];

        $this->noopCalculator = StatisticsCalculatorFactory::create($params);
    }
   
    public function testDoCalculate()
    {
        foreach ($this->posts as $post) {
            $this->noopCalculator->accumulateData($post);
        }

        $stats = $this->noopCalculator->calculate();


        $stats = $this->extractor->extract($stats, ['teste' => 'teste']);
        $this->assertEquals((float) 1, $stats['children'][0]['value']);
    }

    private function buildPosts()
    {
        $fakeResponse = \file_get_contents('tests/data/social-posts-response.json');
        $fakeResponse = json_decode($fakeResponse, true);
        $posts = $fakeResponse['data']['posts'];

        $postsDto = [];
        foreach ($posts as $postData) {
            $postsDto[] = $this->fictionalPostHydrator->hydrate($postData);
        }
        return $postsDto;
    }
}
