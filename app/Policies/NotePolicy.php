<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function view(User $user, Note $note): bool
    {
        return (int) $note->created_by === (int) $user->id;
    }

    public function update(User $user, Note $note): bool
    {
        return (int) $note->created_by === (int) $user->id;
    }

    public function delete(User $user, Note $note): bool
    {
        return (int) $note->created_by === (int) $user->id;
    }
}
