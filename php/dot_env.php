<?php

$path = __DIR__ . '/..';
$file = $path . '/.env';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
  if (strpos(trim($line), '#') === 0) {
    continue;
  }
  list($name, $value) = explode('=', $line, 2);
  $name = trim($name);
  $value = trim($value);

  if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
    $value = $matches[1];
  }

  if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
    putenv(sprintf('%s=%s', $name, $value));
    $_ENV[$name] = $value;
    $_SERVER[$name] = $value;
  }
}
