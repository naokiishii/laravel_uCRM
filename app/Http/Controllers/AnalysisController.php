<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2022-08-01';
        $endDate = '2022-08-31';

        //$period = Order::betweenDate($startDate, $endDate)->groupBy('id')->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')->orderBy('created_at')->paginate(50);
        //dd($period);
        //$subQuery = Order::betweenDate($startDate, $endDate)->where('status', true)->groupBy('id')->selectRaw('id, SUM(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m%d") as date');
        //$data = DB::table($subQuery)->groupBy('date')->selectRaw('date, sum(totalPerPurchase) as total')->get();
        //dd($data);

        //dd($data);
        return Inertia::render('Analysis');
    }

    public function decile() {
        $startDate = '2022-08-01';
        $endDate = '2022-08-31';
        // group by order ID
        $subQuery = Order::betweenDate($startDate, $endDate)->groupBy('id')->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase');

        // gorup by customer and sort by total
        $subQuery = DB::table($subQuery)->groupBy('customer_id')->selectRaw('customer_id, customer_name, sum(totalPerPurchase) as total')->orderBy('total', 'desc');

        // add index
        DB::statement('set @row_num = 0;');
        $subQuery = DB::table($subQuery)->selectRaw('@row_num := @row_num + 1 as row_num, customer_id, customer_name, total');

        // count all and get decil result
        $count = DB::table($subQuery)->count();
        $total = DB::table($subQuery)->selectRaw('sum(total) as total')->get();
        $total = $total[0]->total;
        $decile = ceil($count / 10);
        $bindValues = [];
        $tempValue = 0;
        for($i = 1; $i <= 10; $i++) {
            array_push($bindValues, 1 + $tempValue);
            $tempValue += $decile;
            array_push($bindValues, 1 + $tempValue);
        }

        // grouping
        DB::statement('set @row_num = 0;');
        $subQuery = DB::table($subQuery)->selectRaw("
            row_num,
            customer_id,
            customer_name,
            total,
            case
                when ? <= row_num and row_num < ? then 1
                when ? <= row_num and row_num < ? then 2
                when ? <= row_num and row_num < ? then 3
                when ? <= row_num and row_num < ? then 4
                when ? <= row_num and row_num < ? then 5
                when ? <= row_num and row_num < ? then 6
                when ? <= row_num and row_num < ? then 7
                when ? <= row_num and row_num < ? then 8
                when ? <= row_num and row_num < ? then 9
                when ? <= row_num and row_num < ? then 10
            end as decile
            ", $bindValues);
        
        // caculate per group
        $subQuery = DB::table($subQuery)->groupBy('decile')->selectRaw('decile, round(avg(total)) as average, sum(total) as totalPerGroup');

        // ratio
        DB::statement("set @total = ${total} ;");
        $data = DB::table($subQuery)->selectRaw('decile, average, totalPerGroup, round(100 * totalPerGroup / @total, 1) as totalRatio')->get();


    }
}
