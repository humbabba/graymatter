<?php

use Illuminate\Foundation\Auth\User;

if (!function_exists('getVersion')) {
    /**
     * @return \Illuminate\Config\Repository|int|mixed
     */
    function getVersion()
    {
        switch (config('app.env')) {
            case 'production':
                return config('app.copyright.version');
                break;
            default:
                return time();
                break;
        }
    }
}

if (!function_exists('getCopyrightYear')) {
    /**
     * @return false|string
     */
    function getCopyrightYear()
    {
        $currentYear = date('Y');
        $copyrightYear = config('app.copyright.year');
        if ($copyrightYear < $currentYear) {
            return $copyrightYear . ' - ' . $currentYear;
        }
        return $currentYear;
    }
}

if (!function_exists('getGravatarSrc')) {
    /**
     * @param $email
     * @param int $size
     * @return string
     */
    function getGravatarSrc($email,$size = 200)
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/$hash?d=identicon&s=$size";
    }
}

if(!function_exists('renderLinkedUserDisplayName')) {
    /**
     * @param $id
     * @param bool $withGravatar
     * @return string
     */
    function renderLinkedUserDisplayName($id, $withGravatar = true)
    {
        $output = '';
        $user = User::find($id);
        if(!is_null($user)) {
            $profileLink = route('users.profile',$user->id);
            if($withGravatar) {
                $gravatarSrc = getGravatarSrc($user->email,14);
                $output .= "<a href='$profileLink'><img class='display-name-gravatar' src='$gravatarSrc' /></a> ";
            }
            $output .= "<a href='$profileLink'>{$user->name}</a>";
        }
        return $output;
    }
}

if(!function_exists('quotesWrap')) {
    /**
     * @param $input
     * @return string
     */
    function quotesWrap($input) {
        return '"' . str_replace('"','""', $input) . '"';
    }
}
