## 2026-02-09 - [Optimizing Item Listing and Catalog Performance]
**Learning:** Found N+1 query patterns in Item catalog and listing views due to missing eager loading of the 'category' relationship. Additionally, frequent sorting by 'nama_barang' without a database index was an efficiency bottleneck.
**Action:** Always check views for relationship access and ensure they are eager-loaded in the controller. Add database indexes on columns used frequently for sorting or filtering to improve query execution time.
