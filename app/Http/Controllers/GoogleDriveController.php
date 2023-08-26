<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleDriveController extends Controller
{
    public function uploadFile(Request $request)
    {
        $uploadedFile = $request->file('file');
        $filename = time() . '_' . $uploadedFile->getClientOriginalName();
        $googleDisk = Storage::disk('google');
        $googleDisk->put($filename, file_get_contents($uploadedFile));
        $googleDisk->setVisibility($filename, 'public');
        $url = $googleDisk->url($filename);
        return response()->json(['url' => $url]);
    }
}
