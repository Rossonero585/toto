<?php


namespace Builders\Providers\FonBet;

use Builders\Providers\EventInterface;
use \stdClass;

class EventFromWeb implements EventInterface
{
    /** @var \stdClass */
    private $event;

    /** @var string */
    private $lang;

    public function __construct(stdClass $toto, $lang = 'ru')
    {
        $this->event = $toto;
        $this->lang = $lang;
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getNumber(): ?int
    {
        return $this->event->Order + 1;
    }

    public function getP1(): float
    {
        return $this->event->Win1->Probability / 100;
    }

    public function getPx(): float
    {
        return $this->event->Draw->Probability / 100;
    }

    public function getP2(): float
    {
        return $this->event->Win2->Probability / 100;
    }

    public function getS1(): float
    {
        return $this->event->Win1->Percentage / 100;
    }

    public function getSx(): float
    {
        return $this->event->Draw->Percentage / 100;
    }

    public function getS2(): float
    {
        return $this->event->Win2->Percentage / 100;
    }

    public function getLeague(): string
    {
        foreach ($this->event->Championships as $championship) {
            if ($championship->Key == $this->lang) {
                return $championship->Value;
            }
        }

        return $this->event->Championships[0]->Value;
    }

    public function getTile(): string
    {
        foreach ($this->event->Names as $name) {
            if ($name->Key == $this->lang) {
                return $name->Value;
            }
        }

        return $this->event->Names[0]->Value;
    }

    public function getResult(): ?string
    {
        return $this->event->ResultCode;
    }

    public function getSource(): ?string
    {
        return null;
    }

}
