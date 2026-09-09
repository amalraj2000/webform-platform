# Multi-Tenant Webform Platform Architecture

## Component Architecture Flow

```text
    [ Anonymous User ]                [ Company Admin ]                 [ Super Admin ]
            |                                |                                 |
     (GET /forms/uuid)                 (GET /dashboard)            (GET /superadmin/dashboard)
            |                                |                                 |
            v                                v                                 v
    +-----------------------------------------------------------------------------------+
    |                           Laravel Application (Sail / PHP)                        |
    |                                                                                   |
    |   +-------------------+    +-----------------------+    +---------------------+   |
    |   | PublicController  |    | Admin\FormController  |    | SuperAdminController|   |
    |   +-------------------+    +-----------------------+    +---------------------+   |
    |             |                          |                           |              |
    |    (POST /api/submissions)     (Schema Builder / Export)           |              |
    |             |                          |                           |              |
    |   +-------------------+                |                           |              |
    |   | Rate Limiter (Redis)               |                           |              |
    |   +-------------------+                |                           |              |
    |             |                          |                           |              |
    |   +-------------------+                |                           |              |
    |   | DynamicValidator  |                |                           |              |
    |   +-------------------+                |                           |              |
    |             |                          v                           v              |
    |    (202 Accepted)          +--------------------------------------------------+   |
    |             |              |                    MySQL (8.0)                   |   |
    |             +------------> |                                                  |   |
    |                            |  [accounts] [users] [forms] [form_versions]      |   |
    |   +-------------------+    |                                                  |   |
    |   | Redis Queue       |    |  [submissions] (id, version_id, data JSON)       |   |
    |   +-------------------+    +--------------------------------------------------+   |
    |             |                                   ^                                 |
    |             v                                   |                                 |
    |   +-------------------+                         |                                 |
    |   | Queue Worker      |-------------------------+                                 |
    |   | (Async Persistence|                                                           |
    |   +-------------------+                                                           |
    +-----------------------------------------------------------------------------------+
```

## Deep Dive: High, Bursty Load Handling
The system handles massive, concurrent submission bursts (e.g. 10,000+ submissions per minute) using a Queue Buffer Pipeline:
1. **In-Memory Validation**: Instead of querying the database on every submission to fetch the schema, validation relies on caching the schema (or quick indexed reads) and parsing it purely in PHP memory.
2. **HTTP 202 Accepted**: As soon as validation passes, the raw payload is dispatched to Redis. The HTTP connection is closed immediately with a `202 Accepted` response.
3. **Queue Buffering**: Redis absorbs the write spikes at microsecond latency.
4. **Worker Retry Strategy**: Background workers pull from Redis and write to MySQL. If MySQL is overloaded or deadlocks occur, Laravel's queue workers implement automatic exponential backoff and retry mechanisms without dropping data or failing the end-user request.

## Data Model Justification
**Why MySQL Native JSON?**
Storing dynamic form data is traditionally handled by Entity-Attribute-Value (EAV) patterns (where every field is a row in an `attributes` table) or NoSQL (like MongoDB).
- **Against EAV**: EAV tables scale horribly under heavy concurrent writes due to index bloat and complex Joins required for reporting.
- **Against MongoDB**: Introducing MongoDB adds severe operational overhead. We would lose ACID compliance for relational data (Accounts, Forms).
- **The Choice (MySQL JSON)**: MySQL 8.0 offers native `JSON` columns. We get the schema flexibility of MongoDB combined with the relational integrity of MySQL. We store arbitrary user submissions seamlessly in a single row (`INSERT INTO submissions (data) VALUES ('{"name": "...", "age": 22}')`).

## Security & Isolation
- **Tenant Isolation**: All queries in the Admin dashboard are strictly scoped to `auth()->user()->account_id`.
- **Rate Limiting**: `SubmitFormController` limits submissions to 60 per minute per IP to prevent spam and DDoS.
- **XSS Sanitization**: `DynamicFormValidator` passes all text string inputs through native `strip_tags` to prevent basic cross-site scripting payload ingestion before they hit the database.

## "Built vs. Designed"
While this working slice accurately demonstrates the architectural paradigm, an enterprise production deployment would expand on this:
- **Built**: Redis queue processor running locally.
- **Designed**: Dedicated auto-scaling Kubernetes worker pods isolated per tenant (premium tenants get priority queue lanes).
- **Built**: Single MySQL Database.
- **Designed**: Read-replicas for CSV exporting to prevent dashboard queries from impacting ingestion speed.
- **Built**: Direct CSV generation using PHP streams.
- **Designed**: S3-backed asynchronous CSV generation where the user receives an email with a secure presigned download link.
