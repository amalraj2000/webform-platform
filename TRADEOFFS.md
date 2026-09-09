# Architectural Tradeoffs

## 1. Asynchronous Ingestion (Queue / HTTP 202) vs. Synchronous DB Writes (HTTP 200)
- **Decision**: Implemented an async ingestion pipeline using Redis queues and returning HTTP 202 Accepted.
- **Alternative Rejected**: Direct synchronous writes (HTTP 200) where the submission is validated and written to MySQL in the same HTTP request lifecycle.
- **Reasoning**: Synchronous DB writes become a major bottleneck under high concurrent load. A sudden burst (e.g. 5,000 users submitting a form at a conference simultaneously) will rapidly exhaust database connections and lead to 503 timeouts. The tradeoff is eventual consistency—the user receives a success message slightly before the data is actually persisted, and any fatal database error must be handled out-of-band by the worker retry logic.

## 2. MySQL Native JSON Storage vs. Entity-Attribute-Value (EAV) vs. MongoDB
- **Decision**: Utilized a single MySQL 8.0 `JSON` column to store dynamic submission data.
- **Alternative Rejected (EAV)**: Creating tables like `fields`, `values`, and linking them. This scales poorly, causes index bloat, and makes data retrieval incredibly complex (requiring multiple JOINs).
- **Alternative Rejected (MongoDB)**: Spinning up a dedicated NoSQL cluster. This fragments the architecture and drastically increases DevOps maintenance cost for a SaaS.
- **Reasoning**: MySQL 8.0's JSON columns provide the "best of both worlds". It allows us to keep our transactional relationships (Accounts -> Users -> Forms) intact while offering schema-less persistence for unpredictable user input. The tradeoff is slightly slower indexing capabilities for extremely complex JSON queries compared to MongoDB, but for this domain (primarily bulk ingestion and flat exporting), it's highly optimal.

## 3. Pre-compiled Cached Schema Rules vs. On-the-fly Dynamic Validation
- **Decision**: On-the-fly dynamic validation (`DynamicFormValidator` parses the schema JSON and builds the Validator rules array for every incoming request).
- **Alternative Rejected**: Compiling the schema into a static PHP rules array and caching it in Redis when the form is published.
- **Reasoning**: Caching the rules would shave off a few milliseconds of CPU time per request. However, the cost of iterating over a 10-20 item JSON array in memory is negligible in PHP 8.x. Implementing caching would introduce cache invalidation complexity and race conditions during form updates. We traded micro-optimizations for architectural simplicity and bulletproof reliability.
