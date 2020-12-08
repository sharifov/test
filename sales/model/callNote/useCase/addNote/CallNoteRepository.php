<?php

namespace sales\model\callNote\useCase\addNote;

use sales\model\callNote\entity\CallNote;

class CallNoteRepository
{
    /**
     * @param int $callId
     * @param string $note
     */
    public function add(int $callId, string $note): void
    {
        $callNote = CallNote::create($callId, $note);
        $this->save($callNote);
    }

    /**
     * @param CallNote $callNote
     * @return CallNote
     */
    public function save(CallNote $callNote): CallNote
    {
        if (!$callNote->save()) {
            throw new \RuntimeException($callNote->getErrorSummary(false)[0]);
        }
        return $callNote;
    }
}
