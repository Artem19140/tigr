<?php

namespace App\Http\Controllers\Web\Upload;

use App\Http\Requests\Upload\ChunkStoreRequest;
use App\Http\Requests\Upload\UploadStoreRequest;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController
{
    public function index()
    {
        //
    }
    
    public function store(UploadStoreRequest $request)
    {
        $upload = Upload::create([
            'total_chunks' => $request->validated('totalChunks'),
            'uploaded_chunks' => 0,
            'center_id' => $request->user()->center_id,
            'status' => 'uploading',
            'original_name' => $request->validated('fileName'),
            'mime_type' => $request->validated('fileType'),
            'uuid' => Str::uuid()
        ]);
        return response()->json(['uploadId' => $upload->id]);
    }

    public function chunk(
        ChunkStoreRequest $request,
        Upload $upload
    )
    {
        $order = $request->validated('order');
        $request->file('chunk')->storeAs("uploads/$upload->uuid", $order);
        $upload->increment('uploaded_chunks');
        $isLastChunk = $upload->total_chunks === $upload->uploaded_chunks;
        if($isLastChunk){
            $path = $this->mergeChunks($upload);
            $upload->path = $path;
            $upload->status='completed';
            $upload->save();
        }
        return response()->json([
            'isLastChunk' => $isLastChunk,
            'uploadId' => $upload->id
        ]);
    }

    public function show(Upload $upload)
    {
        //
    }

    protected function mergeChunks(Upload $upload):string 
    {
        $uuid = $upload->uuid;
        $extension = pathinfo($upload->original_name, PATHINFO_EXTENSION);
        $relativePath = "uploads/$uuid.$extension";
        $path = Storage::disk('local')->path("uploads/$uuid.$extension");

        $output = fopen($path, 'wb');

        for($i = 1; $i <= $upload->total_chunks; $i++){
            $chunkPath = Storage::disk('local')->path("uploads/$upload->uuid/$i") ;
            $input = fopen($chunkPath, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
        }

        fclose($output);
        Storage::deleteDirectory("uploads/$upload->uuid");
        return $relativePath;
    }

}
