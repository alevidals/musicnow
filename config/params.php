<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'smtpUsername' => 'musicnowproyect@gmail.com',
    'defaultImgProfile' => 'https://firebasestorage.googleapis.com/v0/b/song-test-103af.appspot.com/o/images%2Fall%2Fblank-profile.png?alt=media',
    'defaultImgProfile' => getenv('url_prefix') . 'images%2Fall%2Fblank-profile.png' . getenv('url_suffix'),
    'bsVersion' => '4.x',
];
