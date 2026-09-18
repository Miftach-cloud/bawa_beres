---
paths:
  - 'app/{Actions,Console,Http,Models}/**/*.php,routes/web.php'
---

# Actions Console Http Models

## Operational media is private-only
Payment proofs, inventory photos, and order attachments must be stored and served only from the local private disk through authorized routes. Do not restore public-disk runtime fallbacks. Use media:migrate-legacy-private to copy and verify legacy public files before optionally deleting their sources.
