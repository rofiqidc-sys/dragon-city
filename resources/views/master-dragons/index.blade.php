@extends('layouts.app')

@section('title', 'Master Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Master Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Master Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0" style="background: #ffffff;">
            <div class="card-header" style="background: linear-gradient(145deg, #73EC8B 0%, #06D001 100%); border-radius: .5rem; color: #ffffff; border: none;">
                <h5 class="mb-1" style="font-weight: 700;">Master Dragon</h5>
                <p class="mb-0" style="opacity: .9;">Dragon yang belum dimiliki oleh account dengan ID 1.</p>
            </div>
            <div class="card-body" style="background: #ffffff; color: #1f2d20;">
                <p class="card-text text-dark">Halaman ini menampilkan semua dragon yang belum dimiliki oleh account target. Warna dominan putih membuat daftar lebih bersih, sementara hijau digunakan sebagai aksen utama.</p>
                <form method="GET" action="{{ route('master-dragons.index') }}" class="row gx-2 align-items-end mb-4">
                    <div class="col-md-6">
                        <label for="rarity" class="form-label">Filter Rarity</label>
                        <select id="rarity" name="rarity" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Rarity --</option>
                            @foreach($rarities as $rarity)
                                <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">Cari Dragon</label>
                        <div class="input-group">
                            <input id="search" name="search" type="search" class="form-control" placeholder="Cari nama, book, atau rarity..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-success">Cari</button>
                        </div>
                    </div>
                </form>
                <div class="row mt-4">
                    @forelse($dragons as $dragon)
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm" style="background: #ffffff; border: 1.5px solid #73EC8B;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 text-dark">{{ $dragon->dragon_name }}</h5>
                                            <p class="text-muted mb-0">{{ $dragon->dragon_book ?? '-' }}</p>
                                        </div>
                                        <span class="badge" style="background: #73EC8B; color: #064400; font-weight: 700;">ID {{ $dragon->id }}</span>
                                    </div>

                                    <p class="mb-2"><strong>Summon Time:</strong> <span class="text-dark">{{ $dragon->summon_time ?? '-' }}</span></p>
                                    <p class="mb-2"><strong>Orb to Summon:</strong> <span class="text-dark">{{ $dragon->orb_to_summon }}</span></p>
                                    <p class="mb-2"><strong>Owned Orb:</strong> <span class="text-dark">{{ $dragon->jumlah_orb ?? 0 }}</span></p>
                                    <p class="mb-2"><strong>Rarity:</strong> <span class="text-dark">{{ $dragon->rarity->name ?? '-' }}</span></p>
                                    <p class="mb-2"><strong>Elements:</strong>
                                        <span class="text-dark">
                                            {{ $dragon->element1->name ?? '' }}
                                            {{ $dragon->element2 ? ' / ' . $dragon->element2->name : '' }}
                                            {{ $dragon->element3 ? ' / ' . $dragon->element3->name : '' }}
                                            {{ $dragon->element4 ? ' / ' . $dragon->element4->name : '' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <span class="text-success" style="font-weight: 700;">Belum dimiliki oleh account #1</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-success" role="alert">
                                Semua dragon sudah dimiliki oleh account #1.
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $dragons->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
