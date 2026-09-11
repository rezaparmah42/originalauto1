# Vehicle Knowledge Import Pipeline

This repository contains the code-only import pipeline for vehicle encyclopedia content. The pipeline is designed to operate in a safe, staged workflow without touching production data.

## Scope

The architecture remains:

- Canonical tables: vehicle_brands, vehicle_models, vehicle_model_service, services, vehicle_common_problems, vehicle_symptoms, vehicle_dtc_codes, vehicle_diagnostics, vehicle_ecu_info, vehicle_maintenance_tasks, vehicle_repair_solutions, vehicle_model_faqs, articles
- Draft/source layer: knowledge_source_documents, vehicle_knowledge_contents

No migration is executed in this phase. No SQL is run against the live database. No content is published directly.

## Pipeline stages

1. PDF discovery
2. PDF classification and attribute extraction
3. Brand/model/service mapping against canonical tables only
4. Draft creation in the editorial layer
5. Validation for slug, duplicates, canonical relationships, and SEO completeness
6. Approval-only workflow in code or admin tooling
7. Publication only after validation passes

## Supported URL patterns

- /vehicles/{brand}/{model}
- /vehicles/{brand}/{model}/{knowledge-topic}
- /knowledge/{brand}/{model}
- /knowledge/{brand}/{model}/{topic}

## Import process

The repository includes:

- app/Services/VehicleKnowledgeImportPlanner.php
- scripts/vehicle_knowledge_import_plan.php
- scripts/import_vehicle_knowledge.php

The planner scans ai_sources and classifies each PDF by:

- brand
- model
- generation
- engine
- system/service category
- content type
- knowledge category

## Safe execution policy

- Default script mode is dry-run / code-only analysis.
- No database writes occur unless an explicit execution flag is used.
- This repository does not execute SQL or modify the production database.

## Current repository state

The ai_sources directory is prepared for future PDF intake. No actual PDF files are currently tracked in the repository; therefore, this phase produces a mapping and readiness report without importing content.

## Ready-to-run handoff

When PDF files are placed into ai_sources, the planner can generate a candidate mapping report and an import plan that is suitable for review before approval and publication.
