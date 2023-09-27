<?php

class Bind9ZoneRecordParser
{
  const DnsRecordTypes = array(
    "A",
    "NS",
    "MD",
    "MF",
    "CNAME",
    "SOA",
    "MB",
    "MG",
    "MR",
    "NULL",
    "WKS",
    "PTR",
    "HINFO",
    "MINFO",
    "MX",
    "TXT",
    "RP",
    "AFSDB",
    "X25",
    "ISDN",
    "RT",
    "NSAP",
    "NSAP",
    "SIG",
    "KEY",
    "PX",
    "GPOS",
    "AAAA",
    "LOC",
    "NXT",
    "EID",
    "NIMLOC",
    "SRV",
    "ATMA",
    "NAPTR",
    "KX",
    "CERT",
    "A6",
    "DNAME",
    "SINK",
    "OPT",
    "APL",
    "DS",
    "SSHFP",
    "IPSECKEY",
    "RRSIG",
    "NSEC",
    "DNSKEY",
    "DHCID",
    "NSEC3",
    "NSEC3PARAM",
    "TLSA",
    "SMIMEA",
    "HIP",
    "NINFO",
    "RKEY",
    "TALINK",
    "CDS",
    "CDNSKEY",
    "OPENPGPKEY",
    "CSYNC",
    "ZONEMD",
    "SPF",
    "UINFO",
    "UID",
    "GID",
    "UNSPEC",
    "NID",
    "L32",
    "L64",
    "LP",
    "EUI48",
    "EUI64",
    "TKEY",
    "TSIG",
    "IXFR",
    "AXFR",
    "MAILB",
    "MAILA",
    "URI",
    "CAA",
    "AVC",
    "DOA",
    "AMTRELAY",
    "TA",
    "DLV"
  );

  public function canParse(string $recordText): bool
  {
    return preg_match($this->getPattern(), $recordText) == 1;
  }

  public function parse(string $recordText): Bind9ZoneRecord
  {
    $record = new Bind9ZoneRecord();
    $record->line = $recordText;

    if ((preg_match($this->getPattern(), $recordText, $r) != 0)) {
      $record->host = $r['host'];
      $record->type = $r['type'];
      $record->data = $r['data'];
    }

    return $record;
  }

  private function getPattern(): string
  {
    return '/(?<host>.*?)\s*(IN\s*)?(?<type>(' . join("|", Bind9ZoneRecordParser::DnsRecordTypes) . '))\s*(?<data>.*)/';
  }
}
