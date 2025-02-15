<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    /**
     * Menampilkan semua kategori (akses publik)
     */
    public function index()
    {
        $categories = Category::all();
        return ApiFormatter::createJson(200, 'Daftar kategori berhasil diambil', $categories);
    }

    /**
     * Menampilkan satu kategori berdasarkan ID (akses publik)
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return ApiFormatter::createJson(200, 'Kategori berhasil ditemukan', $category);
        } catch (ModelNotFoundException $e) {
            return ApiFormatter::createJson(404, 'Kategori tidak ditemukan');
        }
    }

    /**
     * Menambahkan kategori baru (hanya admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::create($request->only(['name']));

        return ApiFormatter::createJson(201, 'Kategori berhasil ditambahkan', $category);
    }

    /**
     * Memperbarui kategori berdasarkan ID (hanya admin)
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category->update($request->only(['name']));

            return ApiFormatter::createJson(200, 'Kategori berhasil diperbarui', $category);
        } catch (ModelNotFoundException $e) {
            return ApiFormatter::createJson(404, 'Kategori tidak ditemukan');
        }
    }

    /**
     * Menghapus kategori berdasarkan ID (hanya admin)
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return ApiFormatter::createJson(200, 'Kategori berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return ApiFormatter::createJson(404, 'Kategori tidak ditemukan');
        } catch (\Exception $e) {
            return ApiFormatter::createJson(500, 'Gagal menghapus kategori', ['error' => $e->getMessage()]);
        }
    }
}
