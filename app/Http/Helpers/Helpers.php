<?php

  if (!function_exists('getVersion')) {
    function getVersion()
    {
      switch(config('app.env')) {
        case 'production':
          return config('app.copyright.version');
          break;
        default:
          return time();
          break;
      }
    }
  }

    if (!function_exists('getCopyrightYear')) {
      function getCopyrightYear()
      {
        $currentYear = date('Y');
        $copyrightYear = config('app.copyright.year');
        if($copyrightYear < $currentYear) {
          return $copyrightYear . ' - ' . $currentYear;
        }
        return $currentYear;
      }
    }
