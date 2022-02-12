<?php

class Bind9ZoneRecord
{
  public string $line;
  public string $origin;
  public string $ttl;
  public string $type;
  public string $host;
  public string $inheritedHost = "";
  public string $data;
  public string $zoneName;

  public function toString(int $hostPadding = 0, int $typePadding = 0, int $dataPadding = 0): string
  {
    $result = str_pad($this->host, $hostPadding) . " " . str_pad($this->type, $typePadding) . " " . str_pad($this->data, $dataPadding);
    return $result;
  }

  public function getFqdn(): string
  {
    return $this->getResultingHost($this->host);
  }

  private function getResultingHost(string $host): string
  {
    // FQDN
    if (strlen($host) > 1 && str_ends_with($host, ".")) {
      return $host;
    }

    if ($host == ".") {
      return $this->getResultingHost($this->origin);
    }

    if ($host == "" && $this->inheritedHost != "") {
      return $this->getResultingHost($this->inheritedHost);
    }

    if ($host == "@") {
      return $this->zoneName . ".";
    }

    if ($this->origin == ".") {
      return $host . $this->origin;
    }

    return $host . "." . $this->origin;
  }
}
