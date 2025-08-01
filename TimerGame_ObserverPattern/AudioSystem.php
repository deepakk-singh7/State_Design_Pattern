<?php 
require_once 'Observer.php';
require_once 'TimerEvent.php';
class AudioSystem implements Observer{
    private int $volume; 
    public function __construct($volume = 0.8) {
        $this->volume = $volume;
    }
    public function onNotify($event, $data):void {
        switch($event){
            case TimerEvent::TICK:
                echo 'Audio : Tick' . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;
            case TimerEvent::WARNING_30:
                echo 'Audio : Warning_30' . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;
            case TimerEvent::HALFWAY_POINT:
                echo 'Audio : Halfway_point' . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;    
            case TimerEvent::GAME_OVER:
                echo 'Audio : Game Over' . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;        
        }
    }
}