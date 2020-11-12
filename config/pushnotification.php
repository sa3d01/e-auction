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
        'apiKey' => 'AAAALmsArCs:APA91bEQKkd_iLnW18iSxqBjSdmKOu6YK4VAanFR1rdM6Jtg1mHQmvEYBNMIJV0aRkQAVTxbvvMT20jKmwudyo4qb9OYYG4Q2uubYqmltkA_Ew7DjRu0ExfPqCOn0ohhFlyCIjPghR3e',
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => 'secret', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true,
    ],
];
