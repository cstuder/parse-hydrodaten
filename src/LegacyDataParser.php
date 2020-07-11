<?php

namespace cstuder\ParseHydrodaten;

/**
 * Parser for legacy Hydrodaten data strings
 *
 */
class LegacyDataParser
{
    /**
     * Imported parameter types and translations
     */
    protected const PARAMETER_TYPES = [
        3 => 'temperature',
        10 => 'flow',
        2 => 'height',
        22 => FALSE, // Flow l/s
        1 => FALSE, // Height absolute
    ];

    /**
     * Timezone of the timestamps in the XML string
     */
    protected const TIMEZONE = 'Europe/Zurich';

    /**
     * Parse data string
     * 
     * @param string $raw legacy Hydrodaten data string
     * @return array
     */
    public static function parse(string $raw)
    {
        $points = [];
        $xml = simplexml_load_string($raw);

        // Loop over stations
        $count = 0;

        foreach ($xml->MesPar as $station) {
            // Extract values
            $loc = (string) $station['StrNr'];

            $type = (int) $station['Typ'];
            $par = self::PARAMETER_TYPES[$type] ?? FALSE;
            if ($par === FALSE) continue; // Ignored parameter

            $datetime = ((string) $station->Datum) . ' ' . ((string) $station->Zeit) . ':00';

            $values = $station->Wert;
            $value = NULL;
            $previousValue = NULL;

            // TODO continue here

            foreach ($values as $v) {
                $attributes = $v->attributes();

                if (count($attributes)) {
                    if (isset($v['dt'])) {
                        $previousValue = (float) $v;
                    }
                } else {
                    // The node without attributes is the value we're looking for
                    $value = (float) $v;
                }
            }

            // Validate data
            $timestamp = strtotime("{$datetime} {$timezone}");
            if ($timestamp == 0) {
                ExistenzApiFactory::log("ERROR: Unable to parse '{$datetime}' for location {$loc}, parameter {$par}.");
                continue;
            }

            if ($value == 0) {
                ExistenzApiFactory::log("ERROR: Finding invalid value '{$value}' for location {$loc}, parameter {$par}.");
                continue;
            }

            // Valid value found
            $success = $hydro->insertValue($loc, $par, $timestamp, $value, TRUE);

            if (!$success) {
                ExistenzApiFactory::log("ERROR: Unable to insert value '{$value}' for location {$loc}, parameter {$par}.");
            } else {
                $count++;
            }

            // Prepare for insert into Influx
            $points[] = new InfluxDB\Point(
                $influxMeasurement,
                null,
                ['location' => $loc],
                [$par => floatval($value)],
                $timestamp
            );

            if ($previousValue == 0) continue;

            // Insert previous value
            $previousTimestamp = strtotime("{$datetime} {$timezone} -1 day");
            $success = $hydro->insertValue($loc, $par, $previousTimestamp, $previousValue, TRUE);

            if (!$success) {
                ExistenzApiFactory::log("ERROR: Unable to insert previous value '{$previousValue}' for location {$loc}, parameter {$par}.");
            } else {
                $count++;
            }

            // Prepare for insert into Influx
            $points[] = new InfluxDB\Point(
                $influxMeasurement,
                null,
                ['location' => $loc],
                [$par => floatval($previousValue)],
                $previousTimestamp
            );
        }
    }
}
