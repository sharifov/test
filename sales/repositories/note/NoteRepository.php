<?php

namespace sales\repositories\note;

use common\models\Note;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class NoteRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Note
     */
    public function get($id): Note
    {
        if ($note = Note::findOne($id)) {
            return $note;
        }
        throw new NotFoundException('Note is not found');
    }


    /**
     * @param Note $note
     * @return int
     */
    public function save(Note $note): int
    {
        if (!$note->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($note->releaseEvents());
        return $note->id;
    }

    /**
     * @param Note $note
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Note $note): void
    {
        if (!$note->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($note->releaseEvents());
    }
}