<?php

namespace App\Http\Controllers;

use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Service\Drive\DriveFile;

class GoogleDriveController extends Controller
{
    protected $drive;

    public function __construct(Drive $drive)
    {
        $this->drive = $drive;
    }

    public function listFiles()
    {
        $files = $this->drive->files->listFiles();
        return response()->json($files);
    }
    public function showFilesAndFolders()
    {
        // Dapatkan ID Folder dari environment variable
        $folderId = env('GOOGLE_DRIVE_FOLDER_ID');

        // Dapatkan informasi file dan folder
        $filesAndFolders = $this->drive->files->listFiles([
            'q' => "'$folderId' in parents",
        ]);

        // Kembalikan view dengan data file dan folder
        return view('google-drive.files-and-folders', compact('filesAndFolders'));
    }
    public function createFile(Request $request)
    {
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $request->input('name'),
            'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')]
        ]);
        $content = file_get_contents($request->file('file')->path());
        $file = $this->drive->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $request->file('file')->getMimeType(),
            'uploadType' => 'multipart'
        ]);

        return response()->json($file);
    }


    public function upload(Request $request)
    {
        // Ambil file dari request
        $file = $request->file('file');

        // Simpan file ke penyimpanan sementara
        $filePath = $file->storeAs('/', $file->getClientOriginalName());

        // Unggah file ke Google Drive
        $driveFileMetadata = new DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
        ]);

        $driveFile = $this->drive->files->create($driveFileMetadata, [
            'data' => file_get_contents(Storage::path($filePath)),
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
        ]);

        // Hapus file sementara setelah diunggah
        Storage::delete($filePath);

        // Redirect kembali ke halaman upload dengan pesan sukses
        return redirect()->route('google-drive.upload')->with('success', 'File uploaded successfully!');
    }

    public function delete($fileId)
    {
        try {
            // Hapus file dari Google Drive
            $this->drive->files->delete($fileId);

            // Redirect kembali ke halaman files dan folders dengan pesan sukses
            return redirect()->route('google-drive.index')->with('success', 'File deleted successfully!');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, redirect kembali ke halaman files dan folders dengan pesan error
            return redirect()->route('google-drive.index')->with('error', 'Failed to delete file. Error: ' . $e->getMessage());
        }
    }

    public function downloadFile($fileId)
    {
        // Mendapatkan URL unduhan file dari Google Drive
        $file = $this->drive->files->get($fileId, ['fields' => 'webContentLink']);
        $downloadUrl = $file->getWebContentLink();

        // Mengalihkan pengguna ke URL unduhan
        return redirect()->away($downloadUrl);
    }

    public function previewFile($fileId)
    {
        // Mendapatkan URL preview file dari Google Drive
        $file = $this->drive->files->get($fileId, ['fields' => 'webViewLink']);
        $previewUrl = $file->getWebViewLink();

        // Mengalihkan pengguna ke URL preview
        return redirect()->away($previewUrl);
    }

    public function index($folderId = null)
    {
        $parentFolderId = null;

        if ($folderId) {
            // Ambil file dan folder di dalam folder tertentu
            $filesAndFolders = $this->getFilesAndFoldersInFolder($folderId);

            // Ambil parent folder ID untuk tombol back
            $parentFolderId = $this->getParentFolderId($folderId);
        } else {
            // Ambil file dan folder di root
            $filesAndFolders = $this->getFilesAndFoldersInFolder();
        }

        // Tampilkan view dengan data file dan folder
        return view('google-drive.files-and-folders', compact('filesAndFolders', 'folderId', 'parentFolderId'));
    }

    protected function getParentFolderId($folderId)
    {
        // Panggil API Google Drive untuk mendapatkan metadata folder
        $folder = $this->drive->files->get($folderId, ['fields' => 'parents']);

        // Jika folder memiliki parent, kembalikan parent pertama
        if ($folder->parents) {
            return $folder->parents[0];
        }

        // Jika tidak ada parent, kembalikan null
        return null;
    }


    protected function getFilesAndFoldersInFolder($folderId = null)
    {
        // Tentukan query untuk Google Drive API
        $query = "'$folderId' in parents and trashed=false";

        // Panggil API Google Drive untuk mendapatkan file dan folder
        $filesAndFolders = $this->drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, mimeType)',
        ]);

        return $filesAndFolders->files;
    }

    public function folder($folderId)
    {
        // Redirect ke halaman files dan folders dengan folderId sebagai parameter
        return redirect()->route('google-drive.index', ['folderId' => $folderId]);
    }
}
