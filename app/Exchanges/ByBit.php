<?php


namespace App\Exchanges;


use App\Http\Controllers\BaseController;
use App\Interfaces\Exchange as ExI;
use App\Models\Symbol;

class Bybit extends BaseController implements ExI
{

    public function ticker($timeFrame)
    {

        $data = $this->sendRequest($this->exchangeId, __METHOD__);

        if (is_null($data)) return [];

        $data = $data->result;

        $query = [];

        foreach ($data as $item) {

            $symbol = $this->checkSymbol($item->symbol);
            $last = $item->last_price;

            if (empty($symbol)) continue;

            $query[] = [
                'exchange_id' => $this->exchangeId,
                'name'        => $symbol,
                'last'        => $last,
                'time_frame'  => $timeFrame,
                'created_at'  => now(),
                'updated_at'  => now()
            ];
        }

        Symbol::query()->insert($query);
    }
}
