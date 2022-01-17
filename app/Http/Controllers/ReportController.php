<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->get();
        return ReportResource::collection($reports);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'judul' => ['required'],
            'deskripsi' => ['required'],
            // 'gambar' => ['required', 'image'],
        ]);

        if ($validate->fails()) return response()->json([
            'message' => 'Your Request is Invalid',
            'errors' => $validate->errors()
        ], 400);

        $gambar = $request->file('gambar');
        $path = $gambar->store('reports', ['disk' => 'public']);

        $request->user()->reports()->create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $path,
            'status' => 'menunggu'
        ]);


        return response()->json([
            'message' => 'Pengaduan Berhasil di Tambah'
        ]);
    }

    public function destroy($id)
    {
        $response = Response::find($id);

        if (!$response) return ResponseBuilder::buildErrorResponse('Response Not Found', [], 404);

        $response->delete();

        return ResponseBuilder::buildResponse('Response deleted successfuly', []);
    }

}
