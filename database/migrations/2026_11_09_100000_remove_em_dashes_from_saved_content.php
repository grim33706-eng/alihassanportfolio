<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Takes the long dash out of content already saved in the admin, which
 * overrides the defaults in config/. Skills are left alone: there the dash
 * only separates a name from its detail line and is never displayed.
 */
return new class extends Migration
{
    private const SKIP = ['portfolio.skills', 'admin.password_hash'];

    public function up(): void
    {
        DB::table('site_settings')->whereNotIn('key', self::SKIP)->get()->each(function ($row) {
            $value = json_decode($row->value, true);
            $clean = $this->clean($value);
            if ($clean !== $value) {
                DB::table('site_settings')->where('id', $row->id)->update([
                    'value' => json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });

        $this->cleanColumns('case_studies', ['title', 'brand', 'industry', 'strategy', 'improvements']);
        $this->cleanColumns('case_study_images', ['caption']);
    }

    public function down(): void
    {
        // The original punctuation is not kept.
    }

    private function cleanColumns(string $table, array $columns): void
    {
        DB::table($table)->get()->each(function ($row) use ($table, $columns) {
            $changes = [];
            foreach ($columns as $column) {
                if (is_string($row->$column) && ($text = $this->text($row->$column)) !== $row->$column) {
                    $changes[$column] = $text;
                }
            }
            if ($changes) {
                DB::table($table)->where('id', $row->id)->update($changes);
            }
        });
    }

    private function clean(mixed $value, int|string|null $key = null): mixed
    {
        if (is_array($value)) {
            foreach ($value as $k => $item) {
                $value[$k] = $this->clean($item, $k);
            }

            return $value;
        }

        if (! is_string($value)) {
            return $value;
        }

        return $key === 'seo_title' ? str_replace(' — ', ' | ', $value) : $this->text($value);
    }

    /** A comma suits most sentences; these are the ones that read better another way. */
    private function text(string $text): string
    {
        $rules = [
            '/ — (The Social Links|NextQuick Solutions|Dynasty Marketing Agency|Orbit Creator)\./u' => ' at $1.',
            '/\bYes — /u' => 'Yes. ',
            '/\) — fashion/u' => '): fashion',
            '/main goal — /u' => 'main goal: ',
            '/performance loop — /u' => 'performance loop: ',
            '/ — (campaign priorities|content strategy|setup,|paid media specialists|targeting,)/u' => ': $1',
            '/ — B2B & B2C/u' => ' (B2B & B2C)',
            '/\s*—\s*/u' => ', ',
        ];

        return preg_replace(array_keys($rules), array_values($rules), $text);
    }
};
