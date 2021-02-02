<?php

namespace Kommunikatisten\ContaoScheduleBundle\Entity;


abstract class AbstractEntity {

    protected int $id;
    protected string $lastModified;

    /**
     * AbstractEntity constructor.
     */
    public function __construct() {
        $this->id = 0;
        $this->lastModified = '';
    }


    public function equals(AbstractEntity $entity): bool {
        return $this->serialize(false) == $entity->serialize(false);
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

    /**
     * @param bool $encode
     * @return array | string
     */
    abstract public function serialize(bool $encode);

    abstract static function apply(array $array, bool $cascade = true): AbstractEntity;
    abstract function merge(array $array, bool $cascade = true): void;

    /**
     * @param array $values
     * @param array &$target
     * @param bool $cascade
     */
    abstract static function merging(array $values, array &$target, bool $cascade);

    /**
     * @param AbstractEntity[] $entities
     * @return array
     */
    protected static function serializeChilds(array $entities): array {
        return array_map(function(AbstractEntity $entity) {
            return $entity->serialize(false);
        }, $entities);
    }

    public static function applyAfterInsert(AbstractEntity $entity, int $id) {
        if($entity->id === 0) {
            $entity->id = $id;
        }
    }
}
