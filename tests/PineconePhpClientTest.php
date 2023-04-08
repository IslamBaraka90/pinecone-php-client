<?php
use Islambaraka90\PineconePhpClient\PineconePhpClient;
use PHPUnit\Framework\TestCase;

class PineconePhpClientTest extends TestCase
{
    public function testPineconePhpClient(): void
    {
        $client = new PineconePhpClient();

        $result = $client->doSomething();

        $this->assertEquals('expected result', $result);
    }

}
