<?php 

class FrameBuffer {
    private const HEIGHT = 10; 
    private const WIDTH = 10; 
    private const WHITE = '.';
    private const BLACK = '*';

    public array $pixels = [];

    public function __construct(){
        $this->clear();
    }

    public function clear():void{
        $this->pixels = array_fill(0,self::WIDTH*self::HEIGHT,self::WHITE);
    }

    public function draw(int $x, int $y):void{
        if($x>=0&&$x<self::WIDTH && $y>=0 && $y<self::HEIGHT){
            $this->pixels[(self::WIDTH * $y) + $x] = self::BLACK;
        }
    }
    public function getPixels():array{
        return $this->pixels;
    }
    public function display():string{
        $result = "";
        for($y=0;$y<self::HEIGHT;$y++){
            for($x=0;$x<self::WIDTH;$x++){
                $result .= $this->pixels[(self::WIDTH * $y ) + $x] . " ";
            }
            $result .= PHP_EOL;
        }
        return $result;
    }

    public function getHeight():int{
        return self::HEIGHT;
    }
    public function getWidth():int{
        return self::WIDTH;
    }
}