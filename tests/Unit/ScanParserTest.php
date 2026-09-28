<?php

namespace AppTest\Service;

use App\Service\ScanParser;
use PHPUnit\Framework\TestCase;

class ScanParserTest extends TestCase
{
    private ScanParser $parser;

    protected function setUp(): void
    {
        $this->parser = new ScanParser();
    }

    public function testParsesValidMoonScan(): void
    {
        $scanData = <<<SCAN
40000001\tTitanium Chromide\t123.456
40000001\tTwisted Exergite\t654.321
40000002\tChromebit\t789.012
SCAN;

        $result = $this->parser->parseMoonScan($scanData);

        $this->assertSame(2, $result->getMoonCount());
        $this->assertContains(40000001, $result->getMoonIds());
        $this->assertContains(40000002, $result->getMoonIds());
    }

    public function testThrowsExceptionOnEmptyData(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parseMoonScan('');
    }

    public function testThrowsExceptionOnInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parseMoonScan('invalid scan data');
    }

    public function testValidatesMoonScan(): void
    {
        $validScan = "40000001\tTitanium Chromide\t123.456";
        $invalidScan = "this is not a scan";

        $this->assertTrue($this->parser->isValidMoonScan($validScan));
        $this->assertFalse($this->parser->isValidMoonScan($invalidScan));
    }
}
