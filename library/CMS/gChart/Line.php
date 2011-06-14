<?php
//namespace gchart;
class CMS_gChart_Line extends CMS_gChart
{
    public function __construct($width = 200, $height = 200)
    {
        $this->setProperty('cht', 'lc');
        $this->setDimensions($width, $height);
    }
    public function getUrl()
    {
        $retStr = parent::getUrl();
        return $retStr;
    }
}