@extends('adminlte::page')

@section('title', 'Sikahim | Edit Kegiatan')

@section('content_header')
    <x-header title="Edit Kegiatan" breadcrumb="Edit Kegiatan" />
@stop

@section('plugins.Select2', true)
@section('plugins.BsCustomFileInput', true)
@section('plugins.TempusDominusBs4', true)

@section('content')
    <form action="{{ route('kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-input-file id="thumbnail" name="thumbnail" label="Ganti Thumbnail Kegiatan (Opsional)"
                    placeholder="Upload thumbnail baru" igroup-size="md" legend="Browse">
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-dark">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>

                <x-adminlte-input name="activity_name" label="Nama Kegiatan"
                    value="{{ old('activity_name', $kegiatan->activity_name) }}"
                    placeholder="Masukkan nama kegiatan" label-class="text-dark" required />

                <x-adminlte-input name="penyelenggara" label="Penyelenggara Kegiatan"
                    value="{{ old('penyelenggara', $kegiatan->penyelenggara) }}"
                    placeholder="Masukkan nama penyelenggara" label-class="text-dark" required />

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-select2 name="category_id" label="Kategori" label-class="text-black"
                            igroup-size="md" data-placeholder="Pilih kategori" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" @selected(old('category_id', $kegiatan->category_id) == $category->category_id)>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                    <div class="col-md-8">
                        @php
                            $selectedTags = collect(old('sel2Category', $kegiatan->tags->pluck('tag_id')->all()));
                            $config = ['placeholder' => 'Tambahkan tags', 'allowClear' => true];
                        @endphp
                        <x-adminlte-select2 id="tags" name="sel2Category[]" label="Tags (Optional)"
                            label-class="text-black" igroup-size="md" :config="$config" multiple>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->tag_id }}" @selected($selectedTags->contains($tag->tag_id))>
                                    {{ $tag->tag_name }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="datetime_started">Tanggal Mulai</label>
                            <input type="text" class="form-control" id="datetime_started" name="datetime_started"
                                value="{{ old('datetime_started', $kegiatan->datetime_started->format('Y-m-d H:i')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="datetime_finished">Tanggal Selesai</label>
                            <input type="text" class="form-control" id="datetime_finished" name="datetime_finished"
                                value="{{ old('datetime_finished', $kegiatan->datetime_finished->format('Y-m-d H:i')) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kuota">Kuota Peserta (Opsional)</label>
                    <input type="number" class="form-control" id="kuota" name="max_participant" min="1"
                        value="{{ old('max_participant', $kegiatan->max_participant) }}"
                        placeholder="Kosongkan jika tanpa batas peserta">
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi Kegiatan</label>
                    <input type="text" class="form-control" id="lokasi" name="location"
                        value="{{ old('location', $kegiatan->location) }}" placeholder="Lokasi" required>
                </div>

                @if (!empty($kegiatan->materi))
                    <div class="form-group">
                        <label>Materi Saat Ini (Centang untuk menghapus)</label>
                        @foreach ($kegiatan->materi as $materiPath)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_materi[]" value="{{ $materiPath }}" id="rem_{{ $loop->index }}">
                                <label class="form-check-label text-danger" for="rem_{{ $loop->index }}">
                                    Hapus {{ basename($materiPath) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                <x-adminlte-input-file id="materi" name="materi[]" label="Tambah Materi Kegiatan Baru (Opsional)"
                    placeholder="Upload materi baru" igroup-size="md" legend="Browse" multiple />

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Kegiatan</label>
                    <textarea class="form-control" id="deskripsi" name="description" rows="4" required>{{ old('description', $kegiatan->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary mb-5">Simpan Perubahan</button>
            </div>
        </div>
    </form>
@stop

@section('footer')
    @include('components.footer')
@endsection
