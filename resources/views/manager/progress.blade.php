@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">查看工單進度</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 15%;">工件編號 / 業務</th>
                    <th style="width: 10%;">總進度</th>
                    <th>製程狀態詳情</th>
                    <th style="width: 15%;">建立時間</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                @php
                    $total = $product->processes->count();
                    $completed = $product->processes->where('status', 'completed')->count();
                    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $product->product_code }}</strong><br>
                        <small class="text-muted">業務: {{ $product->sales_rep }}</small>
                    </td>
                    <td class="text-center">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar @if($percent == 100) bg-success @else bg-primary @endif" 
                                 role="progressbar" style="width: {{ $percent }}%;" 
                                 aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $percent }}%
                            </div>
                        </div>
                        <small class="text-muted">{{ $completed }} / {{ $total }} 已完成</small>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->processes as $proc)
                            <div class="card shadow-sm @if($proc->status == 'completed') border-success @elseif($proc->status == 'processing') border-warning @else border-secondary @endif" 
                                 style="min-width: 120px; font-size: 0.85rem;">
                                <div class="card-header p-1 text-center @if($proc->status == 'completed') bg-success text-white @elseif($proc->status == 'processing') bg-warning text-dark @else bg-light @endif">
                                    {{ $proc->sequence }}. {{ $proc->processType->name }}
                                </div>
                                <div class="card-body p-2 text-center">
                                    @if($proc->status == 'completed')
                                        <span class="badge bg-success">已完成</span><br>
                                        <small>{{ $proc->worker->name ?? '未知' }}</small><br>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $proc->completed_at ? \Carbon\Carbon::parse($proc->completed_at)->format('m/d H:i') : '' }}</small>
                                    @elseif($proc->status == 'processing')
                                        <span class="badge bg-warning text-dark">進行中</span><br>
                                        <small>{{ $proc->worker->name ?? '未知' }}</small>
                                    @else
                                        <span class="badge bg-secondary">待處理</span>
                                    @endif
                                </div>
                            </div>
                            @if(!$loop->last)
                            <div class="align-self-center">
                                <i class="bi bi-chevron-right text-muted">→</i>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </td>
                    <td class="text-center text-muted small">
                        {{ $product->created_at->format('Y-m-d') }}<br>
                        {{ $product->created_at->format('H:i:s') }}
                    </td>
                </tr>
                @endforeach

                @if($products->isEmpty())
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">目前尚無任何工單</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
