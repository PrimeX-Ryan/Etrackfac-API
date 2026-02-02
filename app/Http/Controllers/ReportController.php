<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function reports()
    {
        return Submission::select(
            'users.department_id',
            DB::raw('count(*) as total'),
            DB::raw("sum(case when status='pending' then 1 else 0 end) as pending"),
            DB::raw("sum(case when status='approved' then 1 else 0 end) as approved"),
            DB::raw("sum(case when status='rejected' then 1 else 0 end) as rejected")
        )
            ->join('users', 'users.id', '=', 'submissions.faculty_id')
            ->groupBy('users.department_id')
            ->get();
    }
}
