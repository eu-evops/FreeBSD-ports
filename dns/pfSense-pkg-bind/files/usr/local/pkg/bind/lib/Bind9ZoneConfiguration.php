<?php

class Bind9ZoneConfiguration
{
  public array $records = [];

  public function toString()
  {
    $result = "";
    $origin = "";
    $ttl = 0;

    $longestHost = 0;
    $longestType = 0;
    $longestData = 0;

    foreach ($this->records as $record) {
      if (strlen($record->host) > $longestHost) {
        $longestHost = strlen($record->host);
      }
      if (strlen($record->type) > $longestType) {
        $longestType = strlen($record->type);
      }
      if (strlen($record->data) > $longestData) {
        $longestData = strlen($record->data);
      }
    }

    foreach ($this->records as $record) {
      if ($ttl != $record->ttl) {
        $ttl = $record->ttl;
        $result .= "\n\$TTL " . $record->ttl . "\n";
      }

      if ($origin != $record->origin) {
        $origin = $record->origin;
        $result .= "\n\$ORIGIN " . $record->origin . "\n";
      }

      $result .= $record->toString($longestHost, $longestType, $longestData) . " ; TTL " . str_pad($record->ttl, 6) . " ORIGIN " . $record->origin . " FQDN " . $record->getFqdn() . " PHOST " . $record->inheritedHost . " -- " . $record->line . "\n";
    }
    return $result;
  }

  public function merge(Bind9ZoneConfiguration $other)
  {
    foreach ($other->records as $record) {
      if ($record->type == "SOA") continue;
      if ($record->type == "NS") continue;

      if ($this->hasRecord($record)) {
        $this->updateRecord($record);
      } else {
        $this->records[] = $record;
      }
    }
  }

  private function hasRecord(Bind9ZoneRecord $other): bool
  {
    foreach ($this->records as $r) {
      if ($r->getFqdn() == $other->getFqdn() && $r->type == $other->type) {
        return true;
      }
    }
    return false;
  }

  private function updateRecord(Bind9ZoneRecord $other)
  {
    foreach ($this->records as $r) {
      if ($r->host == $other->host && $r->type == $other->type) {
        $r->data = $other->data;
        $r->ttl = $other->ttl;
        break;
      }
    }
  }
}
