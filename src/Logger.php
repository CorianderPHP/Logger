<?php

namespace CorianderCore\Modules\Logger;

use DateTime;
use DateTimeZone;
use DateInterval;
use Exception;

/**
 * Simple logger class for handling log files with a configurable reset interval.
 */
class Logger
{
    private $logFilePath;
    private $timeZone;
    private $resetInterval;
    private $intervalUnit;

    /**
     * Constructor for Logger.
     *
     * @param string $logFilePath Path to the log file.
     * @param string $timeZone Time zone to use for logs (e.g. 'Europe/Paris').
     * @param string $intervalUnit The unit of the interval: 'hours', 'days', or 'weeks'.
     * @param int $resetInterval The number representing the interval (e.g., 72 for 72 hours if you've selected hours for intervalUnit).
     * @throws Exception If the provided interval unit is invalid.
     */
    public function __construct($logFilePath, $timeZone, $intervalUnit = 'days', $resetInterval = 1)
    {
        $this->logFilePath = $logFilePath;
        $this->timeZone = new DateTimeZone($timeZone);
        $this->resetInterval = $resetInterval;
        $this->intervalUnit = $intervalUnit;

        // Validate the interval unit
        if (!in_array($this->intervalUnit, ['hours', 'days', 'weeks'])) {
            throw new Exception("Invalid interval unit. Allowed units are: 'hours', 'days', 'weeks'.");
        }

        $this->initializeLogFile();
    }

    /**
     * Ensures the log file is ready for writing logs. Resets based on the specified interval (hours, days, or weeks).
     */
    private function initializeLogFile()
    {
        if (!file_exists($this->logFilePath)) {
            return; // No file to reset
        }

        $fileModificationTime = filemtime($this->logFilePath);
        $currentDateTime = new DateTime("now", $this->timeZone);
        $fileDateTime = (new DateTime())->setTimestamp($fileModificationTime);

        // Determine the appropriate DateInterval based on the unit
        switch ($this->intervalUnit) {
            case 'hours':
                $intervalSpec = 'PT' . $this->resetInterval . 'H'; // PT = Period Time, H = hours
                break;
            case 'weeks':
                $intervalSpec = 'P' . $this->resetInterval . 'W'; // P = Period, W = weeks
                break;
            case 'days':
            default:
                $intervalSpec = 'P' . $this->resetInterval . 'D'; // P = Period, D = days
                break;
        }

        // Calculate the threshold for when the log file should be reset
        $resetThreshold = clone $fileDateTime;
        $resetThreshold->add(new DateInterval($intervalSpec));

        // If the current time has passed the threshold, reset the log file
        if ($currentDateTime >= $resetThreshold) {
            unlink($this->logFilePath);
        }
    }

    /**
     * Logs a message to the specified log file with a timestamp.
     *
     * @param string $fileName The filename of the log initiator.
     * @param string $type The type of the log (like error, log, success...).
     * @param string $message The message to log.
     * @throws Exception If the log file is not writable.
     */
    public function log($fileName, $type = "log", $message)
    {
        if (!is_writable(dirname($this->logFilePath))) {
            throw new Exception("Log directory is not writable.");
        }

        $dateTime = new DateTime("now", $this->timeZone);
        $formattedDate = $dateTime->format('Y-m-d H:i:s');
        $logEntry = "[$formattedDate] | [$fileName] [$type] > $message\n";

        // Append the log entry to the file
        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX);
    }
}