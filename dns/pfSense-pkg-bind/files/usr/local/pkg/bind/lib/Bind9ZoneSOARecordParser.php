<?php

class Bind9ZoneSOARecordParser
{
  private $componentOrder = [
    'serial',
    'refresh',
    'retry',
    'expire',
    'minimum'
  ];

  private $currentComponentIndex = 0;

  public function __construct()
  {
  }

  public function parse(string $recordText): Bind9ZoneSOARecord
  {
    $soaRecord = new Bind9ZoneSOARecord();
    $soaRecord->line = $recordText;

    if (($match = preg_match('/(?<host>.*?)\s*(IN\s*)?(?<type>(SOA|A|MX|CNAME|PTR|NS|TXT))\s+(?<data>.*)\s+(?<email>.*)\s+\(/', $recordText, $r) != 0)) {
      $soaRecord->host = $r['host'];
      $soaRecord->type = $r['type'];
      $soaRecord->data = $r['data'];
      $soaRecord->email = $r['email'];
    }

    preg_match("/\((?<soa>.*)\)/", $recordText, $matches);

    $soaComponents = preg_split("/\s+/", $matches['soa']);
    foreach ($soaComponents as &$soaComponent) {
      if (trim($soaComponent) == "") continue;

      $soaRecord->{$this->componentOrder[$this->currentComponentIndex++]} = $soaComponent;
    }

    return $soaRecord;
  }
}
