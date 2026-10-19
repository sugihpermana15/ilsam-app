<?php

namespace App\Services;

class CareerCvAntivirusScanner
{
  public function scan(string $absoluteFilePath): string
  {
    $command = trim((string) config('career_security.antivirus.command'));
    if ($command === '' || $absoluteFilePath === '') {
      return 'error';
    }

    $command .= ' --no-summary ' . escapeshellarg($absoluteFilePath) . ' 2>&1';

    $output = [];
    $exit = 2;
    @exec($command, $output, $exit);

    return match ((int) $exit) {
      0 => 'clean',
      1 => 'infected',
      default => 'error',
    };
  }
}
