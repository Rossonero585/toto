<?php

namespace Builders\Providers;

use Builders\EventBuilder;
use Builders\Providers\Factory\DataProviderFactory;
use Builders\Providers\Factory\EventProviderFactory;
use Helpers\FileParser;

abstract class BetRequest
{
    /**
     * @var string
     */
    protected $totoId;

    /**
     * @var string
     */
    protected $bookMaker;

    /**
     * @var bool
     */
    protected $isTest;

    public function __construct(string $totoId, string $bookMaker, bool $isTest)
    {
        $this->totoId = $totoId;
        $this->bookMaker = $bookMaker;
        $this->isTest = $isTest;
    }

    protected function getEventsArray(string $eventsFile)
    {
        $eventsAssoc = FileParser::parseFileWithEvents($eventsFile);

        $dataProvider  = DataProviderFactory::createDataProvider($this->bookMaker, $this->totoId);

        $out = [];

        foreach ($dataProvider->getEvents() as $key => $jsonEvent)
        {
            $number = $key + 1;

            $event = EventBuilder::createEvent(new EventFromMixedSource(
                EventProviderFactory::createEventProvider($this->bookMaker, $jsonEvent, 'ru', $number),
                new EventFromArray($eventsAssoc[$key]),
                $number
            ));

            array_push($out, $event);
        }

        return $out;
    }
}