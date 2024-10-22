# Logger Module for CorianderPHP

The **Logger** module is a simple and flexible logging utility for the CorianderPHP framework. It allows developers to log messages to a file with customizable reset intervals, ensuring that logs can be managed efficiently based on your specific needs.

---

## Features
- **Configurable Reset Intervals**: Automatically reset log files based on a defined time interval (hours, days, or weeks).
- **Time Zone Support**: Set the desired time zone for log timestamps.
- **Log Types**: Supports different types of logs such as `log`, `error`, `success`, etc.

---

## Requirements
- **CorianderPHP Framework**: This module is designed to work within the CorianderPHP framework. (https://github.com/CorianderPHP/CorianderPHP)
- PHP version 8.2 or higher.

---

## Installation

At the moment, module installation will be managed natively through the CorianderPHP framework’s CLI, which is currently under development.

Stay tuned for updates!

---

## Usage

### Initializing the Logger
To use the logger, instantiate the `Logger` class by providing the path to the log file, the desired time zone, the reset interval unit (`hours`, `days`, or `weeks`), and the interval value.
```php
use CorianderCore\Modules\Logger;

// Initialize the logger
$logger = new Logger(
    '/path/to/log/file.log',     // Log file path
    'Europe/Paris',              // Time zone
    'days',                      // Reset interval unit ('hours', 'days', or 'weeks')
    1                            // Reset interval (e.g., 1 day)
);
```

### Logging a Message
Use the `log()` method to log messages to the specified file. You can pass the filename of the initiator, the log type, and the log message.
```php
$logger->log('myfile.php', 'error', 'An error occurred while processing the request.');
```
#### Parameters:
- **fileName**: The filename where the log originates.
- **type**: The type of log (e.g., log, error, success, etc.). Default is log.
- **message**: The message to be logged.

---

### Reset Interval Configuration
The logger can automatically reset the log file after a specified period. You can choose from the following reset intervals:

- **Hours**: Reset after a specific number of hours (e.g., every 72 hours).
- **Days**: Reset daily or after a specified number of days.
- **Weeks**: Reset weekly or after a specified number of weeks.

```php
// Example: Reset the log file every 72 hours
$logger = new Logger('/path/to/log/file.log', 'Europe/Paris', 'hours', 72);

// Example: Reset the log file every 7 days
$logger = new Logger('/path/to/log/file.log', 'Europe/Paris', 'days', 7);

// Example: Reset the log file every 2 weeks
$logger = new Logger('/path/to/log/file.log', 'Europe/Paris', 'weeks', 2);
```

### Error Handling
If the log directory is not writable, the `Logger` will throw an `Exception`:
```php
try {
    $logger->log('myfile.php', 'error', 'An error occurred.');
} catch (Exception $e) {
    echo "Logging failed: " . $e->getMessage();
}
```

---

## Contributing

We welcome contributions. If you'd like to contribute, please submit pull requests or report issues on GitHub.

---

## License

This project is licensed under the MIT License.
