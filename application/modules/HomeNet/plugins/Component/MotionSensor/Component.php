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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Component
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Plugin_Component_MotionSensor_Component extends HomeNet_Model_Component_Abstract {

     public function saveDatapoint($value, $timestamp) {

        if (empty($this->settings['datatype'])) {
            throw new Zend_Exception('this subdevice doesn\'t have a datatype to save a value');
        }



//        $class = 'HomeNet_Model_DbTable_Datapoints' . ucfirst($this->settings['datatype']);
//
//        if (!class_exists($class)) {
//            throw new Zend_Exception('Invalid Datatype: ' . $class);
//        }

        $dService = new HomeNet_Model_Datapoint_Service();
        $dService->add($this->settings['datatype'],$this->id,$value,$timestamp);

//        $table = new $class();
//        // $table = new HomeNet_Model_DbTable_DatapointsBoolean();
//
//        $row = $table->createRow();
//
//        $row->subdevice = $this->id;
//        $row->datetime = $timestamp;
//
//        //$value = $this->_convertValue($value);
//        $row->value = $value;
//
//        $row->save();
    }



    public function hasSummary() {
        return true;
    }

    public function getSummary() {
        return 'COntact switch';
    }

    public function hasNewestDataPoint() {
        return '';
    }

    public function getNewestDataPoint() {
        $dService = $this->getDatapointService();
        $row = $dService->getNewestDatapointByComponent($this->id);
        if (empty($row)) {
            return array();
        }

        $units = $this->getSetting('units');

        $array = array(
            'datetime' => new Zend_Date($row->datetime, Zend_Date::ISO_8601),
            'value' => round($this->_convertValue($row->value), 2) . $this->getSetting('units')
        );

        return $array;
    }

    /**
     * Get graph array
     *
     * @return Zend_Form
     */
    public function hasGraphs() {
        return true;
    }

    public function getGraphPresets() {
        return array(
            'Last Hour' => array('start' => '-1 hour', 'end' => 'now'),
            'Last 6 Hours' => array('start' => '-6 hours', 'end' => 'now'),
            'Last Day' => array('start' => '-1 day', 'end' => 'now'),
            'Last Week' => array('start' => '-1 Week', 'end' => 'now'),
            'Last Month' => array('start' => '-1 month', 'end' => 'now')
        );
    }

    /**
     * Get stats graph
     *
     * @return Zend_Form
     */
    public function getGraph(Zend_Date $start, Zend_Date $end, $width = 250, $height = 100) {


        $datapoints = $this->getDataPoints($start, $end, $width / 4);

        $lineChart = new CMS_gChart_Line($width, $height);

        $lineChart->addDataSet($datapoints);

        //$lineChart->setEncodingType('s');
        //$lineChart->setEncodingType('s');
        if ($height < 62) {
            //$lineChart->setEncodingType('s');//
        } else {
            //$lineChart->setEncodingType('e');
        }

        $numOfXLabels = floor($width / 75);
        //$numOfLabels = 2;
        $labelsX = array();
        $labelXPositions = array();

        $positionXStep = 100 / ($numOfXLabels - 1);


        if ($numOfXLabels >= 2) {


            //get date difference
            $difference = $end->getTimestamp() - $start->getTimestamp();

            //offset to nearest?


            $dateStep = $difference / ($numOfXLabels - 1);

            $date = $start;

            for ($i = 0; $i < $numOfXLabels; $i++) {

                if ($difference <= 86400) {
                    $labelsX[] = $start->toString(Zend_Date::TIME_SHORT);
                } else {
                    $labelsX[] = $start->toString('MMM d');
                }
                $labelXPositions[] = round($i * $positionXStep);
                $start->addSecond($dateStep);
            }
        }

        //y labels
        $numOfYLabels = floor($height / 25);
        //$numOfLabels = 2;
        $labelsY = array();
        $labelYPositions = array();


        $range = $this->getSetting('range');

        //die(debugArray($range));

        if (!is_null($range)) {
            $min = (int) $range[0];
            $max = (int) $range[1];
            $difference = $max - $min;
        } else {
            //fixes null being treated like 0
            $datapointsNoNulls = array_filter($datapoints, "is_notnull");

            if (!empty($datapointsNoNulls)) {

                $max = max($datapointsNoNulls);
                $min = min($datapointsNoNulls);



                $padding = 5;
                $difference = $max - $min;
                if ($difference < $padding) {
                    $padding = $difference;
                }



                //add some breathing room
                $max = ceil($max + $padding);

                if (!(($min > 0 ) && ($min < 5 ))) {
                    $min = floor($min - $padding);
                }
            } else {
                $max = 100;
                $min = 0;
            }
            $difference = $max - $min;


            //$temp = array($min, $max, $difference);
        }



        $valueYStep = $difference / ($numOfYLabels - 1);

        // die(debugArray($valueYStep));

        $positionYStep = 100 / ($numOfYLabels - 1);

        $units = $this->getSetting('units');

        if ($valueYStep < 1) {

            for ($i = 0; $i < $numOfYLabels; $i++) {

                $labelsY[] = round(($i * $valueYStep) + $min, 1) . $units;
                $labelYPositions[] = round($i * $positionYStep, 1);
            }
        } else {
            for ($i = 0; $i < $numOfYLabels; $i++) {

                $labelsY[] = round(($i * $valueYStep) + $min) . $units;
                $labelYPositions[] = round($i * $positionYStep, 1);
            }
        }


        $lineChart->setDataRange($min, $max);
        //   $lineChart = new gLineChart(300,300);
//$lineChart->setLegend(array("first", "second", "third","fourth"));
        $lineChart->setColors(array("CC6600"));
        $lineChart->addBackgroundFill('bg', '00000000');
        $lineChart->setVisibleAxes(array('x', 'y'));
        $lineChart->setGridLines(round($positionXStep, 1), round($positionYStep, 1));
        $lineChart->addAxisLabel(0, $labelsX);
        $lineChart->addAxisLabelPositions(0, $labelXPositions);
        $lineChart->addAxisLabel(1, $labelsY);
        $lineChart->addAxisLabelPositions(1, $labelYPositions);
        $lineChart->addDataSet($datapoints);
//$lineChart->setDataRange(30,400);
//$lineChart->addAxisRange(0, 1, 4, 1);
//$lineChart->addAxisRange(1, 30, 400);



        return $lineChart->getUrl();
    }

    /**
     * Get Datapoints
     *
     * @return Zend_Form
     */
    public function getDataPoints($start, $end, $density = null) {

        $dService = $this->getDatapointService();

        $rows = $dService->getAveragesByComponentTimespan($this->id, $start, $end, $density);
        //die(debugArray($rows));
        return $rows; //->toArray();
    }
}