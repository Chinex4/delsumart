@props(['user'])
<span {{ $attributes->class('avatar') }} aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
