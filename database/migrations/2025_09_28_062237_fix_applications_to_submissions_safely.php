<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        try {
            // Disable foreign key checks for this migration
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Step 1: Check if applications table exists and rename to submissions
            if (Schema::hasTable('applications') && !Schema::hasTable('submissions')) {
                Schema::rename('applications', 'submissions');
                echo "✅ Renamed applications table to submissions\n";
            }

            // Step 2: Update submissions table structure
            if (Schema::hasTable('submissions')) {
                Schema::table('submissions', function (Blueprint $table) {
                    // Add submission_number if it doesn't exist
                    if (!Schema::hasColumn('submissions', 'submission_number')) {
                        $table->string('submission_number')->nullable()->unique()->after('id');
                    }

                    // Remove application_number if it exists
                    if (Schema::hasColumn('submissions', 'application_number')) {
                        $table->dropColumn('application_number');
                    }
                });
                echo "✅ Updated submissions table structure\n";
            }

            // Step 3: Handle application_histories table
            if (Schema::hasTable('application_histories')) {
                // Rename table
                Schema::rename('application_histories', 'submission_histories');
                echo "✅ Renamed application_histories to submission_histories\n";

                // Update foreign key column
                Schema::table('submission_histories', function (Blueprint $table) {
                    if (Schema::hasColumn('submission_histories', 'application_id')) {
                        // Drop existing foreign key if exists
                        try {
                            $table->dropForeign(['application_id']);
                        } catch (\Exception $e) {
                            // Ignore if foreign key doesn't exist
                        }

                        // Rename column
                        $table->renameColumn('application_id', 'submission_id');
                    }
                });
                echo "✅ Updated submission_histories foreign key\n";
            }

            // Step 4: Handle payments table
            if (Schema::hasTable('payments')) {
                Schema::table('payments', function (Blueprint $table) {
                    if (Schema::hasColumn('payments', 'application_id')) {
                        // Drop existing foreign key if exists
                        try {
                            $table->dropForeign(['application_id']);
                        } catch (\Exception $e) {
                            // Ignore if foreign key doesn't exist
                        }

                        // Rename column
                        $table->renameColumn('application_id', 'submission_id');
                    }
                });
                echo "✅ Updated payments foreign key\n";
            }

            // Step 5: Handle generated_documents table
            if (Schema::hasTable('generated_documents')) {
                Schema::table('generated_documents', function (Blueprint $table) {
                    if (Schema::hasColumn('generated_documents', 'application_id')) {
                        // Drop existing foreign key if exists
                        try {
                            $table->dropForeign(['application_id']);
                        } catch (\Exception $e) {
                            // Ignore if foreign key doesn't exist
                        }

                        // Rename column
                        $table->renameColumn('application_id', 'submission_id');
                    }
                });
                echo "✅ Updated generated_documents foreign key\n";
            }

            // Step 6: Re-add foreign key constraints (safer approach)
            if (Schema::hasTable('submissions') && Schema::hasTable('payments')) {
                if (Schema::hasColumn('payments', 'submission_id')) {
                    Schema::table('payments', function (Blueprint $table) {
                        try {
                            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
                        } catch (\Exception $e) {
                            echo "⚠️ Could not add payments foreign key: " . $e->getMessage() . "\n";
                        }
                    });
                }
            }

            if (Schema::hasTable('submissions') && Schema::hasTable('generated_documents')) {
                if (Schema::hasColumn('generated_documents', 'submission_id')) {
                    Schema::table('generated_documents', function (Blueprint $table) {
                        try {
                            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
                        } catch (\Exception $e) {
                            echo "⚠️ Could not add generated_documents foreign key: " . $e->getMessage() . "\n";
                        }
                    });
                }
            }

            if (Schema::hasTable('submissions') && Schema::hasTable('submission_histories')) {
                if (Schema::hasColumn('submission_histories', 'submission_id')) {
                    Schema::table('submission_histories', function (Blueprint $table) {
                        try {
                            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
                        } catch (\Exception $e) {
                            echo "⚠️ Could not add submission_histories foreign key: " . $e->getMessage() . "\n";
                        }
                    });
                }
            }

            // Step 7: Update existing data
            if (Schema::hasTable('submissions')) {
                // Update submission_number for existing records
                $submissions = DB::table('submissions')->whereNull('submission_number')->get();
                foreach ($submissions as $submission) {
                    $submissionNumber = 'BMKG-SUR-' . date('md', strtotime($submission->created_at)) . '-' . date('Y', strtotime($submission->created_at)) . '-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT);
                    DB::table('submissions')->where('id', $submission->id)->update(['submission_number' => $submissionNumber]);
                }
                echo "✅ Updated submission numbers for existing records\n";
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            echo "🎉 Migration completed successfully!\n";
        } catch (\Exception $e) {
            // Re-enable foreign key checks on error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw $e;
        }
    }

    public function down()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Reverse all changes
            if (Schema::hasTable('submission_histories')) {
                Schema::rename('submission_histories', 'application_histories');

                Schema::table('application_histories', function (Blueprint $table) {
                    if (Schema::hasColumn('application_histories', 'submission_id')) {
                        try {
                            $table->dropForeign(['submission_id']);
                        } catch (\Exception $e) {
                            // Ignore
                        }
                        $table->renameColumn('submission_id', 'application_id');
                    }
                });
            }

            if (Schema::hasTable('generated_documents') && Schema::hasColumn('generated_documents', 'submission_id')) {
                Schema::table('generated_documents', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['submission_id']);
                    } catch (\Exception $e) {
                        // Ignore
                    }
                    $table->renameColumn('submission_id', 'application_id');
                });
            }

            if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'submission_id')) {
                Schema::table('payments', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['submission_id']);
                    } catch (\Exception $e) {
                        // Ignore
                    }
                    $table->renameColumn('submission_id', 'application_id');
                });
            }

            if (Schema::hasTable('submissions')) {
                Schema::table('submissions', function (Blueprint $table) {
                    if (Schema::hasColumn('submissions', 'submission_number')) {
                        $table->dropColumn('submission_number');
                    }
                    if (!Schema::hasColumn('submissions', 'application_number')) {
                        $table->string('application_number')->nullable()->unique()->after('id');
                    }
                });

                Schema::rename('submissions', 'applications');
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw $e;
        }
    }
};
