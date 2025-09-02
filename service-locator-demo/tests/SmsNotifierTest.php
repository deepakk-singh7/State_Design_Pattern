<?php 

namespace Test; 

use PHPUnit\Framework\TestCase;

use SmsNotifier;

class SmsNotifierTest extends TestCase{


    public function testSendSms(): void{

        // 1: Arrange 
        $notifier = new SmsNotifier();
        $input = 'Hello Scopely';
        $expectedOutput = "📱 SMS: Message 'Hello Scopely' sent successfully.";

        // 2: Act 
        $actualOutput = $notifier->send($input);

        // 3: Assert 

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}