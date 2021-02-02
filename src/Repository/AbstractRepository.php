<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Contao\System;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use mysqli;
use Psr\Log\LoggerInterface;

abstract class AbstractRepository {

    protected static ?LoggerInterface $logger = null;
    private mysqli $connection;

    /**
     * AbstractRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        if(self::$logger == null) {
            self::$logger = System::getContainer()->get('monolog.logger.contao');
        }
        $connection = $registry->getConnection();
        $this->connection = new mysqli($connection->getHost(), $connection->getUserName(),$connection->getPassword(), $connection->getDatabase(), $connection->getPort());
    }

    /**
     * @param string $query
     * @param array $namedParams
     * @param callable|null $translation
     * @return int
     * @throws Exception
     */
    protected function executeNamedQuery(string $query, array $namedParams, callable $translation = null): int {
        $params = array();
        foreach ($namedParams as $name => $value) {
            $query = str_replace(":$name", $this->prepare($value), $query);
            $params[] = $value;
        }
        if(preg_match('/:[a-z]+/', $query) > 0) {
            throw new Exception("query parameter missing $query");
        }
        if($stm = $this->connection->prepare($query)){
            if($stm->execute()) {
                switch (strtolower(substr($query, 0, 6))){
                    case 'select':
                        $str = $stm->get_result();
                        if(is_callable($translation)) {
                            while ($row = $str->fetch_assoc()) {
                                $translation($row);
                            }
                        }
                        return $str->num_rows;
                    case 'insert';
                        return $stm->insert_id ? $stm->insert_id : $stm->affected_rows;
                    default:
                        return $stm->affected_rows;
                }
            } else {
                throw new Exception("query could not be executed $query\n" . json_encode($params) . " || " . $this->connection->error);
            }
        } else {
            throw new Exception("query could not prepared $query\n" . $this->connection->error);
        }
    }

    private function prepare($value) : string {
        if(is_string($value)) {
            return '\'' . mysqli_real_escape_string($this->connection, $value) . '\'';
        } elseif (is_int($value)) {
            return strval($value);
        } elseif (is_double($value)) {
            return strval($value);
        } elseif ($value == null) {
            return 'NULL';
        }

    }

    /**
     * @param array $array
     * @return mixed|null
     */
    protected static function last(array $array) {
        if(null == $array || empty($array)) return null;
        return $array[count($array)-1];
    }

    /**
     * @param $message
     */
    protected static function info($message): void {
        self::$logger->info(var_export($message, true));
    }
}
