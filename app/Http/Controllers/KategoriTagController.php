<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kategori;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KategoriTagController extends Controller
{
    public function index()
    {
        $categories = Kategori::orderBy('category_name')->get();
        $tags = Tag::withCount('kegiatan')->orderBy('tag_name')->get();

        return view('pages.admin.kategori-tag', compact('categories', 'tags'));
    }

    public function storeKategori(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:kategori,category_name'],
        ]);

        $kategori = Kategori::create($validated);
        AuditLog::record('create_kategori', "Menambahkan kategori: {$kategori->category_name}", 'Kategori', $kategori->category_id);

        return redirect()->route('kategori-tag.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, Kategori $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', 'unique:kategori,category_name,'.$kategori->category_id.',category_id'],
        ]);

        $oldName = $kategori->category_name;
        $kategori->update($validated);
        AuditLog::record('update_kategori', "Mengubah kategori {$oldName} menjadi {$kategori->category_name}", 'Kategori', $kategori->category_id);

        return redirect()->route('kategori-tag.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(Kategori $kategori): RedirectResponse
    {
        $name = $kategori->category_name;
        $id = $kategori->category_id;
        $kategori->delete();
        AuditLog::record('delete_kategori', "Menghapus kategori: {$name}", 'Kategori', $id);

        return redirect()->route('kategori-tag.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tag_name' => ['required', 'string', 'max:255', 'unique:tag,tag_name'],
        ]);

        $tag = Tag::create($validated);
        AuditLog::record('create_tag', "Menambahkan tag: {$tag->tag_name}", 'Tag', $tag->tag_id);

        return redirect()->route('kategori-tag.index')->with('success', 'Tag berhasil ditambahkan.');
    }

    public function updateTag(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'tag_name' => ['required', 'string', 'max:255', 'unique:tag,tag_name,'.$tag->tag_id.',tag_id'],
        ]);

        $oldName = $tag->tag_name;
        $tag->update($validated);
        AuditLog::record('update_tag', "Mengubah tag {$oldName} menjadi {$tag->tag_name}", 'Tag', $tag->tag_id);

        return redirect()->route('kategori-tag.index')->with('success', 'Tag berhasil diperbarui.');
    }

    public function destroyTag(Tag $tag): RedirectResponse
    {
        $name = $tag->tag_name;
        $id = $tag->tag_id;
        $tag->delete();
        AuditLog::record('delete_tag', "Menghapus tag: {$name}", 'Tag', $id);

        return redirect()->route('kategori-tag.index')->with('success', 'Tag berhasil dihapus.');
    }
}
