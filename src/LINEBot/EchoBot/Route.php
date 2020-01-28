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

use PDO;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;

class Route
{
    public $messages = array();
    public function register(\Slim\App $app)
    {
        $app->post('/callback', function (\Slim\Http\Request $req, \Slim\Http\Response $res)  {
            /** @var \LINE\LINEBot $bot */
            $bot = $this->bot;
            /** @var \Monolog\Logger $logger */
            $logger = $this->logger;

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            // error_log(print_r($req->getBody(),true));
            if (empty($signature)) {
                return $res->withStatus(400, 'Bad Request');
            }

            // Check request with signature and parse request
            try {
                error_log("Try");
                $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
            } catch (InvalidSignatureException $e) {

                error_log("Error",$e);
                return $res->withStatus(400, 'Invalid signature');
            } catch (InvalidEventRequestException $e) {
                error_log("Error",$e);
                return $res->withStatus(400, "Invalid event request");
            }

            foreach ($events as $event) {

                if (!($event instanceof MessageEvent)) {
                    $logger->info('Non message event has come');
                    continue;
                }

                if (($event instanceof ImageMessage)) {
                    error_log(print_r($event->getContentProvider(),true));
                    continue;
                }
                if (($event instanceof TextMessage)) {
                    $test = 0;

                    $resp = $bot->getProfile($event->getUserId());
                    $name = '';

                    if ($resp->isSucceeded()) {
                        $profile = $resp->getJSONDecodedBody();
                        $displayName = $profile['displayName'];
                        $name =$displayName;
                    } else {
                        error_log(print_r($resp->getJSONDecodedBody()));
                    }
                    $text = $event->getText();
                    $timestamp = date("Y-m-d H:i:s", substr($event->getTimestamp(), 0, -3));

                    try {
                        $db = $this->db;
                        $sql = $db->prepare("INSERT INTO messages (id, name, text,timestamp) VALUES (:id, :name, :text, :timestamp)");
                        // use exec() because no results are returned
                        $sql->bindParam(':id', $test, PDO::PARAM_INT);
                        $sql->bindParam(':name', $name, PDO::PARAM_STR, 12);
                        $sql->bindParam(':text', $text, PDO::PARAM_STR, 12);
                        $sql->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
                        $sql->execute();
                        }
                    catch(PDOException $e)
                        {
                            error_log( $sql . "<br>" . $e->getMessage());
                        }
                    continue;
                }


                // $resp = $bot->replyText($event->getReplyToken(), $replyText);
                // error_log($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
            }

            // error_log(print_r($events,true));
            $res->write('OK');
            // error_log(print_r($res,true));
            return $res;
        });
    }
}
