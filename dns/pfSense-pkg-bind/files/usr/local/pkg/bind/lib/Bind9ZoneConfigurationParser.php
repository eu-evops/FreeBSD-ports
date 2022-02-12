<?php

if (!function_exists('str_starts_with')) {
  function str_starts_with($haystack, $needle)
  {
    return strpos($haystack, $needle) === 0;
  }
}

if (!function_exists('str_ends_with')) {
  function str_ends_with($haystack, $needle)
  {
    return strpos($haystack, $needle) === strlen($haystack) - 1;
  }
}

class Bind9ZoneConfigurationParser
{
  private string $filePath;
  public function __construct(string $filePath)
  {
    $this->filePath = $filePath;
  }

  public function parse(string $zoneName): Bind9ZoneConfiguration
  {
    $f = fopen($this->filePath, 'r');

    $currentTTL = 0;
    $currentOrigin = "";
    $parsingSOA = false;

    $soaParser = new Bind9ZoneSOARecordParser();
    $recordParser = new Bind9ZoneRecordParser();
    $bindConfiguration = new Bind9ZoneConfiguration();

    $soaRecordText = "";
    $currentHost = "";

    if ($f) {
      while (($line = fgets($f)) !== false) {
        $line = trim($line);
        $lineComment = null;
        if (preg_match("/;/", $line)) {
          $lineComment = preg_replace("/.*;\s*/", "", $line);
          $line = preg_replace("/\s*;.*/", "", $line);
        }
        $isRecord = true;

        if (strlen($line) == 0) continue;
        if ($line[0] == ';') continue;

        if (str_starts_with($line, '$TTL')) {
          $isRecord = false;

          $currentTTL = substr(preg_replace("/\s*;.*/", "", $line), 5);
        }

        if (str_starts_with($line, '$ORIGIN')) {
          $isRecord = false;
          $currentOrigin = substr($line, 8);
        }

        if (str_ends_with($line, '(')) {
          $parsingSOA = true;
          // $line = substr($line, 0, strlen($line) - 1);
        }

        if ($parsingSOA) {
          $soaRecordText .= " " . $line;
        }

        if (str_ends_with($line, ')')) {
          $isRecord = false;
          $parsingSOA = false;

          $soaRecord = $soaParser->parse(trim($soaRecordText));
          $soaRecord->zoneName = $zoneName;
          $soaRecord->origin = $currentOrigin;
          $soaRecord->ttl = $currentTTL;
          $bindConfiguration->records[] = $soaRecord;
          $currentRecord = $soaRecord;
        }

        if ($isRecord && !$parsingSOA) {
          if ($recordParser->canParse($line)) {
            $record = $recordParser->parse($line);
            $record->zoneName = $zoneName;
            $record->origin = $currentOrigin;
            $record->ttl = $currentTTL;

            if (!$record->host) {
              $record->inheritedHost = $currentHost;
            }

            if ($currentRecord && $record->host == "") {
              $record->inheritedHost = $currentRecord->host;
            }

            $bindConfiguration->records[] = $record;
            if ($record->host) {
              $currentHost = $record->host;
            }
          }
        }
      }
    }

    fclose($f);

    return $bindConfiguration;
  }
}
