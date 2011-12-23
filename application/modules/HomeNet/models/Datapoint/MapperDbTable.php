<?php

/*
 * DatapointMapperDbTable.php
 *
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 *
 * This file is part of HomeNet.
 *
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Datapoint
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Datapoint_MapperDbTable implements HomeNet_Model_Datapoint_MapperInterface {

    protected $_table;

    protected $_type;
    protected $_database;
    protected $_house;


    public function  __construct($house_id, $database, $type = null) {
        $this->_type = strtolower($type);
        
        $this->_house = $house_id;
        
        $config = Zend_Registry::get('config');
            
            if(APPLICATION_ENV == 'testing'){
                $database = Zend_Db_Table_Abstract::getDefaultAdapter();
            } else {
                if($database == 1){
                    $database = Zend_Db_Table_Abstract::getDefaultAdapter();
                } elseif($database == 2){
                    $database = Zend_Db::factory('PDO_MYSQL', array(
                        'host'             => 'localhost',
                        'dbname'           => 'homenet_data',
                        'username'         => $config->resources->db->params->username,
                        'password'         => $config->resources->db->params->password));
                } else {
                    throw new Exception('Invalid Database');
                }
            }
        
        $this->_database = $database;
    }
   
    /**
     *
     * @return HomeNet_Model_DbTable_Datapoints;
     */
    public function getTablePrefix(){
        if(empty($this->_house)){
            throw new Exception('Missing House Id');
        }
        return 'homenet_datapoint_' . mysql_real_escape_string($this->_house).'_';
    }
    
    private function getDatabase(){
        return $this->_database;
    }
    
    public function getTable() {
        if ($this->_table === null) {
            
            $prefix = $this->getTablePrefix();
            
            switch($this->_type){
                case HomeNet_Model_Datapoint::BOOLEAN:
                case 'bool':
                case 'boolean':
                    $table = $prefix.'boolean';
                    break;
                case HomeNet_Model_Datapoint::BYTE:
                case 'byte':
                    $table = $prefix.'byte';
                    break;
                case HomeNet_Model_Datapoint::FLOAT:
                case 'float':
                    $table = $prefix.'float';
                    break;
                case HomeNet_Model_Datapoint::INTEGER:
                case 'int':
                case 'integer':
                    $table = $prefix.'int';
                    break;
                case HomeNet_Model_Datapoint::LONG:
                case 'long':
                    $table = $prefix.'long';
                    break;
                default:
                    throw new Exception('Invalid Type: '.$this->_type);
                    break;
            }
            
            
            
            $this->_table = new Zend_Db_Table(array('name' => $table, 
                                                    'db' => $this->getDatabase()));
            $this->_table->setRowClass('HomeNet_Model_Datapoint_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }
    
    public function createTables() {

        $prefix = $this->getTablePrefix();

        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'boolean` (
          `id` int(11) unsigned NOT NULL,
          `timestamp` datetime NOT NULL,
          `value` tinyint(1) unsigned NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MyISAM;');

        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'byte` (
          `id` int(11) unsigned NOT NULL,
          `timestamp` datetime NOT NULL,
          `value` tinyint(3) unsigned NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MyISAM;');

        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'float` (
          `id` int(11) unsigned NOT NULL,
          `timestamp` datetime NOT NULL,
          `value` float NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MyISAM;');

        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'int` (
          `id` int(11) unsigned NOT NULL,
          `timestamp` datetime NOT NULL,
          `value` int(11) NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MyISAM;');

        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'long` (
          `id` int(11) NOT NULL,
          `timestamp` datetime NOT NULL,
          `value` bigint(20) NOT NULL,
          PRIMARY KEY (`id`,`timestamp`)
        ) ENGINE=MyISAM;');
        
        $this->getDatabase()->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'messages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `user` int(11) DEFAULT NULL,
          `room` int(11) DEFAULT NULL,
          `component` int(11) DEFAULT NULL,
          `level` tinyint(3) unsigned NOT NULL,
          `message` varchar(128) CHARACTER SET utf8 NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM;');
        
    }
    
    public function deleteTables(){
         //drop table
        $prefix = $this->getTablePrefix();
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'boolean`');
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'byte`');
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'float`');
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'int`');
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'long`');
        $result = $this->getDatabase()->query('DROP TABLE IF EXISTS `'.$prefix.'messages`');

        return $result;
    }

    public function fetchLastObjectById($id) {

        $select = $this->getTable()->select()
                        //->from($this,array('datetime','value'))
                        ->where('id = ?', $id)
                        ->order('timestamp DESC')
                        ->limit(1);

        return $this->getTable()->fetchRow($select);
    }

    public function fetchAveragesByIdTimespan($id, Zend_Date $start, Zend_Date $end, $points = null) {
        /*
         * @todo check that are in order
         */
        $unixStart = $start->getTimestamp();
        $unixEnd = $end->getTimestamp();

        $interval = ($unixEnd - $unixStart) / $points;

        // $start to unix time stamp
        // $end to unix time stamp
        // group = $end-$start / $points
        // average(value) as average
        // floor(unixtimestamp(date) / group) as point
        //to unix timestamp() as timestamp
        //group by point
        //'totalGuests' => new Zend_Db_Expr($totalGuests)

        //die(debugArray($this->getTable()));
$table = $this->getTable();

        $select = $table->select()
                        ->setIntegrityCheck(false)
                        ->from($table, array('average' => new Zend_Db_Expr('ROUND(AVG(value),2)'),
                            'interval' => new Zend_Db_Expr('FLOOR(UNIX_TIMESTAMP(timestamp)/' . $interval . ')')))
                        ->where('id = ?', $id)
                        ->where('timestamp > ?', $start->toString('YYYY-MM-dd HH:mm:ss'))
                        ->where('timestamp < ?', $end->toString('YYYY-MM-dd HH:mm:ss'))
                        ->order('timestamp ASC')
                        ->group('interval');
        // ->order('interval ASC');

        //return array();

        $rows = $this->getTable()->fetchAll($select);

        $datapoints = array();

        //$offset = $rows->current()->interval;
        $offset = $unixStart / $interval;

        foreach ($rows as $row) {
            $datapoints[$row->interval - $offset] = $row->average;
        }



        for ($i = 0; $i < $points; $i++) {
            if (empty($datapoints[$i])) {
                $datapoints[$i] = null;
            }
        }
        ksort($datapoints);


        $nullCount = 0;

        $max = 4 * 60 * 60;

        $steps = floor($max / $interval);
        //die($steps);
        $start = true;
        foreach ($datapoints as $key => $value) {
            if (is_null($value)) {
                $nullCount++;
                continue;
            } elseif (($nullCount > 0) && ($nullCount < $steps)) {
                $pos = 1;
                if (!$start) {
                    /*  while(array_key_exists($pos,$datapoints) && is_null($datapoints[$pos])){
                      unset($datapoints[$pos]);
                      $pos--;
                      }
                      } else { */

                    $first = $datapoints[$key - $nullCount - 1]; //72
                    //die($first);
                    // $difference = $datapoints[$key] - $first;

                    $step = ( $datapoints[$key] - $first) / ($nullCount + 1);


                    while (array_key_exists($key - $pos, $datapoints) && is_null($datapoints[$key - $pos])) {
                        $datapoints[$key - $pos] = round($first + ($step * ($pos / $nullCount)), 2);
                        $pos++;
                    }
                }
            }
            $nullCount = 0;
            $start = false;
        }

        return $datapoints;
    }

    public function fetchObjectsByIdTimespan($id, Zend_Date $start, Zend_Date $end) {

        $select = $this->getTable()->select()
                        //->from($this,array('datetime','value'))
                        ->where('id = ?', $id)
                        ->where('timestamp > ?', $start->toString('YYYY-MM-dd HH:mm:ss'))
                        ->where('timestamp < ?', $end->toString('YYYY-MM-dd HH:mm:ss'))
                        ->order('timestamp ASC');

        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Datapoint_Interface $object) {

        if (($object instanceof HomeNet_Model_Datapoint_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif (($object->id !== null) && ($object->timestamp !== null)) {
            $row = $this->getTable()->find($object->id, $object->timestamp)->current();
        } 
        
        if(empty($row)){
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
        
        return $row->save();
    }

    public function delete(HomeNet_Model_Datapoint_Interface $object) {

        if (($object instanceof HomeNet_Model_Datapoint_DbTableRow) && ($object->isConnected())) {
            return $object->delete();
        } elseif (($object->id !== null) && ($object->timestamp !== null)) {
            return $this->getTable()->find($object->id, $object->timestamp)->current()->delete();
        }

        throw new HomeNet_Model_Exception('Invalid Datapoint');
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }

}