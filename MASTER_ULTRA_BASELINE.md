# MASTER ULTRA BASELINE

## Scope
This baseline is based on the live filesystem snapshot, live XAMPP stack, and real database state available in this environment.

## Hard facts
- Git repository: not present in this workspace snapshot (`git status` = not a git repository)
- PHP version: 8.2.12
- MariaDB/MySQL version: 10.4.32-MariaDB
- Apache/XAMPP service: present in local environment, but browser automation was not fully available in this session
- DB connection: live MySQL connection is available
- DB name: original_east
- table count: 46
- PDO: available
- required PHP extensions present: PDO, mysqli, mysqlnd, openssl, session, curl, mbstring

## Real project structure
- app/
- app/Core/
- app/Controllers/
- app/Models/
- app/Views/
- app/functions/
- config/
- includes/
- database/
- admin/
- api/
- public/
- tools/
- root-level verifier and patch documentation files

## Working features with evidence
- garage/customer vehicle flow
- workshop flow
- compatibility logic
- inventory stock deduction logic
- callback token verification after fix

## Risks and inconsistencies
- no valid Git baseline
- duplicate/legacy architecture exists alongside active implementation
- schema drift between live DB and historical SQL files is likely
- full admin/authorization matrix not proven
- browser validation not proven
- real external gateway integration is not implemented

## Conclusion
This is a real, active project with validated core flows, but it is not a clean release candidate. The release gap is in full security/authorization, browser validation, deployment hygiene, and broad end-to-end proof.
