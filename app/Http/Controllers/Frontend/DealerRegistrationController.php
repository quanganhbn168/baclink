<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DealerApplication;

class DealerRegistrationController extends Controller
{
    /**
     * Hiển thị form đăng ký đại lý
     */
    public function create(Request $request)
    {
        
        $sources = [
            'Google' => 'Tìm kiếm Google',
            'Social' => 'Mạng xã hội',
            'Friend' => 'Giới thiệu từ bạn bè',
            'Ads'    => 'Quảng cáo',
            'Other'  => 'Khác...',
        ];

        return view('frontend.dealers.create', compact('sources'));
    }

    /**
     * Nhận & lưu đăng ký
     */
    public function store(Request $request)
    {
        $sourceEnums = ['Google','Social','Friend','Ads','Other'];

        $data = $request->validate([
            'name'    => ['required','string','max:255'],                 
            'phone'   => ['required','string','max:30'],                  
            'email'   => ['required','email','max:255'],                  
            'company' => ['nullable','string','max:255'],                 
            'address' => ['required','string','max:255'],                 
            'source'  => ['required','in:'.implode(',', $sourceEnums)],   
            'message' => ['nullable','string','max:2000'],                
        ]);

        
        $data['ip']         = $request->ip();
        $data['user_agent'] = Str::limit((string) $request->userAgent(), 255, '');
        $data['status']     = 0;

        
        DealerApplication::create($data);

        return back()->with('success', 'Đăng ký thành công! Chúng tôi sẽ liên hệ lại sớm nhất.');
    }
}
