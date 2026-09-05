# Patch 6.5 Report

## Files changed
- app/Models/Vehicle.php
- app/Controllers/VehicleController.php
- app/Controllers/AccountController.php
- app/routes.php
- app/Views/account/dashboard.php
- app/Views/account/vehicles/index.php
- app/Views/account/vehicles/create.php
- app/Views/account/vehicles/edit.php
- app/Views/account/vehicles/history.php
- app/Views/admin/vehicles/index.php
- app/Views/admin/vehicles/show.php

## Features added
- Customer vehicle list, create, edit, delete, and repair-history pages.
- Vehicle ownership checks so customers can only access their own vehicles.
- Admin vehicle listing and detail view with customer and repair-history context.
- Customer dashboard cards for vehicles, repair history, and upcoming maintenance.
- PDO-based vehicle model methods for listing, retrieving, creating, updating, deleting, and history lookups.

## Validation result
- Ran PHP syntax checks for the vehicle controller, vehicle model, account controller, all vehicle views, and routes.
- Result: No syntax errors detected in all targeted files.

## Remaining issues
- The implementation uses the existing schema and current tables; no database schema changes were made.
- Repair history is derived from available bookings and repairs data already stored in the database.
