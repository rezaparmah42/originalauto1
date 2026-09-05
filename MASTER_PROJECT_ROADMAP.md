# MASTER PROJECT ROADMAP

## Goal
Bring the project from a partially working multi-module app to a coherent, verifiable, production-ready platform with the highest-value services completed first.

## PHASE A — Critical fixes
Priority: P0

Required actions:
- secure the payment callback flow against forged GET status updates
- confirm customer auth/session ownership under a valid runtime proof
- clean debug/test artifact separation from production application root
- validate route and model ownership assumptions under full runtime checks

Dependencies:
- real backend payment contract or trusted gateway validation
- clean verification scripts
- repo hygiene cleanup

Tests required:
- payment callback signature or trusted verification proof
- customer auth regression script
- route integrity run

## PHASE B — Core completion
Priority: P1

Required actions:
- verify the full customer journey end-to-end
- finalize order creation and lifecycle state handling
- harden inventory stock lifecycle and receiving events
- prove admin dashboard and key module flows

Dependencies:
- stable payment and auth layers
- verified stock logic
- route consistency

Tests required:
- end-to-end customer scenario script
- admin workflow validation
- stock lifecycle verification

## PHASE C — Workshop
Priority: P1

Required actions:
- complete the workshop dashboard workflow and mechanic assignment flow
- verify repair lifecycle from intake to delivery
- connect repair parts, costs, payments, and delivery history together

Dependencies:
- stock deduction and inventory update logic
- repair status model integrity
- customer/vehicle ownership validation

Tests required:
- workshop full lifecycle verification
- repair part deduction verification
- payment/delivery status consistency checks

## PHASE D — Shop / Payment / Inventory
Priority: P1

Required actions:
- complete the cart/checkout flow with real proof
- formalize payment state transitions and reconciliation
- decide whether Excel import is required and implement it explicitly
- complete receiving and stock adjustment workflow

Dependencies:
- gateway or trusted payment contract
- inventory schema and product lifecycle decisions

Tests required:
- cart-to-order-to-payment proof
- inventory deduction and restock validation
- duplicate-order prevention validation

## PHASE E — Admin
Priority: P1/P2

Required actions:
- validate all admin controllers by route and data behavior
- close inconsistencies between admin modules and data models
- enforce consistent authorization checks in every admin route

Dependencies:
- route and auth hardening
- cleaned duplicate/legacy admin patterns

Tests required:
- admin CRUD acceptance checks per module
- auth bypass validation

## PHASE F — UX / Mobile
Priority: P2

Required actions:
- run browser-based UI testing for RTL and mobile layouts
- validate all critical forms and flows visually
- fix real UX defects, not only code-level defects

Dependencies:
- final core app stability
- working payment/auth flows

Tests required:
- browser smoke tests
- mobile layout validation

## PHASE G — SEO / Content
Priority: P2

Required actions:
- validate SEO metadata generation and canonical handling
- check robots, sitemap completeness, and page-level metadata
- remove placeholder or demo content from live-facing pages

Dependencies:
- content inventory and CMS validation
- UI stabilization

Tests required:
- page metadata checks
- sitemap and robots verification
- content/placeholder scanning

## PHASE H — AI / Knowledge / Diagnostic
Priority: P3

Required actions:
- decide whether these modules are real product features or future projects
- validate data source quality and workflow integration before promoting them as production features
- keep the J2534/ECU area separated from the core app until proven stable

Dependencies:
- core app security and data reliability
- product/repair workflow validation

Tests required:
- AI knowledge workflow validation
- diagnostic engine integration checks
- feature gating and product readiness review

## Recommended order
1. Payment trust and security
2. Customer auth proof
3. Inventory lifecycle and stock proof
4. Admin route/data validation
5. Workshop lifecycle completion
6. Shop checkout/payment proof
7. UX/mobile validation
8. SEO/content cleanup
9. AI and diagnostic feature gating

## Release gate
Do not declare production readiness until all of the following are true:
- customer auth proof is valid
- payment flow is secure and trusted
- inventory and order lifecycle are fully proven
- admin authorization and CRUD are tested
- core customer journey is proven end-to-end
- browser-based UX validation has passed for critical routes
- no hidden debug or legacy artifacts remain in active production paths
