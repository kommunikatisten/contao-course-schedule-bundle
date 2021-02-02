<?php


namespace Kommunikatisten\ContaoScheduleBundle\Service\BE;


use Contao\System;
use Psr\Log\LoggerInterface;

abstract class AbstractBackendService {


    private static ?LoggerInterface $logger = null;

    /**
     * AbstractBackendService constructor.
     */
    public function __construct() {
        if(self::$logger == null) {
            self::$logger = System::getContainer()->get('monolog.logger.contao');
        }
    }


    protected static function info($message): void {
        $m = is_string($message) ? $message : var_export($message, true);
        self::$logger->info('Service: ' . $m);
    }

    protected static function toBeAdded(array $actualIds, array $currentIds): array {
        return array_filter($actualIds, function(int $actualId) use ($currentIds){
            return !in_array($actualId, $currentIds);
        });
    }
    protected static function toBeDeleted(array $actualIds, array $currentIds): array {
        return array_filter($currentIds, function(int $currentId) use ($actualIds){
            return !in_array($currentId, $actualIds);
        });
    }
}
