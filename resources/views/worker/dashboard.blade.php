@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h3>工人工作看板</h3>
        <p class="text-muted small">點擊下方產品卡片，領取或回報工序進度。</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    @forelse($products as $product)
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">{{ $product->product_code }}</h5>
                <span class="badge @if($product->status == 'pending') bg-secondary @else bg-warning text-dark @endif">
                    {{ $product->status }}
                </span>
            </div>
            <div class="card-body">
                <p class="mb-2 text-muted">業務：{{ $product->sales_rep }}</p>
                <div class="list-group">
                    @foreach($product->processes as $proc)
                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 bg-light mb-2 rounded">
                        <div>
                            <span class="fw-bold">{{ $proc->sequence }}. {{ $proc->processType->name }}</span>
                            <div class="small">
                                @if($proc->status == 'completed')
                                    <span class="text-success">已完成 (由 {{ $proc->worker->name }})</span>
                                @elseif($proc->status == 'processing')
                                    <span class="text-warning">進行中 ({{ $proc->worker->name }})</span>
                                @else
                                    <span class="text-muted">待處理</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($proc->status == 'pending')
                                <form action="{{ route('worker.process.start', $proc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm px-3">領取</button>
                                </form>
                            @elseif($proc->status == 'processing' && $proc->worker_id == Auth::id())
                                <div class="d-flex gap-1">
                                    <form action="{{ route('worker.process.complete', $proc->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm px-3">完工</button>
                                    </form>
                                    <form action="{{ route('worker.process.rollback', $proc->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('要撤銷領取嗎？')">退回</button>
                                    </form>
                                </div>
                            @elseif($proc->status == 'completed' && $proc->worker_id == Auth::id())
                                <form action="{{ route('worker.process.rollback', $proc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('要撤銷完工嗎？')">回退狀態</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center py-5">
            目前沒有正在進行中的工單。
        </div>
    </div>
    @endforelse
</div>
@endsection
