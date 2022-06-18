{{-- hiển thị danh sách quản trị hệ thống --}}
@extends('layouts.admin')
@section('content')
    <div id="content" class="container-fluid">
        <div class="card">
            {{-- xuất thông báo thêm dl thành công --}}
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
                <h5 class="m-0 ">Danh sách thành viên</h5>
                <div class="form-search form-inline">
                    <form action="#">
                        {{-- viết cn tìm kiếm người dùng, hiển thị lại giá trị keyword trong trường value --}}
                        <input type="text" class="form-control form-search" name="keyword"
                            value="{{ request()->input('keyword') }}" placeholder="Tìm kiếm">
                        <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="analytic">
                    {{-- xd đường link thống kê user theo trạng thái --}}
                    {{-- ht lấy request hiện tại nối thêm 1 tham số nữa --}}
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="text-primary">Kích hoạt
                        <span class="text-muted">({{ $count[0] }})</span></a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'trash']) }}" class="text-primary">Vô hiệu hóa
                        <span class="text-muted">({{ $count[1] }})</span></a>
                </div>
                <form action="{{ url('admin/user/action') }}" method="">
                    <div class="form-action form-inline py-3">
                        <select class="form-control mr-1" name="act" id="">
                            <option>Chọn</option>
                            @foreach ($list_act as $k => $act)
                                <option value="{{ $k }}">{{ $act }}</option>
                            @endforeach
                            {{-- <option value="delete">Xóa</option>
                            <option value="restore">Khôi phục</option> --}}
                        </select>
                        <input type="submit" name="btn-search" value="Áp dụng" class="btn btn-primary">
                    </div>
                    <table class="table table-striped table-checkall">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" name="checkall">
                                </th>
                                <th scope="col">#</th>
                                <th scope="col">Họ tên</th>
                                <th scope="col">Email</th>
                                <th scope="col">Quyền</th>
                                <th scope="col">Ngày tạo</th>
                                <th scope="col">Tác vụ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($users->total() > 0)
                                @php
                                    $t = 0;
                                @endphp
                                @foreach ($users as $user)
                                    @php
                                        $t++;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{-- lấy ds id những thằng chúng ta tương tác --}}
                                            <input type="checkbox" name="list_check[]" value="{{ $user->id }}">
                                        </td>
                                        <td scope="row">{{ $t }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>Admintrator</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>
                                            {{-- cập nhật tt người dùng --}}
                                            <a href="{{ route('user.edit', $user->id) }}"
                                                class="btn btn-success btn-sm rounded-0 text-white" title="Edit"><i
                                                    class="fa fa-edit"></i></a>
                                            {{-- hiển thị nút xóa --}}
                                            @if (Auth::id() != $user->id)
                                                {{-- click vào nút xóa hiển thị xác nhận --}}
                                                <a href="{{ route('delete_user', $user->id) }}"
                                                    onclick="return confirm('Bạn có chắc chắn xóa bản ghi này?')"
                                                    class="btn btn-danger btn-sm rounded-0 text-white" type="button"
                                                    data-toggle="tooltip" data-placement="top" title="Delete"><i
                                                        class="fa fa-trash"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="bg-white">
                                        Không tìm thấy bản ghi
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </form>
                {{-- xử lý phân trang --}}
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
