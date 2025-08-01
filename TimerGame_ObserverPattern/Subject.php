<?php 

class Subject{
    # subject should have 2 things. 1: list of observers 2: methods to notify theses observers.. 
    private array $observers = []; // make it strict type.. 
    // private string $str;

    public function addObserver(Observer $observer):void{
        $this->observers[] = $observer;
        echo 'Observer Added ' . '</br>';
        ob_flush();  // Flush PHP's own buffer
        flush();     // Flush system (Apache) buffer
    }

    public function removeObserver(Observer $observer):void{
        foreach($this->observers as $key=>$obj){
            if($obj===$observer)unset($this->observers[$key]);
        }
        echo 'Observer Removed ' . '</br>';
        ob_flush();  // Flush PHP's own buffer
        flush();     // Flush system (Apache) buffer
    }

    protected function notify($event, $data):void{
        foreach($this->observers as $observer){
            $observer->onNotify($event,$data);
        }
    }

}