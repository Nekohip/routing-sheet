@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">製程選項管理</h5>
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createProcessTypeModal">新增選項</button>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>名稱</th>
                    <th>是否預設</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processTypes as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>
                        @if($type->is_default)
                        <span class="badge bg-secondary text-white">系統預設</span>
                        @else
                        <span class="badge bg-info text-dark">自定義</span>
                        @endif
                    </td>
                    <td>
                        @if(!$type->is_default)
                        <form action="{{ route('manager.process_types.destroy', $type->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('確定刪除？')">刪除</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createProcessTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('manager.process_types.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">新增製程選項</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">製程名稱</label>
                        <input type="text" name="name" class="form-control" placeholder="例如：熱處理、噴漆..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-success">確認新增</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
