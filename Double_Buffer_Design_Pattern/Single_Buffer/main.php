<?php 

require_once 'src/SingleBufferScene.php';
require_once 'src/VideoDriver.php';
require_once 'src/DoubleBufferScene.php';
require_once 'src/FrameBuffer.php';

// get the videoDriver 
$videoDriver = new VideoDriver();
// get the scene..
$singleScene = new SingleBufferScene();
$doubleScene = new DoubleBufferScene();


### USING SINGLE BUFFER SCENE
// echo "\n=== SINGLE BUFFER ===\n";
// for($frame=1;$frame<=5;$frame++){

//     $singleScene->draw();
//     $buffer = $singleScene->getBuffer();
//     $tornPixels = $videoDriver->readPixels($buffer);

//    echo "What Video Driver Sees (TORN):\n";
//     echo $videoDriver->displayFrame($tornPixels,$buffer->getWidth(),$buffer->getHeight());

//     echo "What Should Be Displayed:\n";
//     echo $buffer->display();
//     echo "\n";
// }

echo "\n=== DOUBLE BUFFER ===\n";
for($frame = 1; $frame <= 5; $frame++){
    echo "Frame $frame:\n";
    
    // Game draws to back, then swaps
    $doubleScene->draw();  
    
    // VideoDriver reads from front (always complete!)
    $buffer = $doubleScene->getBuffer();
    
    // You COULD use VideoDriver, but no tearing is possible:
    $pixels = $buffer->getPixels(); // Complete frame guaranteed
    echo "What Video Driver Sees (ALWAYS CLEAN):\n";
    echo $videoDriver->displayFrame($pixels, $buffer->getWidth(), $buffer->getHeight());
    echo "\n";
}