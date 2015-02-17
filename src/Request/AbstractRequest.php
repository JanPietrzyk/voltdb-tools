<?php

namespace Jpietrzyk\VoltDbTools\Request;

use PDO;

abstract class AbstractRequest {
    
    protected $dbServer;

    protected $procedure;

    protected $parameters;

    protected $fetchMode;

    public function __construct($host, $port, $username = null, $password = null) {
        $this->dbServer = "http://$host:$port/api/1.0/";
        $this->reset();
    }
    
    protected function reset() {
        $this->parameters = array();
        $this->setFetchMode(PDO::FETCH_ASSOC);
    }

    public function setFetchMode($mode) {
        $this->fetchMode = $mode;
        return $this;
    }

    public function setProcedure($procedure) {
        $this->procedure = $procedure;

        return $this;
    }

    public function addParameter($param) {
        if(!$this->parameters) {
            $this->parameters = array();
        }

        $this->parameters[] = $param;

        return $this;
    }


    /**
     * @param string $queryString
     * @return string Raw response
     */
    abstract protected function fetchResult($queryString);

    public function request()
    {
        $queryString = $this->createQueryString();
        $resRaw = $this->fetchResult($queryString);
        
        $res = json_decode($resRaw);

        if(JSON_ERROR_NONE != json_last_error()) {
            throw new RequestException('Invalid server response "' . json_last_error_msg() . '"');
        }

        if($res['status'] != ResponseInterpreter::STATUS_SUCCESS) {
            throw new RequestException('Request failed, DB returned error status: "' . $res['statusstring'] . '"');
        }

        $data = $res['results'];


        $ret = [];
        foreach($data as $resultTable) {
            $ret[] = $this->remapResult($resultTable);
        }

        $this->reset();

        if(count($data) == 1) {
            return $ret[0];
        }

        return $ret;
    }
    
    protected function createQueryString() {
        $querystring = "Procedure=" . $this->procedure;

        if (count($this->parameters)) {
            $querystring .= "&Parameters=" . urlencode(json_encode($this->parameters));
        }
        
        return $querystring;
    }

    protected function remapResult($result) {
        switch($this->fetchMode) {
            // (default): returns an array indexed by both column name and 0-indexed column number as returned in your result set
            case PDO::FETCH_BOTH:
                return array_merge_recursive(
                    $this->fetchAssoc($result['data'], $result['schema']),
                    $this->fetchNum($result['data'], $result['schema'])
                    );
                break;
            // returns an array indexed by column name as returned in your result set
            case PDO::FETCH_ASSOC:
                return $this->fetchAssoc($result['data'], $result['schema']);
                break;
            // returns an array indexed by column number as returned in your result set, starting at column 0
            case PDO::FETCH_NUM:
                return $this->fetchNum($result['data'], $result['schema']);
                break;
            //Specifies that the fetch method shall return only a single requested column from the next row in the result set.
            case PDO::FETCH_COLUMN:
                throw new Exception('Not implemented');
                break;
            // return stdClass
            case PDO::FETCH_OBJ:
                throw new \Exception('Not implemented');
                break;
            default:
                throw new \Exception('Invalid fetch mode specified "' . $this->fetchMode . '"');
        }

    }

    protected function fetchAssoc(array $data, array $schema) {

        $ret = array();
        foreach($data as $row) {
            $rowResult = [];
            foreach($row as $index => $field) {
                $rowResult[$schema[$index]['name']] = $field;
            }
            $ret[] = $rowResult;
        }

        return $ret;
    }

    protected function fetchNum(array $data, array $schema) {

        $ret = array();
        foreach($data as $row) {
            $rowResult = [];
            foreach($row as $index => $field) {
                $rowResult[$schema[$index]['name']] = $field;
            }
            $ret[] = $rowResult;
        }

        return $ret;
    }
}
