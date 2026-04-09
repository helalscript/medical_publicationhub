<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseModuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'mentor_id' => $this->mentor_id,
            'type' => $this->type,
            'title' => $this->title,
            'video' => $this->video,
            'attached_file' => $this->attached_file,
            'attached_text' => $this->attached_text,
            'document' => $this->document,
            'order_by' => $this->order_by,
            'durations' => $this->durations,
            'is_locked' => $this->is_locked,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'lessons' => $this->when(
                $this->relationLoaded('lessons'),
                fn () => CourseLessonResource::collection($this->lessons)->resolve()
            ),
        ];
    }
}
