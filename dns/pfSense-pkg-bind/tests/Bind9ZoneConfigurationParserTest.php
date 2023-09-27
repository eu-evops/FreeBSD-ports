<?php

declare(strict_types=1);

include '../files/usr/local/pkg/bind/lib/Bind9ZoneConfigurationParser.php';
include '../files/usr/local/pkg/bind/lib/Bind9ZoneConfiguration.php';
include '../files/usr/local/pkg/bind/lib/Bind9ZoneRecord.php';
include '../files/usr/local/pkg/bind/lib/Bind9ZoneRecordParser.php';
include '../files/usr/local/pkg/bind/lib/Bind9ZoneSOARecordParser.php';
include '../files/usr/local/pkg/bind/lib/Bind9ZoneSOARecord.php';

use PHPUnit\Framework\TestCase;

class Bind9ZoneConfigurationParserTest extends TestCase
{
    public function testConfigurationParsingBindGenerated(): void
    {
        $parser = new Bind9ZoneConfigurationParser("./data/before.conf", "example.com");
        $configuration = $parser->parse("example.com");

        $this->assertInstanceOf('Bind9ZoneConfiguration', $configuration);
        $this->assertEquals(16, count($configuration->records));
    }

    public function testConfigurationParsingUserGenerated(): void
    {
        $parser = new Bind9ZoneConfigurationParser("./data/after.conf", "example.com");
        $configuration = $parser->parse("example.com");

        $this->assertInstanceOf('Bind9ZoneConfiguration', $configuration);
        $this->assertEquals(15, count($configuration->records));
    }

    public function testConfigurationDiff(): void
    {
        $dynamicParser = new Bind9ZoneConfigurationParser("./data/before.conf");
        $pfsenseParser = new Bind9ZoneConfigurationParser("./data/after.conf");
        $dynamicConfiguration = $dynamicParser->parse("example.com");
        $pfsenseConfiguration = $pfsenseParser->parse("example.com");

        $pfsenseConfiguration->merge($dynamicConfiguration);

        $this->assertCount(16, $pfsenseConfiguration->records);
    }
}
