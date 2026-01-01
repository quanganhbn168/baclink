<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function index()
    {
        // Simply return the view. The view will load the media-picker JS
        return view('admin.file-manager.index');
    }
}
