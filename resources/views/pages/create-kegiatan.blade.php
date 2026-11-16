@extends('adminlte::page')

@section('title', 'Sikahim | Buat Kegiatan')

@section('content_header')
    <x-header title="Buat Kegiatan" breadcrumb="Buat Kegiatan" />
@stop

@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)
@section('plugins.BsCustomFileInput', true)
@section('plugins.TempusDominusBs4', true)

@section('content')
    <form action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
            <!-- Bagian Kiri -->
            <div class="col-md-6">
                <x-adminlte-input-file id="thumbnail" name="thumbnail" label="Thumbnail Kegiatan"
                    placeholder="Upload thumbnail kegiatan" igroup-size="md" legend="Browse">
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-dark">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>

                <x-adminlte-input name="activity_name" label="Nama Kegiatan" placeholder="Masukkan nama kegiatan" label-class="text-dark" required>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-file-signature text-dark"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <x-adminlte-input name="penyelenggara" label="Penyelenggara Kegiatan" placeholder="Masukkan nama penyelenggara" label-class="text-dark" required>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-users text-dark"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-select2 name="category_id" label="Kategori" label-class="text-black"
                            igroup-size="md" data-placeholder="Pilih kategori" required>
                            <x-slot name="prependSlot">
                                <div class="input-group-text">
                                    <i class="fas fa-tag text-dark"></i>
                                </div>
                            </x-slot>

                            <option value="" disabled selected>Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                    <div class="col-md-8">
                        @php
                            $config = [
                                "placeholder" => "Tambahkan tags",
                                "allowClear" => true,
                            ];
                        @endphp
                        <x-adminlte-select2 id="tags" name="sel2Category[]" label="Tags (Optional)"
                            label-class="text-black" igroup-size="md" :config="$config" multiple>
                            <x-slot name="prependSlot">
                                <div class="input-group-text">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </x-slot>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->tag_id }}">{{ $tag->tag_name }}</option>
                            @endforeach
                        </x-adminlte-select2>
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan -->
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        @php
                            $configStart = ['format' => 'YYYY-MM-DD HH:mm'];
                        @endphp
                        <x-adminlte-input-date name="datetime_started" :config="$configStart" :value="$startAt" placeholder="Pilih Tanggal"
                            label="Tanggal Mulai" label-class="text-dark" required>
                            <x-slot name="appendSlot">
                                <x-adminlte-button theme="outline-dark" icon="fas fa-lg fa-calendar"
                                    title="Set Date Started"/>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                    <div class="col-md-6">
                        @php
                            $configFinished = ['format' => 'YYYY-MM-DD HH:mm'];
                        @endphp
                        <x-adminlte-input-date name="datetime_finished" :config="$configFinished" :value="$endAt" placeholder="Pilih Tanggal"
                            label="Tanggal Selesai" label-class="text-dark" required>
                            <x-slot name="appendSlot">
                                <x-adminlte-button theme="outline-dark" icon="fas fa-lg fa-calendar"
                                    title="Set Date Finished"/>
                            </x-slot>
                        </x-adminlte-input-date>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kuota">Kuota Peserta</label>
                    <input type="number" class="form-control" id="kuota" name="max_participant" placeholder="Kuota Peserta" required>
                </div>

                <div class="form-group">
                    <label for="lokasi">Lokasi Kegiatan</label>
                    <input type="text" class="form-control" id="lokasi" name="location" placeholder="Lokasi" required>
                </div>

                <x-adminlte-input-file id="materi" name="materi[]" label="Materi Kegiatan (Optional)"
                    placeholder="Upload materi kegiatan" igroup-size="md" legend="Browse" multiple>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-dark">
                            <i class="fas fa-file-upload"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input-file>

                <div class="form-group">
                    <label for="deskripsi">Deskripsi Kegiatan</label>
                    <textarea class="form-control" id="deskripsi" name="description" rows="4" placeholder="Deskripsi Kegiatan" required></textarea>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary mb-5">Submit</button>
            </div>
        </div>
    </form>
@stop

@section('footer')
    @include('components.footer')
@endsection
