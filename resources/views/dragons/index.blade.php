@extends('layouts.app')

@section('title', 'Dragons')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Dragons</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Dragons</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-8">
        <div class="d-flex flex-wrap align-items-center">
            <a href="{{ route('dragons.create') }}" class="btn btn-success mr-2 mb-2" style="background: #2e8b57; border-color: #2e8b57;">Add Dragon</a>
            <form action="{{ route('dragons.index') }}" method="GET" class="d-flex align-items-center mr-2 mb-2">
                <label for="dragon-search" class="sr-only">Search dragons</label>
                <div class="input-group" style="min-width: 260px; max-width: 340px;">
                    <input type="search" id="dragon-search" name="search" value="{{ strtolower((string) request('search')) }}" class="form-control" placeholder="Search dragon..." aria-label="Search dragons" style="text-transform: lowercase;" oninput="this.value = this.value.toLowerCase();">
                    <button type="submit" class="btn btn-outline-success" title="Search dragons" aria-label="Search dragons">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <input type="hidden" id="sortInput" name="sort" value="{{ request('sort', 'asc') }}">
                <button type="button" id="sortToggleBtn" class="btn btn-outline-secondary btn-sm ml-2" title="Toggle sort" onclick="toggleSort()" aria-label="Toggle sort">
                    <i id="sortIcon" class="fas {{ request('sort', 'asc') == 'asc' ? 'fa-sort-alpha-down' : 'fa-sort-alpha-up' }}"></i>
                </button>
            </form>
            <form action="{{ route('dragons.index') }}" method="GET" class="d-flex align-items-center mr-2 mb-2">
                <input type="hidden" name="search" value="{{ strtolower((string) request('search')) }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'asc') }}">
                <label for="rarity-filter" class="sr-only">Filter by rarity</label>
                <select id="rarity-filter" name="rarity" class="form-control" style="min-width: 140px; max-width: 180px;">
                    <option value="">All rarities</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->id }}" {{ request('rarity') == $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-success ml-2">Filter</button>
            </form>
            @if(isset($dragons))
                <div class="ml-md-auto mb-2">
                    <small class="text-muted">Total: {{ $dragons->total() }}</small>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-4 d-flex justify-content-md-end flex-wrap align-items-center">
        <form action="{{ route('dragons.truncate') }}" method="POST" style="display:inline;" class="mr-2 mb-2">
            @csrf
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('This will clear all dragon data and reset the sequence. Continue?')">Truncate Data</button>
        </form>
        <button type="button" class="btn btn-outline-success mr-2 mb-2" data-toggle="modal" data-target="#generateModal">Generate</button>
        <form action="{{ route('dragons.generate-aliases') }}" method="POST" style="display:inline;" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-outline-success" onclick="return confirm('Generate ulang alias untuk semua naga?')">Generate Alias</button>
        </form>
    </div>
</div>

<div class="row">
    @forelse($dragons as $index => $dragon)
        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
            <div class="card prod-p-card shadow-sm border-0" style="background: linear-gradient(135deg, #f7fff9 0%, #e9f8ef 100%); color: #28543c;">
                <div class="card-body">
                    <div class="row align-items-center m-b-25">
                        <div class="col">
                            <h6 class="m-b-5" style="font-weight: 700; color: #28543c;">{{ $dragon->dragon_name }}</h6>
                            <small style="color: #5f7d6a; font-size:14px; font-weight:600;">Alias: {{ $dragon->alias ?? '-' }}</small>
                            <h3 class="m-b-0" style="color: #2e8b57;">{{ $dragon->orb_to_summon }}</h3>
                            <small style="color: #5f7d6a;">Orbs</small>
                        </div>
                        <div class="col-auto text-center" style="min-width:68px;">
                            <div>
                                <small style="color: #5f7d6a; display:block; font-size:16px; font-weight:700;">{{ $dragon->dragon_book ?? '-' }}</small>
                            </div>
                            <div>
                                <i class="fas fa-dragon f-18" style="color: #4caf50;"></i>
                            </div>
                        </div>
                    </div>
                    <p class="m-b-10">
                        <span class="badge badge-pill" style="background: #dff5e8; color: #2f6b4f; font-size:14px; font-weight:700; padding:6px 10px;">{{ $dragon->rarity->name ?? '-' }}</span>
                    </p>
                    <div class="row m-b-15">
                        <div class="col-12">
                            <small>
                                @if($dragon->element1)
                                    <span class="badge badge-light mr-1" style="background: #ffffff; color: #4f6f5f; font-size:13px; font-weight:600; padding:4px 8px;">{{ $dragon->element1->name }}</span>
                                @endif
                                @if($dragon->element2)
                                    <span class="badge badge-light mr-1" style="background: #ffffff; color: #4f6f5f; font-size:13px; font-weight:600; padding:4px 8px;">{{ $dragon->element2->name }}</span>
                                @endif
                                @if($dragon->element3)
                                    <span class="badge badge-light mr-1" style="background: #ffffff; color: #4f6f5f; font-size:13px; font-weight:600; padding:4px 8px;">{{ $dragon->element3->name }}</span>
                                @endif
                                @if($dragon->element4)
                                    <span class="badge badge-light mr-1" style="background: #ffffff; color: #4f6f5f; font-size:13px; font-weight:600; padding:4px 8px;">{{ $dragon->element4->name }}</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="row align-items-center justify-content-center" style="gap: 10px;">
                        <div class="col-auto">
                            <a href="{{ route('dragons.edit', $dragon) }}" class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e3f2fd; color: #2e8b57; border: none; border-radius: 50%; transition: all 0.2s ease-in-out; display: flex; align-items: center; justify-content: center;" title="Edit" onmouseover="this.style.background='#bbdefb'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='#e3f2fd'; this.style.transform='scale(1)'">
                                <i class="fas fa-pencil-alt" style="font-size: 16px;"></i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <form action="{{ route('dragons.markBestHeroic', $dragon) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #007bff; color: #fff; border: none; border-radius: 50%; transition: all 0.2s ease-in-out; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Jadikan Best Heroic" onclick="return confirm('Set this dragon as Best Heroic?')" onmouseover="this.style.background='#0069d9'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='#007bff'; this.style.transform='scale(1)'">
                                    <i class="fas fa-medal" style="font-size: 16px;"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col-auto">
                            <form action="{{ route('dragons.destroy', $dragon) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #fde8e8; color: #d9534f; border: none; border-radius: 50%; transition: all 0.2s ease-in-out; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Delete" onclick="return confirm('Delete this dragon?')" onmouseover="this.style.background='#f5c6c6'; this.style.transform='scale(1.1)'" onmouseout="this.style.background='#fde8e8'; this.style.transform='scale(1)'">
                                    <i class="fas fa-trash" style="font-size: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">No dragons found. <a href="{{ route('dragons.create') }}">Create one now</a></p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($dragons->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Dragon pagination">
            <ul class="pagination pagination-sm" style="gap: 8px;">
                <li class="page-item {{ $dragons->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $dragons->onFirstPage() ? '#' : $dragons->url(1) }}" aria-label="First page" style="border-radius: 999px; color: #2f6b4f; border: 1px solid #cfe8d8; background: #f7fcf8;">
                        First
                    </a>
                </li>

                <li class="page-item {{ $dragons->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $dragons->previousPageUrl() ?? '#' }}" style="border-radius: 999px; color: #2f6b4f; border: 1px solid #cfe8d8; background: #f7fcf8;">
                        «
                    </a>
                </li>

                @foreach ($dragons->getUrlRange(max(1, $dragons->currentPage() - 2), min($dragons->lastPage(), $dragons->currentPage() + 2)) as $page => $url)
                    <li class="page-item {{ $dragons->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}" style="border-radius: 999px; {{ $dragons->currentPage() == $page ? 'background: #4caf50; border-color: #4caf50; color: white;' : 'color: #2f6b4f; border: 1px solid #cfe8d8; background: #f7fcf8;' }}">
                            {{ $page }}
                        </a>
                    </li>
                @endforeach

                <li class="page-item {{ !$dragons->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $dragons->nextPageUrl() ?? '#' }}" style="border-radius: 999px; color: #2f6b4f; border: 1px solid #cfe8d8; background: #f7fcf8;">
                        »
                    </a>
                </li>

                <li class="page-item {{ !$dragons->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $dragons->hasMorePages() ? $dragons->url($dragons->lastPage()) : '#' }}" aria-label="Last page" style="border-radius: 999px; color: #2f6b4f; border: 1px solid #cfe8d8; background: #f7fcf8;">
                        Last
                    </a>
                </li>
            </ul>
        </nav>
    </div>
@endif

<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateModalLabel">Generate Dragon Scrape</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="generateFormGroup">
                    <label for="batchNumber">Nomor batch</label>
                    <input type="number" class="form-control" id="batchNumber" min="1" step="1" placeholder="1" required>
                    <small class="form-text text-muted">Batch 1: 0001-0100, batch 2: 0101-0200, dan seterusnya.</small>
                </div>

                <div id="generateProgress" class="d-none">
                    <div class="mb-2" id="generateStatus">Menunggu...</div>
                    <div class="progress">
                        <div id="generateProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="generateCloseBtn">Close</button>
                <button type="button" class="btn btn-primary" id="generateSubmitBtn" onclick="submitGenerate()">Generate</button>
            </div>
        </div>
    </div>
</div>

<script>
function submitGenerate() {
    const batchNumber = Number.parseInt(document.getElementById('batchNumber').value, 10);

    if (!Number.isInteger(batchNumber) || batchNumber < 1) {
        alert('Silakan isi nomor batch dengan bilangan bulat minimal 1.');
        return;
    }

    const startCode = String((batchNumber - 1) * 100 + 1).padStart(4, '0');
    const endCode = String(batchNumber * 100).padStart(4, '0');
    const parameter = startCode + '-' + endCode;
    const targetUrl = '/dragons/scrape?parameter=' + encodeURIComponent(parameter);

    const batchInput = document.getElementById('batchNumber');
    const submitBtn = document.getElementById('generateSubmitBtn');
    const closeBtn = document.getElementById('generateCloseBtn');
    const progressWrap = document.getElementById('generateProgress');
    const status = document.getElementById('generateStatus');

    // Disable inputs and show progress
    batchInput.disabled = true;
    submitBtn.disabled = true;
    closeBtn.disabled = true;
    progressWrap.classList.remove('d-none');
    status.textContent = 'Starting...';

    fetch(targetUrl, { headers: { 'Accept': 'application/json' } })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            status.textContent = data.message ?? 'Done';
        })
        .catch(err => {
            status.textContent = 'Error: ' + err.message;
        })
        .finally(() => {
            submitBtn.disabled = false;
            closeBtn.disabled = false;
            batchInput.disabled = false;
        });
}

function toggleSort() {
    const sortInput = document.getElementById('sortInput');
    const current = (sortInput.value || 'asc').toLowerCase();
    const next = current === 'asc' ? 'desc' : 'asc';
    sortInput.value = next;

    // submit the parent form to reload with new sort
    const form = sortInput.closest('form');
    if (form) form.submit();
}
</script>

@endsection
