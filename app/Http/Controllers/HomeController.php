<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function store(Request $Request)
    {   
        $validation = Validator::make($Request->all(), [
            'file' => 'image|required|mimes:jpeg,png,gif,svg|max:2048',
        ]);
        if ($validation->fails()) {
            return redirect()->back()->with('error', $validation->errors()->first());
        }
        $file = $Request->file('file');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move(public_path('uploads/'), $filename);
        $path = public_path('uploads/' . $filename);

        $data = [[
            'filename' => $filename,
            'object' => [[
                'name' => 'category1',
                'ocr_text' => 'text inside the bounding box',
                'bndbox' => [
                    'xmin' => 1,
                    'ymin' => 1,
                    'xmax' => 100,
                    'ymax' => 100
                ],
            ]]
        ]];
        $response = Http::withHeaders([
            'accept' => 'multipart/form-data',
            'x-api-key' => '1c7f9a22-fb2a-11ee-9a1b-5e0f9c6b2a2f',
            'Authorization' => 'Basic Og=='
        ])->attach('file', file_get_contents($path), $filename)
            ->post('https://app.nanonets.com/api/v2/OCR/Model/451c4536-b709-4f17-935c-3576e1bd2d98/UploadFile/', [
                'data' => json_encode($data)
            ]);
        if ($response->successful()) {
            $uploadedFile = new UploadedFile();
            $uploadedFile->filename = $filename;
            $uploadedFile->original_name = $file->getClientOriginalName();
            $uploadedFile->file_path = $path;
            $uploadedFile->data = $response->body();
            $uploadedFile->save();
            return redirect()->route('home')->with('success', "File `{$uploadedFile->original_name}` uploaded successfully.");
        } else {
            return redirect()->back()->with('error', 'There was an error uploading the file.');
        }
    }
}
