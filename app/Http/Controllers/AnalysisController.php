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
        $startDate = '2023-05-01';
        $endDate = '2023-09-31';

        //$period = Order::betweenDate($startDate, $endDate)->groupBy('id')->selectRaw('id, sum(subtotal) as total, customer_name, status, created_at')->orderBy('created_at')->paginate(50);
        //dd($period);
        //$subQuery = Order::betweenDate($startDate, $endDate)->where('status', true)->groupBy('id')->selectRaw('id, SUM(subtotal) as totalPerPurchase, DATE_FORMAT(created_at, "%Y%m%d") as date');
        //$data = DB::table($subQuery)->groupBy('date')->selectRaw('date, sum(totalPerPurchase) as total')->get();
        //dd($data);

        //dd($data);

        $this->rfm();

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

    public function rfm() {
        $startDate = '2023-08-28';
        $endDate = '2023-09-31';

        // RFM analysic
        // 1. group by purchase id
        $subQuery = Order::betweenDate($startDate, $endDate)->groupBy('id')->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

        // 2. group by customer id
        $subQuery = DB::table($subQuery)->groupBy('customer_id')->selectRaw('customer_id, customer_name, max(created_at) as recentDate, datediff(now(), max(created_at)) as recency, count(customer_id) as frequency, sum(totalPerPurchase) as monetary');

        // 4. calculate RFM rank
        $rfmPrms = [14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000];
        $subQuery = DB::table($subQuery)->selectRaw('customer_id, customer_name, recentDate, recency, frequency, monetary,
            case
                when recency < ? then 5
                when recency < ? then 4
                when recency < ? then 3
                when recency < ? then 2
                else 1 end as r,
            case
                when ? <= frequency then 5
                when ? <= frequency then 4
                when ? <= frequency then 3
                when ? <= frequency then 2
                else 1 end as f,
            case
                when ? <= monetary then 5
                when ? <= monetary then 4
                when ? <= monetary then 3
                when ? <= monetary then 2
                else 1 end as m
            ', $rfmPrms);

        /*
        $subQuery = DB::table($subQuery)->selectRaw('customer_id, customer_name, recentDate, recency, frequency, monetary,
            case
                when recency < 14 then 5
                when recency < 28 then 4
                when recency < 60 then 3
                when recency < 90 then 2
                else 1 end as r,
            case
                when 7 <= frequency then 5
                when 5 <= frequency then 4
                when 3 <= frequency then 3
                when 2 <= frequency then 2
                else 1 end as f,
            case
                when 300000 <= monetary then 5
                when 200000 <= monetary then 4
                when 100000 <= monetary then 3
                when 30000 <= monetary then 2
                else 1 end as m
            ');
        */
        //dd($subQuery->get());

        // 5. count per rank
        $total = DB::table($subQuery)->count();
        $rCount = DB::table($subQuery)
            ->rightJoin('ranks', 'ranks.rank', '=', 'r')
            ->groupBy('rank')
            ->selectRaw('rank as r, count(r)')
            ->orderBy('r', 'desc')
            ->pluck('count(r)');
        $fCount = DB::table($subQuery)
            ->rightJoin('ranks', 'ranks.rank', '=', 'f')
            ->groupBy('rank')
            ->selectRaw('rank as f, count(f)')
            ->orderBy('f', 'desc')
            ->pluck('count(f)');
        $mCount = DB::table($subQuery)
            ->rightJoin('ranks', 'ranks.rank', '=', 'm')
            ->groupBy('rank')
            ->selectRaw('rank as m, count(m)')
            ->orderBy('m', 'desc')
            ->pluck('count(m)');
        //dd($total, $rCount, $fCount, $mCount);
        //dd($subQuery->get());
        $eachCount = [];
        $rank = 5;

        for($i = 0; $i < 5; $i++) {
            array_push($eachCount, [
                'rank' => $rank,
                'r' => $rCount[$i],
                'f' => $fCount[$i],
                'm' => $mCount[$i],
            ]);
            $rank--;
        }

        //dd($total, $eachCount, $rCount, $fCount, $mCount);

        // 6. display by R and F
        $data = DB::table($subQuery)
            ->rightJoin('ranks', 'ranks.rank', '=', 'r')
            ->groupBy('rank')
            ->selectRaw('concat("r_", rank) as rRank,
                count(case when f = 5 then 1 end) as f_5,
                count(case when f = 4 then 1 end) as f_4,
                count(case when f = 3 then 1 end) as f_3,
                count(case when f = 2 then 1 end) as f_2,
                count(case when f = 1 then 1 end) as f_1
            ')
            ->orderBy('rRank', 'desc')->get();

        //dd($data);


    }
}
