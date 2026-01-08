@csrf
<div class="mb-3">
    <label for="name" class="form-label">Tên vai trò</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $role->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Gán quyền</label>
    @error('permissions')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
    <div class="row">
        @foreach ($permissions as $groupName => $groupPermissions)
            <div class="col-md-4 mb-3">
                <h5>{{ ucfirst($groupName) }}</h5>
                @foreach ($groupPermissions as $permission)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]"
                               value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                               @if(isset($rolePermissions) && in_array($permission->name, $rolePermissions)) checked @endif>
                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ isset($role) ? 'Cập nhật' : 'Tạo mới' }}</button>
<a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Hủy</a>
