<?php 

interface Observer{
    public function onNotify($event, $data):void;
}