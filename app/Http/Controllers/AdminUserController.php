<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    //
    // function list(Request $request)
    // {
    //     // lấy thông tin trên url xử lý 
    //     // return $request->input('keyword');
    //     // cn tìm kiếm người dùng
    //     $keyword = "";
    //     if ($request->input('keyword')) {
    //         $keyword = $request->input('keyword');
    //     }
    //     //hiển thị những thằng đã xóa tạm thời
    //     // $users = User::withTrashed()->where('name', 'LIKE', "%{$keyword}%")->paginate(10);

    //     $users = User::where('name', 'LIKE', "%{$keyword}%")->paginate(10);
    //     // truy xuất vào tổng số bản ghi lấy được
    //     // dd($users->total());


    //     // $users = User::all();
    //     //xử lý phân trang bao nhiêu user trên 1 bản ghi
    //     // $users = User::paginate(10);
    //     // return $users;
    //     // thiết lập truyền dl sang bên list 
    //     return view('admin.user.list', compact('users'));
    // }

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            session(['module_active' => 'user']);
            return $next($request);
        });
    }
    // hiển thị danh sách thống kê user theo trạng thái 
    function list(Request $request)
    {
        $status = $request->input('status');
        //danh sách những tùy chọn trong select box
        $list_act = [
            'delete' => 'Xóa tạm thời',
        ];

        if ($status == 'trash') {
            $list_act = [
                'restore' => 'Khôi phục',
                'forceDelete' => 'Xóa vĩnh viễn'
            ];
            $users = User::onlyTrashed()->paginate(10);
        } else {
            $keyword = "";
            if ($request->input('keyword')) {
                $keyword = $request->input('keyword');
            }
            $users = User::where('name', 'LIKE', "%{$keyword}%")->paginate(10);
        }
        //lấy tổng số bản ghi không kể phần tử bên trong thùng rác
        $count_user_active = User::count();
        //lấy tổng số bản ghi đang xóa tạm thời
        $count_user_trash = User::onlyTrashed()->count();

        $count = [$count_user_active, $count_user_trash];
        return view('admin.user.list', compact('users', 'count', 'list_act'));
    }

    function add(Request $request)
    // function add()
    {
        // kiểm tra người dùng click vào nút submit hay chưa s
        // if ($request->input('btn-add')) {
        //     return $request->input();
        // }
        return view('admin.user.add');
    }

    // nơi lưu trữ xử lý dl 
    function store(Request $request)
    {
        // if ($request->input('btn-add')) {
        //     return $request->input();
        // }

        // validation 
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'required' => ':attribute không được để trống',
                'min' => ':attribute có độ dài ít nhất :min ký tự',
                'max' => ':attribute có độ dài tối đa :max ký tự',
                'confirmed' => 'Xác nhận mật khẩu không thành công',
            ],
            [
                'name' => 'Tên người dùng',
                'email' => 'Email',
                'password' => 'Mật khẩu'
            ]
        );
        // return $request->all(); 

        //dl ok thì thêm user vào trong hệ thống hash hàm mã hóa trong laravel bảo mật hơn md5
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            // 'password' => $request->input('password')
        ]);

        //sau khi thêm dl thành công 
        return redirect('admin/user/list')->with('status', 'Đã thêm thành viên thành công');
    }

    function delete($id)
    {
        //kiểm tra id cần xóa có phải id đang login hay không
        if (Auth::id() != $id) {
            $user = User::find($id);
            $user->delete();

            return redirect('admin/user/list')->with('status', 'Đã xóa thành viên thành công');
        } else {
            return redirect('admin/user/list')->with('status', 'Bạn không thể tự xóa mình ra khỏi hệ thống');
        }
    }
    function action(Request $request)
    {
        // lấy danh sách những phần tử chúng ta đã check 
        $list_check = $request->input('list_check');
        // kiểm tra nó có tồn tại không 
        if ($list_check) {
            // return $request->input('list_check');
            foreach ($list_check as $k => $id) {
                //kiểm tra id đăng nhập có bằng id nào trong list check không nếu bằng thì unset phần tử đấy ra khỏi mảng
                //loại bỏ id đang đăng nhập
                if (Auth::id() == $id) {
                    unset($list_check[$k]);
                }
            }
            if (!empty($list_check)) {
                // kiểm tra action là gì 
                $act = $request->input('act');
                if ($act == 'delete') {
                    User::destroy($list_check);
                    return redirect('admin/user/list')->with('status', 'Bạn đã xóa thành công');
                }
                if ($act == 'restore') {
                    User::withTrashed()
                        ->where('id', $list_check)
                        ->restore();
                    return redirect('admin/user/list')->with('status', 'Bạn đã khôi phục thành công');
                }
                //xóa vĩnh viễn user ra khỏi hệ thống
                if ($act == 'forceDelete') {
                    User::withTrashed()
                        ->where('id', $list_check)
                        ->forceDelete();
                    return redirect('admin/user/list')->with('status', 'Bạn đã xóa vĩnh viễn thành công');
                }
            }
            return redirect('admin/user/list')->with('status', 'Bạn không thể thao tác trên tài khoản của bạn');
        } else {
            return redirect('admin/user/list')->with('status', 'Bạn cần chọn phần tử cần thực thi');
        }
    }
    public function edit($id)
    {
        //
        $user = User::find($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        //
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'required' => ':attribute không được để trống',
                'min' => ':attribute có độ dài ít nhất :min ký tự',
                'max' => ':attribute có độ dài tối đa :max ký tự',
                'confirmed' => 'Xác nhận mật khẩu không thành công',
            ],
            [
                'name' => 'Tên người dùng',
                'password' => 'Mật khẩu'
            ]
        );
        // return $request->all(); 

        User::where('id', $id)([
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect('admin/user/list')->with('status', 'Đã thêm cập nhật thành công');
    }
}
