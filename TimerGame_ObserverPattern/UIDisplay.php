<?php 
require_once 'Observer.php';
require_once 'TimerEvent.php';
class UIDisplay implements Observer {
    private string $playerName;

    public function __construct(string $playerName='Player1'){
        $this->playerName = $playerName;
    }

    public function onNotify($event, $data):void{
        switch($event){
            case TimerEvent::TICK: 
                echo 'UI : Timer Display ' . $data['timeRemaining'] . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;
            case TimerEvent::WARNING_30:
                echo 'UI : 30 seconds remaining!' . $data['timeRemaining'] . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;
            case TimerEvent::HALFWAY_POINT:
                echo 'UI : Halfway point reached! '.  $data['timeRemaining'] . '</br>';
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;
            case TimerEvent::GAME_OVER:
                echo 'UI : Game Over' . '</br>'; 
                ob_flush();  // Flush PHP's own buffer
                flush();     // Flush system (Apache) buffer
                break;        
        }

    }
}