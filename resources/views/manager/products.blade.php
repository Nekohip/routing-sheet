@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">產品管理 (工單上架)</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">新工單上架</button>
    </div>
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>工件編號</th>
                    <th>業務</th>
                    <th>狀態</th>
                    <th>製程流</th>
                    <th>建立時間</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td><strong>{{ $product->product_code }}</strong></td>
                    <td>{{ $product->sales_rep }}</td>
                    <td>
                        <span class="badge @if($product->status == 'pending') bg-secondary @elseif($product->status == 'processing') bg-warning text-dark @else bg-success @endif">
                            {{ $product->status }}
                        </span>
                    </td>
                    <td>
                        @foreach($product->processes as $proc)
                        <span class="badge border text-dark @if($proc->status == 'completed') bg-light text-muted @endif">
                            {{ $proc->sequence }}. {{ $proc->processType->name }}
                        </span>
                        @if(!$loop->last) <i class="bi bi-arrow-right"></i> → @endif
                        @endforeach
                    </td>
                    <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('manager.products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm">編輯</a>
                        <form action="{{ route('manager.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('確定刪除此工單？')">刪除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('manager.products.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">產品上架</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">工件編號</label>
                            <input type="text" name="product_code" class="form-control" placeholder="例如：S-20240319" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">業務姓名</label>
                            <input type="text" name="sales_rep" class="form-control" placeholder="例如：林業務" required>
                        </div>
                    </div>

                    <hr>
                    <h6>配置製程流</h6>
                    <p class="text-muted small">請按順序點擊左側選項加入右側流。可重複選取，可拖曳排序（目前僅依點擊順序）。</p>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="list-group" id="available-processes">
                                @foreach($processTypes as $type)
                                <button type="button" class="list-group-item list-group-item-action" 
                                    onclick="addProcess('{{ $type->id }}', '{{ $type->name }}')">
                                    + {{ $type->name }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-2 text-center align-self-center">
                            <i class="fs-2 text-muted">⟹</i>
                        </div>
                        <div class="col-md-5">
                            <div class="border rounded p-2 bg-light" id="selected-processes" style="min-height: 200px;">
                                <div id="empty-hint" class="text-center text-muted mt-5">尚未選擇製程</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">確認上架</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function addProcess(id, name) {
    const container = document.getElementById('selected-processes');
    const hint = document.getElementById('empty-hint');
    if (hint) hint.remove();

    const div = document.createElement('div');
    div.className = 'd-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2 shadow-sm';
    div.innerHTML = `
        <span>${name}</span>
        <input type="hidden" name="process_ids[]" value="${id}">
        <button type="button" class="btn-close btn-sm" onclick="this.parentElement.remove(); checkEmpty();"></button>
    `;
    container.appendChild(div);
}

function checkEmpty() {
    const container = document.getElementById('selected-processes');
    if (container.children.length === 0) {
        container.innerHTML = '<div id="empty-hint" class="text-center text-muted mt-5">尚未選擇製程</div>';
    }
}
</script>
@endsection
