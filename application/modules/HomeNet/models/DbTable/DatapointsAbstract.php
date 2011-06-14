<?php

/*
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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Datapoint
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_DatapointsAbstract extends Zend_Db_Table_Abstract {

   // protected $_rowClass = 'HomeNet_Model_DbTableRow_Datapoint';

    public function fetchNewestRowBySubdevice($subdevice) {

        $select = $this->select()
                        //->from($this,array('datetime','value'))
                        ->where('subdevice = ?', $subdevice)
                        ->order('datetime DESC')
                        ->limit(1);

        return $this->fetchRow($select);
    }

    public function fetchAveragesBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end, $points = null) {

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

        $select = $this->select()
                        ->setIntegrityCheck(false)
                        ->from($this, array('average' => new Zend_Db_Expr('ROUND(AVG(value),2)'),
                            'interval' => new Zend_Db_Expr('FLOOR(UNIX_TIMESTAMP(datetime)/' . $interval . ')')))
                        ->where('subdevice = ?', $subdevice)
                        ->where('datetime > ?', $start->toString('YYYY-MM-dd HH:mm:ss'))
                        ->where('datetime < ?', $end->toString('YYYY-MM-dd HH:mm:ss'))
                        ->order('datetime ASC')
                        ->group('interval');
        // ->order('interval ASC');

        $rows = $this->fetchAll($select);

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
            foreach($datapoints as $key => $value){
                if(is_null($value)){
                    $nullCount++;
                    continue;
                } elseif(($nullCount > 0) && ($nullCount < $steps) ){
                    $pos = 1;
                    if(!$start){
                      /*  while(array_key_exists($pos,$datapoints) && is_null($datapoints[$pos])){
                            unset($datapoints[$pos]);
                            $pos--;
                        }
                    } else {*/

                        $first = $datapoints[$key-$nullCount-1]; //72
                        //die($first);

                       // $difference = $datapoints[$key] - $first;

                        $step = ( $datapoints[$key] - $first) / ($nullCount+1);


                        while(array_key_exists($key-$pos,$datapoints) && is_null($datapoints[$key-$pos])){
                            $datapoints[$key-$pos] = round($first + ($step * ($pos/$nullCount)),2);
                            $pos++;
                        }
                    }
                    
                }
                $nullCount = 0;
                $start = false;

            }
        

        return $datapoints;
    }

    public function fetchRowsBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end) {

        //die($subdevice);


        $select = $this->select()
                        //->from($this,array('datetime','value'))
                        ->where('subdevice = ?', $subdevice)
                        ->where('datetime > ?', $start->toString('YYYY-MM-dd HH:mm:ss'))
                        ->where('datetime < ?', $end->toString('YYYY-MM-dd HH:mm:ss'))
                        ->order('datetime ASC');

        return $this->fetchAll($select);
    }

}

