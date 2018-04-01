<?php

namespace Loaf\Settings\Policies;

use Loaf\Base\Models\User;
use Loaf\Settings\Configuration\Section;

class SectionPolicy
{
    public function view(User $user, Section $section)
    {
        return $user->can('view settings section');
    }

    public function update(User $user, Section $section)
    {
        return $user->can('update settings section');
    }
}
