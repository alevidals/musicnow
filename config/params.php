<?php

use kartik\datecontrol\Module;

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'smtpUsername' => 'musicnowproyect@gmail.com',
    'defaultImgProfile' => 'https://firebasestorage.googleapis.com/v0/b/song-test-103af.appspot.com/o/images%2Fall%2Fblank-profile.png?alt=media',
    'defaultImgProfile' => getenv('url_prefix') . 'images%2Fall%2Fblank-profile.png' . getenv('url_suffix'),
    'bsVersion' => '4.x',
    'dateControlDisplay' => [
        Module::FORMAT_DATE => 'php:d-m-Y',
        Module::FORMAT_TIME => 'php:H:i:s',
        Module::FORMAT_DATETIME => 'php:d-m-Y H:i:s',
    ],
    'dateControlSave' => [
        Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
        Module::FORMAT_TIME => 'php:H:i:s',
        Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
    ],
];
