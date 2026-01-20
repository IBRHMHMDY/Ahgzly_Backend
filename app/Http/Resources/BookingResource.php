<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_name' => $this->customer->restaurant->name ?? 'N/A', // علاقة متداخلة
            'date' => $this->booking_date,
            'time' => $this->start_at,
            'guests' => $this->guests_count,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
