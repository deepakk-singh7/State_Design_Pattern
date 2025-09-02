<?php

// Declare the namespace for our test file.
namespace Tests;

// Import the base TestCase class from PHPUnit.
use PHPUnit\Framework\TestCase;
// Import the class we want to test. Composer's autoloader will find it!
use EmailNotifier;

/**
 * Test suite for the EmailNotifier class.
 */
class EmailNotifierTest extends TestCase
{
    /**
     * This is our test method.
     * It verifies that the send() method returns the expected string.
     */
    public function testSendReturnsCorrectSuccessMessage(): void
    {
        // 1. Arrange: Set up the objects and variables we need.
        $notifier = new EmailNotifier();
        $testMessage = 'Hello World';
        $expectedOutput = "✅ EMAIL: Message 'Hello World' sent successfully.";

        // 2. Act: Call the method we are testing.
        $actualOutput = $notifier->send($testMessage);

        // 3. Assert: Check if the result is what we expected.
        $this->assertEquals($expectedOutput, $actualOutput);
    }
}