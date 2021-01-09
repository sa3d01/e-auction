<?php
/**
 * @see https://github.com/Edujugon/PushNotification
 */

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAiT_KxCU:APA91bFk2yOLLeyJsffwiarrQ8NylCQaHDP1wDZfB6R4pk6eXVDVdIqBrHkn7YpreESYVgfBODuwzuosuPYlohnMxNCIcBNMLFbCojEw0vZqohNHW-yIMKiltXZtM8nApEmcHxpdEQ7G',
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => 'secret', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true,
    ],
];
