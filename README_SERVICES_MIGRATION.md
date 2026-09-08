Services hub migration — README

What I changed
- Rebuilt `/services` hub to dual-mode (service-first + vehicle-first) and added image slots.
- Added content banks:
  - `app/Data/content_bank1.php` (service-first)
  - `app/Data/content_bank2.php` (vehicle-first)
- Added content generator helper: `app/Helpers/ContentGenerator.php`
- Pre-generated content:
  - Models: `app/Data/generated_models/...` (several key models)
  - Subservices: `app/Data/generated_subservices/...` (all services from bank1)
  - Matrices (sample): `app/Data/generated_matrices/...`
- Added sitemap & generator scripts:
  - `scripts/update_sitemap_services_vehicles.php`
  - `scripts/generate_model_contents.php`
- Lint helper: `scripts/run_php_lint.bat`

How to run locally (Windows/XAMPP)
1. Ensure PHP CLI is installed and in PATH (use XAMPP's php.exe or install PHP):
   - Example (PowerShell):
     ```powershell
     & "C:\\xampp\\php\\php.exe" -v
     ```
2. Generate model contents (if you want to pre-generate or re-generate):
   ```powershell
   php scripts/generate_model_contents.php
   ```
   Generated files will be placed under `app/Data/generated_models/`.
3. Generate sitemap:
   ```powershell
   php scripts/update_sitemap_services_vehicles.php
   ```
   Output: `public/sitemap-services-vehicles.xml`.
4. Run PHP lint across key files (Windows):
   ```powershell
   .\scripts\run_php_lint.bat
   ```

Notes
- I could not run PHP CLI in this automation environment, so some generation/lint steps were prepared but not executed here. I pre-generated key files in the repo to ensure immediate availability.
- Image files are still placeholders: ensure to upload hero and mid images to `public/uploads/services/{service-slug}/` and `public/uploads/vehicles/{brand}/{model}.jpg` and `-mid.jpg`.

Contact
- If you want me to pre-generate matrices for all service+model combinations, I'll proceed and commit them (this will create multiple files). Currently I pre-generated samples for key pairs.
