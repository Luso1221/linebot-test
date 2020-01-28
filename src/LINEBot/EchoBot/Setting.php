<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace LINE\LINEBot\EchoBot;

class Setting
{
    public static function getSetting()
    {
        $channel_token = 'dJhI5iaBe5nWYbuszbHiKqIyiHWW3FgZgI3Sqhufos4jICridjv8W
        7ZINfysHwxfqdJj3sAnuQSaygsyw0iZxVLLD/9xcHJJHxruEGh76GA9BpeESOHa/
        d74zd+YVvOK75fn4SYcT7wvcEtoaiz1ggdB04t89/1O/w1cDnyilFU=';
        $channel_secret = 'bfbe7b86b9ddd70e8ddebdf084573f28';
        return [
            'settings' => [
                'displayErrorDetails' => true, // set to false in production

                'logger' => [
                    'name' => 'slim-app',
                    'path' => __DIR__ . '/../../../logs/app.log',
                ],

                'bot' => [
                    'channelToken' => getenv('LINEBOT_CHANNEL_TOKEN') ?: $channel_token,
                    'channelSecret' => getenv('LINEBOT_CHANNEL_SECRET') ?: $channel_secret,
                ],

                'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
            ],
        ];
    }
}