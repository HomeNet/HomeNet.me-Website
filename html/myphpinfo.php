<?php

/*
 * newEmptyPHP.php
 * 
 * Copyright (c) 2012 Matthew Doll <mdoll at homenet.me>.
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
class Person
{   
    function __construct($c)
    {   
        $this->count = $c;
    }       

                
    function getPrev()
    {       
        return $this->prev; 
    }           
            
    function setPrev($pr)
    {   
        $this->prev = $pr;
    }   
    
    function getNext()
    {
        return $this->next;
    }

    function setNext($nxt)
    {
        $this->next = $nxt;
    }

    function shout($shout, $nth)
    {
        if ($shout < $nth)
        {
            return $shout + 1;
        }
        $this->getPrev()->setNext($this->getNext());
        $this->getNext()->setPrev($this->getPrev());
        return 1;
    }
}

class Chain
{
    public $first;
   // private $last;

    function __construct($size)
    {
        $last = null;
        for($i = 0; $i < $size ; $i++)
        {
            $current = new Person($i);
            if ($this->first == null) $this->first = $current;
            if ($last != null)
            {
                $last->setNext($current);
                $current->setPrev($last);
            }
            $last = $current;
        }
        $this->first->setPrev($last);
        $last->setNext($this->first);
    }

    function kill($nth)
    {
        $current = $this->first;
        $shout = 1;
        while($current->getNext() !== $current)
        {
            $shout =  $current->shout($shout,$nth);
            $current = $current->getNext();
        }
        $this->first = $current;
    }
}

$start = microtime(true);
$ITER = 100000;
for($i = 0 ; $i < $ITER ; $i++)
{
    $chain = new Chain(40);
    $chain->kill(3);
}
$end = microtime(true);
printf("Time per iteration = %3.2f microsecondsnr",(($end -  $start) * 1000000 / $ITER));

//phpinfo();
/*
for($i = 302; $i<=320;$i++){
    echo '<a href="http://reader.eblib.com.ezproxy.lib.usf.edu/(S(gldkj2npwv1uxjil0rvf0joe))/GetPage.aspx?r=pdf&z=0&pg='.$i.'&s=1333520088719"/>'.$i.'</a><br>';
   //              4
}
//*/
?>
