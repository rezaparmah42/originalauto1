@echo off
REM Lint PHP files modified by this migration. Requires PHP in PATH.
set ROOT=%~dp0..\
set PHP=php
if not exist %PHP% (
  echo PHP CLI not found in PATH. Please install PHP and ensure 'php' is available.
  exit /b 1
)

echo Running php -l on key files...
%PHP% -l %ROOT%app\Views\services\show.php
%PHP% -l %ROOT%app\Views\services\subservice.php
%PHP% -l %ROOT%app\Views\services\matrix.php
%PHP% -l %ROOT%app\Views\vehicles\detail.php
%PHP% -l %ROOT%app\Helpers\ContentGenerator.php
%PHP% -l %ROOT%scripts\update_sitemap_services_vehicles.php
%PHP% -l %ROOT%scripts\generate_model_contents.php

echo Lint completed. Review output for errors.
