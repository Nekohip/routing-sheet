@extends('layouts.app')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">使用者管理</h5>
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">新增使用者</button>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th>帳號</th>
                    <th>姓名</th>
                    <th>角色</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->name }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'manager' ? 'bg-danger' : 'bg-success' }}">
                            {{ $user->role === 'manager' ? '主管' : '工人' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm">編輯</button>
                        <button class="btn btn-danger btn-sm">刪除</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('manager.users.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">新增使用者</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">帳號</label>
                        <input type="text" name="username" class="form-control" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">姓名</label>
                        <input type="text" name="name" class="form-control" required autocomplete="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密碼</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">權限角色</label>
                        <select name="role" class="form-select" required>
                            <option value="worker">工人 (Worker)</option>
                            <option value="manager">主管 (Manager)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">確認新增</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
