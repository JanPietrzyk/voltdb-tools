<?php

use \Jpietrzyk\VoltDbTools\Query\InspectableQuery;
/**
 * Tests of InspectableQueryTest
 */
class InspectableQueryTest extends \PHPUnit_Framework_TestCase {
    
    public function testInvalidArgument() {
        $this->setExpectedException('InvalidArgumentException');
        new InspectableQuery('');
    }
    
    public function testSelect() {
        $selectQuery = new InspectableQuery('SELECT * FROM tmp');
        $this->assertEquals($selectQuery->getType(), InspectableQuery::TYPE_SELECT);
        
    }
    
    public function testInsert() {
        $insertQuery = new InspectableQuery('INSERT INTO tmp');
        $this->assertEquals($insertQuery->getType(), InspectableQuery::TYPE_INSERT);
    }
}
