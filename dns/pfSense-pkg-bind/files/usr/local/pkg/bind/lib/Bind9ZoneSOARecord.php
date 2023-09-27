<?php

class Bind9ZoneSOARecord extends Bind9ZoneRecord
{
  public string $master;
  public string $email;
  public string $serial;
  public string $refresh;
  public string $retry;
  public string $expire;
  public string $minimum;

  public function toString(int $hostPadding = 0, int $typePadding = 0, int $dataPadding = 0): string
  {
    $recordNs = parent::toString($hostPadding, $typePadding, $dataPadding);
    $recordNs .= $this->email;
    $recordNs .= " (\n";
    $recordNs .= "    " . str_pad($this->serial, 15) . " ; serial \n";
    $recordNs .= "    " . str_pad($this->refresh, 15) . " ; refresh \n";
    $recordNs .= "    " . str_pad($this->retry, 15) . " ; retry \n";
    $recordNs .= "    " . str_pad($this->expire, 15) . " ; expire \n";
    $recordNs .= "    " . str_pad($this->minimum, 15) . " ; minimum \n";
    $recordNs .= ")\n";
    return $recordNs;
  }
}
