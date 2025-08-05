<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    /**
     * Menampilkan halaman bantuan (panduan dan FAQ).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.helper');
    }
}
