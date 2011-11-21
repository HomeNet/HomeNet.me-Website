<?php

/*
 * Interface.php
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
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Element_DateTime_Element extends Content_Model_Plugin_Element  {

    
  /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = parent::getSetupForm($options);
        $form->setLegend('DateTime Options');
        
        $type = $form->createElement('multiCheckbox', 'target');
        $type->setLabel('Show: ');
        $type->setRequired('true');

        $options = array(
            'year' => 'Year',
            'month' => 'Month',
            'day' => 'Day',
            'hour' => 'Hour',
            'minute' => 'Minute',
            'second' => 'Second',
        );
        $type->setMultiOptions($options);
        $form->addElement($type);
        
        $format = $form->createElement('select', 'format');
        $format->setLabel('Format: ');
        $format->setRequired('true');

        $options = array(
            'd/M/yyyy' => '9-2-2009',
            'EEEE, MMMM d, yyyy' => 'Monday February 9, 2009',
            'MMMM d, yyyy' => 'February 9, 2011',
            'yyyy-M-d' => '2009-2-9',
            'yyyy-MM-dd' => '2009-02-09',
            'd-M-yyyy' => '9-2-2011',
            'dd-MM-yyyy' => '9-2-2011',
            
        );
        $format->setMultiOptions($options);
        $form->addElement($format);
        
        return $form;
    }
    /*Constant 	Description 	Corresponds best to 	Result
G 	Epoch, localized, abbreviated 	                                Zend_Date::ERA              AD
GG 	Epoch, localized, abbreviated 	                                Zend_Date::ERA              AD
GGG 	Epoch, localized, abbreviated 	                                Zend_Date::ERA              AD
GGGG 	Epoch, localized, complete 	                                Zend_Date::ERA_NAME         anno domini
GGGGG 	Epoch, localized, abbreviated 	                                Zend_Date::ERA              a
y 	Year, at least one digit 	                                Zend_Date::YEAR             9
yy 	Year, at least two digit 	                                Zend_Date::YEAR_SHORT       09
yyy 	Year, at least three digit 	                                Zend_Date::YEAR             2009
yyyy 	Year, at least four digit 	                                Zend_Date::YEAR             2009
yyyyy 	Year, at least five digit                                       Zend_Date::YEAR             02009
Y 	Year according to ISO 8601, at least one digit                  Zend_Date::YEAR_8601        9
YY 	Year according to ISO 8601, at least two digit                  Zend_Date::YEAR_SHORT_8601  09
YYY 	Year according to ISO 8601, at least three digit                Zend_Date::YEAR_8601        2009
YYYY 	Year according to ISO 8601, at least four digit                 Zend_Date::YEAR_8601        2009
YYYYY 	Year according to ISO 8601, at least five digit                 Zend_Date::YEAR_8601        02009
M 	Month, one or two digit                                         Zend_Date::MONTH_SHORT      2
MM 	Month, two digit                                                Zend_Date::MONTH            02
MMM 	Month, localized, abbreviated 	                                Zend_Date::MONTH_NAME_SHORT Feb
MMMM 	Month, localized, complete                                      Zend_Date::MONTH_NAME       February
MMMMM 	Month, localized, abbreviated, one digit                        Zend_Date::MONTH_NAME_NARROW 	F
w 	Week, one or two digit                                          Zend_Date::WEEK             5
ww 	Week, two digit                                                 Zend_Date::WEEK             05
d 	Day of the month, one or two digit                              Zend_Date::DAY_SHORT        9
dd 	Day of the month, two digit                                     Zend_Date::DAY              09
D 	Day of the year, one, two or three digit                        Zend_Date::DAY_OF_YEAR      7
DD 	Day of the year, two or three digit                             Zend_Date::DAY_OF_YEAR      07
DDD 	Day of the year, three digit                                    Zend_Date::DAY_OF_YEAR      007
E 	Day of the week, localized, abbreviated, one char               Zend_Date::WEEKDAY_NARROW   M
EE 	Day of the week, localized, abbreviated, two or more chars 	Zend_Date::WEEKDAY_NAME     Mo
EEE 	Day of the week, localized, abbreviated, three chars            Zend_Date::WEEKDAY_SHORT    Mon
EEEE 	Day of the week, localized, complete                            Zend_Date::WEEKDAY          Monday
EEEEE 	Day of the week, localized, abbreviated, one digit              Zend_Date::WEEKDAY_NARROW   M
e 	Number of the day, one digit                                    Zend_Date::WEEKDAY_DIGIT    4
ee 	Number of the day, two digit                                    Zend_Date::WEEKDAY_NARROW   04
a 	Time of day, localized                                          Zend_Date::MERIDIEM         vorm.
h 	Hour, (1-12), one or two digit                                  Zend_Date::HOUR_SHORT_AM    2
hh 	Hour, (01-12), two digit                                        Zend_Date::HOUR_AM          02
H 	Hour, (0-23), one or two digit                                  Zend_Date::HOUR_SHORT       2
HH 	Hour, (00-23), two digit                                        Zend_Date::HOUR             02
m 	Minute, (0-59), one or two digit 	                        Zend_Date::MINUTE_SHORT     2
mm 	Minute, (00-59), two digit                                      Zend_Date::MINUTE           02
s 	Second, (0-59), one or two digit 	                        Zend_Date::SECOND_SHORT     2
ss 	Second, (00-59), two digit                                      Zend_Date::SECOND           02
S 	Millisecond                                                     Zend_Date::MILLISECOND      20536
z 	Time zone, localized, abbreviated 	                        Zend_Date::TIMEZONE         CET
zz 	Time zone, localized, abbreviated 	                        Zend_Date::TIMEZONE         CET
zzz 	Time zone, localized, abbreviated 	                        Zend_Date::TIMEZONE         CET
zzzz 	Time zone, localized, complete                                  Zend_Date::TIMEZONE_NAME    Europe/Paris
Z 	Difference of time zone                                         Zend_Date::GMT_DIFF         +0100
ZZ 	Difference of time zone                                         Zend_Date::GMT_DIFF         +0100
ZZZ 	Difference of time zone                                         Zend_Date::GMT_DIFF         +0100
ZZZZ 	Difference of time zone, separated 	                        Zend_Date::GMT_DIFF_SEP     +01:00
A 	Milliseconds from the actual day 	                        Zend_Date::MILLISECOND      20563*/
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
       // $element = new ZendX_JQuery_Form_Element_DatePicker();
        $element = new Zend_Form_Element_Text($config); 
       // $element = new Zend_Form_Element_Text($field->name); 
        return $element;
    }
}