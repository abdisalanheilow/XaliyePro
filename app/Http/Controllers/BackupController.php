<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    private $backupDir = 'backups';

    public function index(): View
    {
        $path = storage_path('app/'.$this->backupDir);
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $files = File::files($path);
        usort($files, function ($a, $b) {
            return $b->getMTime() - $a->getMTime();
        });

        $backups = [];
        $totalSize = 0;

        foreach ($files as $file) {
            $name = $file->getFilename();
            $size = $file->getSize();
            $totalSize += $size;
            $type = 'Full Backup';
            if (str_contains($name, 'DB_Only')) {
                $type = 'Database Only';
            } elseif (str_contains($name, 'Files_Only')) {
                $type = 'Files Only';
            }

            $backups[] = [
                'name' => $name,
                'path' => $file->getPathname(),
                'type' => $type,
                'size' => number_format($size / 1048576, 2).' MB',
                'date' => date('M d, Y', $file->getMTime()),
                'time' => date('h:i A', $file->getMTime()),
                'status' => 'Success',
            ];
        }

        $totalSizeMB = round($totalSize / 1048576, 2);

        $scheduleFile = storage_path('app/backup_settings.json');
        $schedule = File::exists($scheduleFile) ? json_decode(File::get($scheduleFile), true) : [
            'status' => 'inactive',
            'frequency' => 'daily',
            'retention' => 'keep-30',
        ];

        return view('admin.settings.backup_restore', compact('backups', 'totalSizeMB', 'schedule'));
    }

    public function saveSchedule(Request $request): RedirectResponse
    {
        $status = $request->has('enable_auto_backup') ? 'active' : 'inactive';
        $frequency = $request->frequency ?? 'daily';
        $retention = $request->retention ?? 'keep-30';

        $settings = [
            'status' => $status,
            'frequency' => $frequency,
            'retention' => $retention,
        ];

        File::put(storage_path('app/backup_settings.json'), json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->back()->with([
            'message' => 'Automatic Backup Schedule updated successfully!',
            'title' => 'Schedule Updated',
            'alert-type' => 'success',
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $type = 'database'; // Forced to database as per requirements
        $compression = $request->has('compression');
        $name = $request->name ?? 'XaliyePro_Backup_'.date('Y-m-d_H-i-s');
        $path = storage_path('app/'.$this->backupDir);
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        try {
            if ($type == 'full') {
                $name .= '_Full.zip';
                $this->createFullBackup($name, $compression);
            } elseif ($type == 'database') {
                $name .= '_DB_Only.sql';
                if ($compression) {
                    $name .= '.zip';
                }
                $this->createDatabaseBackup($name, $compression);
            } elseif ($type == 'files') {
                $name .= '_Files_Only.zip';
                $this->createFilesBackup($name, $compression);
            }

            return redirect()->back()->with([
                'message' => 'Backup created successfully!',
                'title' => 'Backup Created',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Backup failed: '.$e->getMessage(),
                'title' => 'Backup Failed',
                'alert-type' => 'error',
            ]);
        }
    }

    private function createDatabaseBackup($filename, $compression)
    {
        $sql = "-- XaliyePro Database Backup\n";
        $sql .= '-- Generated: '.date('Y-m-d H:i:s')."\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        $key = 'Tables_in_'.$dbName;

        // Handle variations in array key name depending on MySQL settings
        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = array_values($tableArray)[0];

            $createStmt = DB::select("SHOW CREATE TABLE `$tableName`");
            if (empty($createStmt)) {
                continue;
            }

            $createArray = (array) $createStmt[0];
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= $createArray['Create Table'].";\n\n";

            // Get Rows
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $values = [];
                foreach ($rowArray as $value) {
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'".addslashes($value)."'";
                    }
                }
                $sql .= "INSERT INTO `$tableName` VALUES (".implode(', ', $values).");\n";
            }
            $sql .= "\n\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        if ($compression) {
            $zipPath = storage_path('app/'.$this->backupDir.'/'.str_replace('.sql', '', $filename));
            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFromString('database.sql', $sql);
                $zip->close();
            } else {
                throw new \Exception('Could not create ZIP file.');
            }
        } else {
            Storage::put($this->backupDir.'/'.$filename, $sql);
        }
    }

    private function createFilesBackup($filename, $compression)
    {
        $zipPath = storage_path('app/'.$this->backupDir.'/'.$filename);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create ZIP file.');
        }

        // Add public upload folders
        $uploadDir = public_path('upload');
        if (File::exists($uploadDir)) {
            $files = File::allFiles($uploadDir);
            foreach ($files as $file) {
                $relativePath = 'public/upload/'.$file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        // Add storage public folders
        $storageDir = storage_path('app/public');
        if (File::exists($storageDir)) {
            $files = File::allFiles($storageDir);
            foreach ($files as $file) {
                $relativePath = 'storage/app/public/'.$file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();
    }

    private function createFullBackup($filename, $compression)
    {
        // 1. Generate temp database backup
        $tempSql = 'database_temp.sql';
        $this->createDatabaseBackup($tempSql, false);

        // 2. Zip everything together
        $zipPath = storage_path('app/'.$this->backupDir.'/'.$filename);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('Could not create ZIP file.');
        }

        // Add database sql
        $sqlContent = Storage::get($this->backupDir.'/'.$tempSql);
        $zip->addFromString('database.sql', $sqlContent);

        // Add public upload folders
        $uploadDir = public_path('upload');
        if (File::exists($uploadDir)) {
            $files = File::allFiles($uploadDir);
            foreach ($files as $file) {
                $relativePath = 'upload/'.$file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();

        // 3. Clean up temp sql file
        Storage::delete($this->backupDir.'/'.$tempSql);
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip,sql',
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        try {
            $file = $request->file('backup_file');
            $extension = $file->getClientOriginalExtension();

            if ($extension == 'sql') {
                $this->restoreDatabaseFile($file->getRealPath());
            } elseif ($extension == 'zip') {
                $zip = new ZipArchive;
                if ($zip->open($file->getRealPath()) === true) {
                    $extractPath = storage_path('app/temp_restore');
                    if (! File::exists($extractPath)) {
                        File::makeDirectory($extractPath);
                    }
                    $zip->extractTo($extractPath);
                    $zip->close();

                    // Check for database.sql
                    if (File::exists($extractPath.'/database.sql')) {
                        $this->restoreDatabaseFile($extractPath.'/database.sql');
                    }

                    // Copy files to upload directly
                    if (File::exists($extractPath.'/upload')) {
                        File::copyDirectory($extractPath.'/upload', public_path('upload'));
                    }

                    File::deleteDirectory($extractPath);
                }
            }

            return redirect()->back()->with([
                'message' => 'System restored successfully!',
                'title' => 'Restore Complete',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Restore failed: '.$e->getMessage(),
                'title' => 'Restore Failed',
                'alert-type' => 'error',
            ]);
        }
    }

    private function restoreDatabaseFile($path)
    {
        $sql = file_get_contents($path);
        if (empty($sql)) {
            throw new \Exception('SQL file is empty.');
        }

        // This execution splits the sql commands and executes them. It's safe but requires splitting by semicolon which might be tricky if data contains semicolon.
        // A safer robust approach using PDO directly
        DB::unprepared($sql);
    }

    public function download($filename): BinaryFileResponse|RedirectResponse
    {
        $path = storage_path('app/'.$this->backupDir.'/'.$filename);
        if (File::exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with(['message' => 'File not found.', 'alert-type' => 'error']);
    }

    public function destroy($filename): RedirectResponse
    {
        $path = storage_path('app/'.$this->backupDir.'/'.$filename);
        if (File::exists($path)) {
            File::delete($path);

            return redirect()->back()->with([
                'message' => 'Backup deleted successfully!',
                'title' => 'Backup Deleted',
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back()->with(['message' => 'File not found.', 'alert-type' => 'error']);
    }
}
