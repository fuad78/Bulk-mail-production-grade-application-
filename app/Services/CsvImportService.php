<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvImportService
{
    public function import(Campaign $campaign, string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        // Normalize headers
        $header = array_map('strtolower', $header);
        $this->validateHeader($header);

        $chunkSize = 1000;
        $chunk = [];
        $emails = []; // For local deduplication within file

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) !== count($header))
                    continue;

                $data = array_combine($header, $row);
                $email = strtolower(trim($data['email']));

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue; // Skip invalid emails or log them
                }

                if (in_array($email, $emails)) {
                    continue; // Duplicate in file
                }

                $emails[] = $email;

                // Check database duplicate for this campaign
                if (Recipient::where('campaign_id', $campaign->id)->where('email', $email)->exists()) {
                    continue;
                }

                $chunk[] = [
                    'campaign_id' => $campaign->id,
                    'email' => $email,
                    'name' => $data['name'] ?? null,
                    'metadata' => json_encode($this->extractMetadata($data)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($chunk) >= $chunkSize) {
                    Recipient::insert($chunk);
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                Recipient::insert($chunk);
            }

            DB::commit();
            fclose($file);

            return count($emails);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            throw $e;
        }
    }

    public function importToList(\App\Models\ContactList $list, string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        // Normalize headers
        $header = array_map('strtolower', $header);
        $this->validateHeader($header);

        $chunkSize = 1000;
        $chunk = [];
        $emails = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) !== count($header))
                    continue;

                $data = array_combine($header, $row);
                $email = strtolower(trim($data['email']));

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                if (in_array($email, $emails)) {
                    continue;
                }

                $emails[] = $email;

                // Check database duplicate for this list
                if (\App\Models\Contact::where('contact_list_id', $list->id)->where('email', $email)->exists()) {
                    continue;
                }

                $chunk[] = [
                    'contact_list_id' => $list->id,
                    'email' => $email,
                    'name' => $data['name'] ?? null,
                    'metadata' => json_encode($this->extractMetadata($data)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($chunk) >= $chunkSize) {
                    \App\Models\Contact::insert($chunk);
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                \App\Models\Contact::insert($chunk);
            }

            DB::commit();
            fclose($file);

            return count($emails);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            throw $e;
        }
    }

    private function validateHeader(array $header)
    {
        if (!in_array('email', $header)) {
            throw ValidationException::withMessages(['file' => 'CSV must contain an "email" column']);
        }
    }

    private function extractMetadata(array $row)
    {
        $exclude = ['email', 'name'];
        return array_diff_key($row, array_flip($exclude));
    }
}
