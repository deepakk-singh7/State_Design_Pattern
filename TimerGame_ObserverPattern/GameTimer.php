<?php 
require_once 'Subject.php';
require_once 'TimerEvent.php';
class GameTimer extends Subject{
    private int $timeRemaining; 
    private int $totalTime; 
    private bool $isRunning; 

    public function __construct($totalTime){
        $this->totalTime = $totalTime;
        $this->timeRemaining = $totalTime;
        $this->isRunning = false;
    }

    public function start():void{
        echo 'TimeGame Started... ' . '</br>';
        ob_flush();  // Flush PHP's own buffer
        flush();     // Flush system (Apache) buffer
        $this->isRunning = true;

        $this->runTimer();
    }

    private function runTimer():void{
        while($this->timeRemaining>=0 && $this->isRunning){
            sleep(1);
            $this->timeRemaining--;
            echo 'TimeRamaining : ' . $this->timeRemaining . '</br>';
            ob_flush();  // Flush PHP's own buffer
            flush();     // Flush system (Apache) buffer

            // check for the special events. 
            $this->checkForEvents();

            // emit tick event every 10 sec.. 
            if($this->timeRemaining%10===0){
                $this->notify(TimerEvent::TICK,['timeRemaining' => $this->timeRemaining, 'totalTime' => $this->totalTime]);
            }

            // check for the game over 
            if($this->timeRemaining<=0){
                $this->notify(TimerEvent::GAME_OVER, ['timeRemaining' => $this->timeRemaining, 'totalTime'=>$this->totalTime]);
            }
        }
        
    }
    private function checkForEvents():void{

        switch($this->timeRemaining){
            case 30: $this->notify(TimerEvent::WARNING_30, ['timeRemaining' => $this->timeRemaining]);
                        break;
            case ($this->totalTime/2): $this->notify(TimerEvent::HALFWAY_POINT,['timeRemaining' => $this->timeRemaining]);
                        break;             
        }

    }

    public function stop():void{
        $this->isRunning = false;
    }

    public function getTimeRemaining():int{
        return $this->timeRemaining; 
    }
}