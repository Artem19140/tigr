<?php

namespace App\Http\Controllers\Web\Upload;

use App\Http\Requests\Upload\ChunkStoreRequest;
use App\Http\Requests\Upload\UploadStoreRequest;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController
{  
    public function store(UploadStoreRequest $request)
    {
        $upload = Upload::create([
            'total_chunks' => $request->validated('totalChunks'),
            'uploaded_chunks' => 0,
            'center_id' => $request->user()->center_id,
            'status' => 'uploading',
            'original_name' => $request->validated('fileName'),
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

        $request->file('chunk')->storeAs($upload->chunksPath(), $order);

        $upload->increment('uploaded_chunks');

        if($upload->allChunksRecieved()){
            $path = $this->mergeChunks($upload);
            $upload->path = $path;
            $upload->status='completed';
            $upload->save();
        }

        return response()->json([
            'isLastChunk' => $upload->allChunksRecieved(),
            'uploadId' => $upload->id
        ]);
    }

    protected function mergeChunks(Upload $upload):string 
    {
        $extension = pathinfo($upload->original_name, PATHINFO_EXTENSION);
        $relativePath = $upload->chunksPath() . '' . $extension;

        $path = Storage::disk('local')->path($relativePath);

        $output = fopen($path, 'wb');
        //Определить mime и сравнить с extension
        for($i = 1; $i <= $upload->total_chunks; $i++){
            $chunkPath = Storage::disk('local')->path($upload->chunksPath() . '' .$i) ;
            $input = fopen($chunkPath, 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
        }

        fclose($output);
        Storage::deleteDirectory("uploads/$upload->uuid");

        return $relativePath;
    }

}
