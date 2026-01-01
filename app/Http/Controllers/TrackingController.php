<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TrackingController extends Controller
{
    
    public function index(Request $request)
    {
        $phone = trim((string) $request->query('phone', ''));
        $code  = ltrim(trim((string) $request->query('code', '')), '#');

        $orders = collect();
        $user   = null;

        if ($code !== '') {
            $order = Order::with(['orderItems.product','user','technician'])
                ->where('code', $code)->first();
            if ($order) $orders = collect([$order]);
        }

        if ($orders->isEmpty() && $phone !== '') {
            $orders = Order::with(['orderItems.product','user','technician'])
                ->where('customer_phone', $phone)
                ->orWhereHas('user', fn($q)=>$q->where('phone',$phone))
                ->orWhereHas('technician', fn($q)=>$q->where('phone',$phone))
                ->orderByDesc('created_at')
                ->get();

            $user = User::where('phone',$phone)->first();
        }

        return view('frontend.tracking.index', [
            'orders'         => $orders,
            'user'           => $user,
            'phone_searched' => $phone,
            'code_searched'  => $code,
        ]);
    }

    
    public function showByCode(string $code)
    {
        $order = Order::with(['orderItems.product','technician'])
            ->where('code', $code)->firstOrFail();

        return view('frontend.tracking.public', compact('order'));
    }

    
    public function qrByCode(string $code)
    {
        $url = route('warranty.code', $code);

    
        if (extension_loaded('imagick')) {
            $png = QrCode::format('png')->size(320)->margin(1)->generate($url);
            return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=2592000'); 
        }

    
        $svg = QrCode::format('svg')->size(320)->margin(0)->generate($url);
        return response($svg)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Cache-Control', 'public, max-age=2592000');
    }
}
