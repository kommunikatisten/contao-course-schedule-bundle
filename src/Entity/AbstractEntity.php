<?php

namespace Kommunikatisten\ContaoScheduleBundle\Entity;


abstract class AbstractEntity {

    protected int $id;
    protected string $lastModified;
    protected array $current;

    /**
     * @return AbstractEntity
     */
    protected function fix(): AbstractEntity {
        $this->current = json_decode(json_encode($this), true);
        return $this;
    }

    /**
     * @return array
     */
    public function getCurrent(): array {
        return $this->current;
    }

    public function hasChanged(): bool {
        return json_encode($this) == $this->current;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLastModified(): string {
        return $this->lastModified;
    }



    abstract static function apply(array $array, bool $cascade = true): AbstractEntity;
}
