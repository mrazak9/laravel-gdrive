<?php

use App\Http\Controllers\GoogleDriveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/files', [GoogleDriveController::class, 'listFiles']);
Route::post('/files', [GoogleDriveController::class, 'createFile']);
Route::put('/files/{id}', [GoogleDriveController::class, 'updateFile']);
Route::delete('/files/{id}', [GoogleDriveController::class, 'deleteFile']);
Route::get('/file-info/{fileId}', [GoogleDriveController::class, 'getFileInfo']);
Route::get('/files-and-folders', [GoogleDriveController::class, 'showFilesAndFolders'])->name('google-drive.files_and_folders');
// Route::get('/google-drive/files-and-folders', [GoogleDriveController::class, 'showFilesAndFolders'])->name('google-drive.files-and-folders');
Route::post('/download/{fileId}', [GoogleDriveController::class, 'downloadFile'])->name('google-drive.download');
Route::get('/preview/{fileId}', [GoogleDriveController::class, 'previewFile'])->name('google-drive.preview');

Route::view('/upload', 'google-drive.upload')->name('google-drive.upload');
Route::post('/upload', [GoogleDriveController::class, 'upload'])->name('google-drive.upload.post');
Route::delete('/delete/{fileId}', [GoogleDriveController::class, 'delete'])->name('google-drive.delete');
// Route untuk menampilkan halaman files dan folders
Route::get('/google-drive/{folderId?}', [GoogleDriveController::class, 'index'])->name('google-drive.index');

// Route untuk masuk ke dalam folder
Route::get('/google-drive/folder/{folderId}', [GoogleDriveController::class, 'folder'])->name('google-drive.folder');
