# Testing Workflow for User Submission to Admin Archiving

## Current Status

-   [x] Fix document upload validation mismatch (frontend JS and backend controller).

## Testing Steps

1. Start local server: Run `php artisan serve` to host the app at http://127.0.0.1:8000.
2. User Flow:
    - Launch browser to http://127.0.0.1:8000/login.
    - Login as user (use seeded user, e.g., email: user@example.com, password: password).
    - Navigate to dashboard, select a guideline (e.g., one requiring 2 docs like "Surat Permohonan" and "Surat Tugas").
    - Fill form: Purpose, dates, upload exact number of files matching required docs.
    - Submit and verify success (no error, redirect to history, new submission appears).
3. Admin Flow:
    - Close browser, relaunch, login as admin (e.g., email: admin@example.com, password: password).
    - Go to admin dashboard, find the new submission (pending).
    - Verify: Update status to 'verified', add history.
    - If PNBP: Handle payment pending -> user uploads proof (simulate or note), admin approves to 'paid'.
    - Process: Update to 'processing', generate document if needed.
    - Complete: Update to 'completed', generate archive entry.
    - Archive: Move to archive table/model.
4. Verify End-to-End:
    - User sees completed status, can download document.
    - Admin sees archived item.
    - No errors in console/logs; fix any via code edits.
5. Cleanup: Close browser, stop server if needed.

## Pending

-   [ ] Execute server start.
-   [ ] Browser testing (user submission).
-   [ ] Browser testing (admin processing).
-   [ ] Fix any errors found.
-   [ ] Confirm full workflow complete.
