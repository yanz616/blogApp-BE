<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    /**
     * Menampilkan semua user kecuali admin
     */
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();

        if ($users->isEmpty()) {
            return ApiFormatter::createJson(404, 'Tidak ada user yang ditemukan');
        }

        return ApiFormatter::createJson(200, 'Daftar user berhasil diambil', $users);
    }

    /**
     * Menghapus post berdasarkan ID dengan validasi
     */
    public function deletePost($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return ApiFormatter::createJson(200, 'Post berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return ApiFormatter::createJson(404, 'Post tidak ditemukan');
        } catch (\Exception $e) {
            return ApiFormatter::createJson(500, 'Gagal menghapus post', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Menghapus komentar berdasarkan ID dengan validasi
     */
    public function deleteComment($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();

            return ApiFormatter::createJson(200, 'Komentar berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return ApiFormatter::createJson(404, 'Komentar tidak ditemukan');
        } catch (\Exception $e) {
            return ApiFormatter::createJson(500, 'Gagal menghapus komentar', ['error' => $e->getMessage()]);
        }
    }
}
