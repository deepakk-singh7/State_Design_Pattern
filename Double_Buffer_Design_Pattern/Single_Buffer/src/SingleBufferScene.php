<?php 

require_once 'FrameBuffer.php';
require_once 'VideoDriver.php';
class SingleBufferScene{

    private FrameBuffer $buffer; 

    public function __construct(){
        $this->buffer = new FrameBuffer();
    }

    public function draw():void{
        $this->buffer->clear();
        // drawing a square.. 
        for($x=3;$x<=7;$x++){
            for($y=3;$y<=7;$y++){
                $this->buffer->draw($x,$y);
            }
        }
        // $this->buffer->draw(2, 2);
        // $this->buffer->draw(5, 2);
        
        // // Nose
        // $this->buffer->draw(4, 4);
        
        // // Mouth
        // $this->buffer->draw(2, 6);
        // $this->buffer->draw(3, 6);
        // $this->buffer->draw(4, 6);
        // $this->buffer->draw(5, 6);
    }
    public function getBuffer(): Framebuffer 
    {
        return $this->buffer;
    }
}