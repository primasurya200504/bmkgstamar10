# TODO: Add "Kategori Data" PNBP/Non-PNBP to Admin Panel

## Information Gathered
- Guidelines model has 'type' field with enum('pnbp', 'non_pnbp')
- Submissions are linked to guidelines via guideline_id, so category inherits from guideline->type
- Payments are linked to submissions, so category is submission->guideline->type
- Data-uploads view shows submissions, so category from submission->guideline->type
- Current views:
  - guidelines/index.blade.php: Already displays "Tipe" column
  - submissions/show.blade.php: Already displays "Tipe" from guideline
  - Other index views (submissions, payments, data-uploads) do not display category

## Plan
- Add "Kategori Data" column to submissions/index.blade.php table
- Add "Kategori Data" column to payments/index.blade.php table
- Add "Kategori Data" column to data-uploads/index.blade.php table
- No database changes needed as data exists via relationships

## Dependent Files to Edit
- resources/views/admin/submissions/index.blade.php
- resources/views/admin/payments/index.blade.php
- resources/views/admin/data-uploads/index.blade.php

## Followup Steps
- Verify category display in all modified views
- Test navigation and ensure no errors
- Check if all data has proper guideline relationships
