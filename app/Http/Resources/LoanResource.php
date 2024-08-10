<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $bookResource = new BookResource($this->whenLoaded('book'));

        return [
            'key' => $this->key,
            'book' => $bookResource->toArray($request),
            'user' => $this->user->name,
            'loan_date' => $this->loan_date,
            'return_date' => $this->return_date,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
