@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">編輯產品/工單：{{ $product->product_code }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('manager.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">工件編號</label>
                    <input type="text" name="product_code" class="form-control" value="{{ $product->product_code }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">業務姓名</label>
                    <input type="text" name="sales_rep" class="form-control" value="{{ $product->sales_rep }}" required>
                </div>
            </div>

            <hr>
            <h6>修改製程流</h6>
            <div class="alert alert-warning small">
                注意：修改製程流會重置目前所有工序狀態為「待處理」。
            </div>
            
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
                        @foreach($product->processes as $proc)
                        <div class="d-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2 shadow-sm">
                            <span>{{ $proc->processType->name }}</span>
                            <input type="hidden" name="process_ids[]" value="{{ $proc->process_type_id }}">
                            <button type="button" class="btn-close btn-sm" onclick="this.parentElement.remove(); checkEmpty();"></button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('manager.products') }}" class="btn btn-secondary">取消</a>
                <button type="submit" class="btn btn-primary">儲存修改</button>
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
