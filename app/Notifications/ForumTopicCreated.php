<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ForumTopicCreated extends Notification
{
    public function __construct(
        public string $courseName,
        public string $topicTitle,
        public string $authorName,
        public string $url,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'forum_topic_created',
            'icon'       => 'forum',
            'title'      => 'Nuevo tema en el foro',
            'body'       => $this->authorName . ' publicó "' . $this->topicTitle . '" en ' . $this->courseName . '.',
            'course_name'=> $this->courseName,
            'topic_title'=> $this->topicTitle,
            'url'        => $this->url,
        ];
    }
}
