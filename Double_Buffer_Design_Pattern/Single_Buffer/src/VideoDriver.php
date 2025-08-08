<?php 

require_once 'FrameBuffer.php';
class VideoDriver{
    private int $scanPosition = 0; # This controls the tearing...

    public function readPixels(FrameBuffer $buffer):array{
        $pixels = $buffer->getPixels();
        $result = [];
        
        for($i = 0; $i < count($pixels); $i++){
            $result[] = $pixels[$i];
        }

        // fill the remaining with the White
        while(count($result)<count($pixels)){
            $result[]= '.';
        }

        // update the scanPosition
        $this->scanPosition = ($this->scanPosition + 15) % count($pixels);
        return $result;

    }

    public function displayFrame(array $pixels, int $width, int $height):string{
        $result="";
        for($y=0;$y<$height;$y++){
            for($x=0;$x<$width;$x++){
                $result .= $pixels[($y*$width)+$x] . " ";
            }
            $result .= PHP_EOL;
        }
        return $result;
    }
}