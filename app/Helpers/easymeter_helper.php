<?php
function avatar($avatar)
{
    if ($avatar == 'none') {

        return base_url('assets/img/user.png');

    } elseif  (file_exists('uploads/avatars/'.$avatar)) {

        return base_url('uploads/avatars/' . $avatar);

    } else {

        // verificar qdo mostra
        return base_url('assets/img/sistema.png');
    }
}