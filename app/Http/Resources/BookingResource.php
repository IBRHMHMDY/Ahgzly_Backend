<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => $this->restaurant?->name ?? 'N/A',
            'date' => optional($this->booking_date)->format('Y-m-d') ?? $this->booking_date,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'guests_count' => $this->guests_count,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
