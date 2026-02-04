@php
  $raw = $raw ?? '';
  $alt = $alt ?? 'Image preview';
  $maxHeight = (int) ($maxHeight ?? 70);

  $rawStr = is_string($raw) ? trim($raw) : '';
  $url = '';

  if ($rawStr !== '') {
    if (preg_match('~^https?://~i', $rawStr)) {
      $path = (string) parse_url($rawStr, PHP_URL_PATH);
      $query = (string) parse_url($rawStr, PHP_URL_QUERY);

      if ($path !== '' && (str_starts_with($path, '/assets/') || str_starts_with($path, '/storage/'))) {
        $local = asset(ltrim($path, '/'));
        $url = $query !== '' ? ($local . '?' . $query) : $local;
      } else {
        $url = $rawStr;
      }
    } else {
      $url = asset(ltrim($rawStr, '/'));
    }
  }
@endphp

@if($url)
  <div class="mt-2">
    <img
      src="{{ $url }}"
      alt="{{ $alt }}"
      style="max-height: {{ $maxHeight }}px; width: auto;"
      class="border rounded"
      loading="lazy"
      decoding="async"
    >
  </div>
@endif
