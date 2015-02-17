<?php

namespace Jpietrzyk\VoltDbTools\Query;

/**
 * class InspectableQuery
 * 
 * A more inteligent way to inflect and inspect querys
 */
class InspectableQuery {

    /**
     * Query type SELECT
     */
    const TYPE_SELECT = 'select';

    /**
     * Query type INSERT
     */
    const TYPE_INSERT = 'insert';

    /**
     * Query type delete
     */
    const TYPE_DELETE = 'delete';

    /**
     * Query type update
     */
    const TYPE_UPDATE = 'update';

    /**
     *
     * @var string
     */
    protected $sourceQuery;

    /**
     *
     * @var array
     */
    protected $queryParts;

    /**
     *
     * @var string|null
     */
    protected $type;

    /**
     *
     * @var string|null
     */
    protected $mainTableName;

    /**
     *
     * @var array
     */
    protected $validStartWords = [
        'CREATE', 'SELECT', 'DELETE', 'INSERT', 
        'UPSERT', 'ALTER', 'DROP', 'EXPORT', 'IMPORT',
        'PARTITION', 'TRUNCATE', 'LOAD',
    ];

    /**
     * 
     * @param string $query
     */
    public function __construct($query) {
        $this->queryParts = explode(' ', trim($query));
        $this->sourceQuery = $query;
    }

    /**
     * @return bool
     */
    public function isQuery() {
        $firstPart = $this->queryParts[0];

        if(in_array($firstPart, $this->validStartWords)) {
            return true;
        }

        return false;
    }

    /**
     * 
     * @return string
     * @throws QueryNotSupportedException
     */
    public function getType() {
        if($this->type) {
            return $this->type;
        }

        //since querys can come from everywhere....
        switch(strtoupper($this->queryParts[0])) {
            case 'SELECT':
                $this->type = self::TYPE_SELECT;
                break;
            case 'UPDATE':
                $this->type = self::TYPE_UPDATE;
                break;
            case 'INSERT':
                $this->type = self::TYPE_INSERT;
                break;
            case 'DELETE':
                $this->type = self::TYPE_DELETE;
                break;
            default:
                throw new QueryNotSupportedException('Unsupported query "' . $this->queryParts[0] . '"');
        }

        return $this->type;
    }

    /**
     * WARNING: SELECT not supported, since this means real parsing!
     *
     * @return string
     * @throws QueryNotSupportedException
     */
    public function getMainTableName() {
        if($this->mainTableName) {
            return $this->mainTableName;
        }

        switch($this->getType()) {
            case self::TYPE_INSERT:
            case self::TYPE_DELETE:
                $this->mainTableName = strtolower($this->queryParts[2]);
                break;
            case self::TYPE_UPDATE:
                $this->mainTableName = strtolower($this->queryParts[1]);
                break;
            default:
                throw new QueryNotSupportedException('Type not suported: "' . $this->getType() . '"');
        }

        return $this->mainTableName;
    }

    /**
     * Get the actual SQL as string
     * 
     * @return string
     */
    public function toSql() {
        return $this->sourceQuery;
    }
    
    /**
     * A unique id reprenting this query
     * 
     * @return string
     */
    public function getUid() {
        return sha1($this->toSql());
    }
    
    /**
     * Wrapper for toSql()
     * 
     * @return string
     */
    public function __toString() {
        return $this->toSql();
    }
}