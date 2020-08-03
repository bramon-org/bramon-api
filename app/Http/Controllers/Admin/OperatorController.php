<?php

namespace App\Http\Controllers\Admin;

class OperatorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $operators = [];

        return response()->json($operators);
    }
}
