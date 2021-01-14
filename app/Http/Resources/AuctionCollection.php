<?php

namespace App\Http\Resources;

use App\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuctionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];
        foreach ($this as $obj) {
            $arr['id'] = (int)$obj->id;
            $items = Item::whereIn('id', $obj->items)->get();
            $arr['start_date'] = $obj->start_date;
            $arr['start_date_text'] = Carbon::createFromTimestamp($obj->start_date)->format('Y-m-d h:i:s A');
            $arr['end_date'] = $obj->more_details ? $obj->more_details['end_date'] : '';
            if (Carbon::createFromTimestamp($obj->more_details['end_date']) < Carbon::now()) {
                $arr['auction_status'] = 'expired';
            } elseif ((Carbon::createFromTimestamp($obj->start_date) <= Carbon::now()) && ($obj->more_details['end_date']) >= Carbon::now()) {
                $arr['auction_status'] = 'live';
            } else {
                $arr['auction_status'] = 'soon';
            }
            $images = [];
            foreach ($items as $item) {
                $images[] = $item->images[0];
            }
            $arr['images'] = $images;
            $data[] = $arr;
        }
        return $data;
    }
}
