@extends('layouts.app')

@section('title', 'Flash Able - Most Trusted Admin Template')

@push('styles')
<style>
    #tbl-best-high-scroll {
        height: 478px;
        position: relative;
        overflow: auto;
    }

    #tbl-best-high-responsive {
        overflow: visible;
    }

    #tbl-best-high {
        min-width: max-content;
    }

    #tbl-best-high thead {
        position: relative;
        z-index: 5;
    }

    #tbl-best-high thead th {
        position: relative;
        z-index: 5;
        background: #f6e7a8;
    }

    #tbl-best-high tfoot {
        position: relative;
        z-index: 4;
    }

    #tbl-best-high tfoot td {
        position: relative;
        z-index: 4;
        background: #f6e7a8;
    }

    #tbl-best-high thead th:first-child,
    #tbl-best-high tfoot td:first-child {
        left: 0;
        z-index: 4;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Home</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Analytics Dashboard</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(session('success'))
        <div class="col-sm-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif

    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card bg-c-red">
            <div class="card-body">
                <div class="row align-items-center m-b-25">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Total Profit</h6>
                        <h3 class="m-b-0 text-white">$1,783</h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-danger m-r-10">+11%</span>From Previous Month</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card bg-c-blue">
            <div class="card-body">
                <div class="row align-items-center m-b-25">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Total Orders</h6>
                        <h3 class="m-b-0 text-white">15,830</h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database text-c-blue f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-primary m-r-10">+12%</span>From Previous Month</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card bg-c-green">
            <div class="card-body">
                <div class="row align-items-center m-b-25">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Average Price</h6>
                        <h3 class="m-b-0 text-white">$6,780</h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign text-c-green f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-success m-r-10">+52%</span>From Previous Month</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card prod-p-card bg-c-yellow">
            <div class="card-body">
                <div class="row align-items-center m-b-25">
                    <div class="col">
                        <h6 class="m-b-5 text-white">Product Sold</h6>
                        <h3 class="m-b-0 text-white">6,784</h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags text-c-yellow f-18"></i>
                    </div>
                </div>
                <p class="m-b-0 text-white"><span class="label label-warning m-r-10">+52%</span>From Previous Month</p>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-4">
        <div class="card card-social">
            <div class="card-block border-bottom">
                <div class="row align-items-center justify-content-center">
                    <div class="col-auto">
                        <i class="fab fa-facebook-f text-primary f-36"></i>
                    </div>
                    <div class="col text-right">
                        <h3>12,281</h3>
                        <h5 class="text-c-blue mb-0">+7.2% <span class="text-muted">Total Likes</span></h5>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="row align-items-center justify-content-center card-active">
                    <div class="col-6">
                        <h6 class="text-center m-b-10"><span class="text-muted m-r-5">Target:</span>35,098</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-blue" role="progressbar" style="width:60%;height:6px;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-center  m-b-10"><span class="text-muted m-r-5">Duration:</span>350</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-green" role="progressbar" style="width:45%;height:6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card card-social">
            <div class="card-block border-bottom">
                <div class="row align-items-center justify-content-center">
                    <div class="col-auto">
                        <i class="fab fa-twitter text-c-info f-36"></i>
                    </div>
                    <div class="col text-right">
                        <h3>11,200</h3>
                        <h5 class="text-c-info mb-0">+6.2% <span class="text-muted">Total Likes</span></h5>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="row align-items-center justify-content-center card-active">
                    <div class="col-6">
                        <h6 class="text-center m-b-10"><span class="text-muted m-r-5">Target:</span>34,185</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-blue" role="progressbar" style="width:40%;height:6px;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-center  m-b-10"><span class="text-muted m-r-5">Duration:</span>800</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-green" role="progressbar" style="width:70%;height:6px;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card card-social">
            <div class="card-block border-bottom">
                <div class="row align-items-center justify-content-center">
                    <div class="col-auto">
                        <i class="fab fa-google-plus-g text-c-red f-36"></i>
                    </div>
                    <div class="col text-right">
                        <h3>10,500</h3>
                        <h5 class="text-c-red mb-0">+5.9% <span class="text-muted">Total Likes</span></h5>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="row align-items-center justify-content-center card-active">
                    <div class="col-6">
                        <h6 class="text-center m-b-10"><span class="text-muted m-r-5">Target:</span>25,998</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-blue" role="progressbar" style="width:80%;height:6px;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-center  m-b-10"><span class="text-muted m-r-5">Duration:</span>900</h6>
                        <div class="progress">
                            <div class="progress-bar progress-c-green" role="progressbar" style="width:50%;height:6px;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-md-6">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Best High Dragons</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#bestHeroicOrbModal">
                        <i class="fas fa-plus mr-1"></i>Entri Orbs
                    </button>
                </div>
            </div>
            <div class="card-body px-0 py-0">
                <div class="table-responsive" id="tbl-best-high-responsive">
                    <div id="tbl-best-high-scroll">
                        <table class="table table-hover m-b-0" id="tbl-best-high" style="border: 2px solid #d6b656; border-radius: 10px; border-collapse: separate; border-spacing: 0; overflow: hidden; background: #fffdf2;">
                            <thead>
                                <tr>
                                    <th style="background: #f6e7a8; color: #6b5717; border-bottom: 2px solid #d6b656; border-right: 1px solid #e4d28a;"><span>Account</span></th>
                                    @foreach($bestHighDragons as $dragon)
                                        @php
                                            $dragonNameParts = explode(' ', $dragon->dragon_name);
                                        @endphp
                                        <th style="background: #f6e7a8; color: #6b5717; border-bottom: 2px solid #d6b656; border-right: 1px solid #e4d28a;"><span>{{ $dragonNameParts[1] ?? $dragon->dragon_name }}</span></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $account)
                                    <tr>
                                        <td style="border-right: 1px solid #eadca7; border-bottom: 1px solid #eee5bf; color: #5f501d; font-weight: 600;">{{ $account->account_name }}</td>
                                        @php
                                            $orbByDragonId = $account->orbOwnings->keyBy('dragon_id');
                                        @endphp

                                        @foreach($bestHighDragons as $dragon)
                                            <td style="border-right: 1px solid #eadca7; border-bottom: 1px solid #eee5bf; color: #5f501d; text-align: center;">{{ $orbByDragonId->get($dragon->id)?->jumlah_orb ?? 0 }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 1 + $bestHighDragons->count() }}" class="text-center">No accounts available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="background: #f6e7a8; border-top: 2px solid #d6b656; border-right: 1px solid #e4d28a; color: #6b5717; font-weight: 700;">Total</td>
                                    @foreach($bestHighDragons as $dragon)
                                        @php
                                            $totalOrb = $accounts->sum(function ($account) use ($dragon) {
                                                return $account->orbOwnings
                                                    ->where('dragon_id', $dragon->id)
                                                    ->sum('jumlah_orb');
                                            });
                                        @endphp
                                        <td style="background: #f6e7a8; border-top: 2px solid #d6b656; border-right: 1px solid #e4d28a; color: #6b5717; text-align: center; font-weight: 700;">{{ $totalOrb }}</td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card user-card">
            <div class="card-header">
                <h5>Seeder</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Seed data yang tersedia untuk database.</p>

                <p class="text-muted small mb-3">Klik tombol di setiap baris untuk langsung menjalankan seeder yang sesuai.</p>

                <form action="{{ route('home.run-seeder') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="seed" value="all">
                    <button type="submit" class="btn btn-success btn-sm">Load All Seeder</button>
                </form>

                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Account Seeder</span>
                        <form action="{{ route('home.run-seeder') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="seed" value="AccountSeeder">
                            <button type="submit" class="btn btn-sm btn-primary">Run</button>
                        </form>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Element Seeder</span>
                        <form action="{{ route('home.run-seeder') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="seed" value="ElementSeeder">
                            <button type="submit" class="btn btn-sm btn-primary">Run</button>
                        </form>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Rarity Seeder</span>
                        <form action="{{ route('home.run-seeder') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="seed" value="RaritySeeder">
                            <button type="submit" class="btn btn-sm btn-primary">Run</button>
                        </form>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Dragon Seeder</span>
                        <form action="{{ route('home.run-seeder') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="seed" value="DragonSeeder">
                            <button type="submit" class="btn btn-sm btn-primary">Run</button>
                        </form>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Best Heroic Seeder</span>
                        <form action="{{ route('home.run-seeder') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="seed" value="BestHeroicSeeder">
                            <button type="submit" class="btn btn-sm btn-primary">Run</button>
                        </form>
                    </div>
                </div>

                @if(session('seed_status'))
                    <div class="alert alert-success mt-3 mb-0">
                        {{ session('seed_status') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bestHeroicOrbModal" tabindex="-1" role="dialog" aria-labelledby="bestHeroicOrbModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bestHeroicOrbModalLabel">Entri Orbs Best Heroic</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('orb-ownings.store-best-heroic') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="orb_account_id">Account <span class="text-danger">*</span></label>
                        <select id="orb_account_id" name="account_id" class="form-control" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        @foreach($bestHighDragons as $dragon)
                            <div class="col-md-6 form-group">
                                <label for="orb_{{ $dragon->id }}">{{ $dragon->dragon_name }}</label>
                                <input type="number" id="orb_{{ $dragon->id }}" name="orbs[{{ $dragon->id }}]" class="form-control" min="0" value="{{ old('orbs.' . $dragon->id, 0) }}" required>
                                @error('orbs.' . $dragon->id)
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Orbs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scrollContainer = document.getElementById('tbl-best-high-scroll');
    const table = document.getElementById('tbl-best-high');

    if (!scrollContainer || !table) return;

    const tableHeader = table.querySelector('thead');
    const tableFooter = table.querySelector('tfoot');

    function updatePinnedRows() {
        const maxScrollTop = Math.max(0, scrollContainer.scrollHeight - scrollContainer.clientHeight);
        const scrollTop = scrollContainer.scrollTop;

        tableHeader.style.transform = `translateY(${scrollTop}px)`;
        tableFooter.style.transform = `translateY(${scrollTop - maxScrollTop}px)`;
    }

    scrollContainer.addEventListener('scroll', updatePinnedRows, { passive: true });
    window.addEventListener('resize', updatePinnedRows);
    updatePinnedRows();
});
</script>
@endsection
