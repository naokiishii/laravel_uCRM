<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RFMService
{
  public static function rfm($subQuery, $rfmPrms)
  {
        // RFM analysic
        // 1. group by purchase id
        $subQuery = $subQuery->groupBy('id')->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

        // 2. group by customer id
        $subQuery = DB::table($subQuery)->groupBy('customer_id')->selectRaw('customer_id, customer_name, max(created_at) as recentDate, datediff(now(), max(created_at)) as recency, count(customer_id) as frequency, sum(totalPerPurchase) as monetary');

        // 4. calculate RFM rank
        //$rfmPrms = [14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000];
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
        //Log::debug($subQuery->get());

        // 5. count per rank
        $totals = DB::table($subQuery)->count();
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

        //Log::debug($rCount);

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

        return [
          $data,
          $totals,
          $eachCount
        ];
  }
}
