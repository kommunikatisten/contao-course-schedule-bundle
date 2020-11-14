<?php


namespace Kommunikatisten\ContaoScheduleBundle\Repository;


use Doctrine\Persistence\ManagerRegistry;
use Exception;
use mysqli;

abstract class AbstractRepository {

    private mysqli $connection;

    /**
     * AbstractRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
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
        $matches = array();
        $params = array();
        preg_match_all('/(:[^\s]+)/i',$query, $matches);
        foreach ($namedParams as $name => $value) {
            $query = str_replace(":$name", '?', $query);
            $params[] = $value;
        }
        if(str_contains($query, ':')) {
            throw new Exception("query parameter missing $query");
        }

        if($stm = $this->connection->prepare($query)){
            foreach ($params as $param) {
                if(is_string($params)) {
                    $stm->bind_param('s', $param);
                } elseif (is_int($param)) {
                    $stm->bind_param('i', $param);
                } elseif (is_double($param)) {
                    $stm->bind_param('d', $param);
                } else {
                    $stm->bind_param('b', $param);
                }
            }
            if($stm->execute()) {
                if(null != $translation) {
                    $str = $stm->get_result();
                    while ($row = $str->fetch_assoc()){
                        $translation($row);
                    }
                    return $str->num_rows;
                } else {
                    return $stm->affected_rows;
                }

            } else {
                throw new Exception("query could not be executed $query\n" . $this->connection->error);
            }
        } else {
            throw new Exception("query could not prepared $query\n" . $this->connection->error);
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
}
