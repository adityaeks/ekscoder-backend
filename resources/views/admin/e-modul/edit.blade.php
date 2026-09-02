<x-admin-layout title="Edit E-Modul" breadcrumb="Perbarui informasi modul dan file dokumen PDF">
    <div style="max-width:800px; margin:0 auto;">
        <div class="card" style="padding:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
                <div>
                    <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">Edit E-Modul</h2>
                    <p style="font-size:13px; color:var(--text-secondary);">Perbarui detail e-modul atau ganti file PDF yang terpasang.</p>
                </div>
                <a href="{{ route('admin.e-modul.index') }}" class="topbar-btn" style="padding:6px 12px; font-size:12px; text-decoration:none;">
                    Batal
                </a>
            </div>

            <form action="{{ route('admin.e-modul.update', $e_modul->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:flex; flex-direction:column; gap:18px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Judul E-Modul <span style="color:var(--rose);">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $e_modul->title) }}" required style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Kategori / Topik</label>
                            <input type="text" name="category" value="{{ old('category', $e_modul->category) }}" placeholder="Contoh: IPA, Pemrograman, Desain" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Status Publikasi</label>
                            <select name="is_active" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">
                                <option value="1" {{ $e_modul->is_active ? 'selected' : '' }}>Published (Aktif)</option>
                                <option value="0" {{ !$e_modul->is_active ? 'selected' : '' }}>Draft (Nonaktif)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Deskripsi / Ringkasan Modul</label>
                        <textarea name="description" rows="3" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">{{ old('description', $e_modul->description) }}</textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Ganti File PDF (Opsional)</label>
                        <input type="file" name="pdf_file" accept=".pdf" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:8px 12px; font-size:13px; color:var(--text-primary);">
                        <p style="font-size:11.5px; color:var(--text-muted); margin-top:4px;">Kosongkan jika tidak ingin mengubah file PDF. File saat ini: {{ $e_modul->formatted_size }}</p>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
                        <a href="{{ route('admin.e-modul.index') }}" class="topbar-btn" style="padding:8px 16px; font-size:13px; text-decoration:none;">Batal</a>
                        <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:8px 20px; font-size:13px; border:none; cursor:pointer;">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
