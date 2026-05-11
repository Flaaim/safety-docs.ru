<?php

declare(strict_types=1);

return [
  'config' => [
      'auth' => [
          'login' => getenv('AUTH_LOGIN'),
          'password' => getenv('AUTH_PASSWORD'),
          'api_token' => base64_encode(getenv('AUTH_LOGIN') . ':' . getenv('AUTH_PASSWORD')),
      ]
  ]
];
