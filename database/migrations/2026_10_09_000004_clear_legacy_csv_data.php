<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $criteria = DB::table('criteria')->orderBy('code')->get(['id']);
        $criterionCount = $criteria->count();

        if ($criterionCount > 0) {
            $weightUnits = intdiv(10000, $criterionCount);
            $remainder = 10000 % $criterionCount;

            foreach ($criteria as $index => $criterion) {
                DB::table('criteria')
                    ->where('id', $criterion->id)
                    ->update([
                        'weight' => ($weightUnits + ($index < $remainder ? 1 : 0)) / 10000,
                        'updated_at' => now(),
                    ]);
            }
        }

        DB::table('respondents')
            ->where('general_feedback', 'like', 'Di-import dari file CSV:%')
            ->delete();

        DB::table('calculation_results')->delete();
    }

    public function down(): void
    {
        throw new RuntimeException('Legacy CSV data was deleted. Restore the pre-migration database backup to recover it.');
    }
};
