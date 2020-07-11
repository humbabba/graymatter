<?php

  if (!function_exists('getVersion')) {
    function getVersion()
    {
      switch(config('app.env')) {
        case 'production':
          return config('app.version');
          break;
        default:
          return time();
          break;
      }
    }
  }
