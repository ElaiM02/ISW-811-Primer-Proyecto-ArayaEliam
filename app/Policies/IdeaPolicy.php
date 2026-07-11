<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    /**
     * ¿Puede el usuario trabajar con (ver/editar/borrar) esta idea?
     * Solo el creador de la idea.
     */
    public function workWith(User $user, Idea $idea): bool
    {
        return $user->is($idea->user);
    }
}
