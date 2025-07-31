<?php 

require_once 'FrameBuffer.php';
class DoubleBufferScene{
    private array $buffers;
    private FrameBuffer $current;
    private FrameBuffer $next;

    public function __construct(){
        $this->buffers = [new FrameBuffer(), new FrameBuffer()];
        $this->current = $this->buffers[0];
        $this->next = $this->buffers[1];
    }

    public function draw(): void{
        $this->next->clear();
        // create the image.. 
        for($x=3;$x<=6;$x++){
            for($y=3;$y<=6;$y++){
                $this->next->draw($x,$y);
            }
        }

        // swap the buffers .. 
        $this->swap();

    }

    private function swap(): void{
            $temp = $this->current;
            $this->current = $this->next;
            $this->next = $temp;
    }

    public function getBuffer():FrameBuffer{
        return $this->current;
    }

}