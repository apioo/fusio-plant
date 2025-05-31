<?php
/*
 * This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
 * Fusio Plant is a server control panel to easily self-host apps on your server.
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Service;

use App\Exception\ProcessTimeoutException;
use App\Model;
use App\Model\DockerImages;
use App\Model\Message;
use App\Service\System\SystemExecutor;
use PSX\Http\Exception as StatusCode;

readonly class System
{
    public function __construct(
        private SystemExecutor $worker,
        private JsonParser $jsonParser
    ) {
    }

    public function certbot(Model\CertbotRequest $request): Message
    {
        try {
            $output = $this->worker->certbot($request);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not execute certbot', previous: $e);
        }

        return $this->newMessage('Obtained certificate successfully', $output);
    }

    public function images(): DockerImages
    {
        try {
            $output = $this->worker->images();
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not execute ps', previous: $e);
        }

        $lines = $this->jsonParser->parseLines($output, Model\DockerImage::class);

        $collection = new Model\DockerImages();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);
        return $collection;
    }

    public function login(Model\DockerLogin $login): Message
    {
        try {
            $output = $this->worker->login($login);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not execute login', previous: $e);
        }

        return $this->newMessage('Login successfully', $output);
    }

    public function ps(): Model\DockerProcesses
    {
        try {
            $output = $this->worker->ps();
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not execute ps', previous: $e);
        }

        $lines = $this->jsonParser->parseLines($output, Model\DockerProcess::class);

        $collection = new Model\DockerProcesses();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);
        return $collection;
    }

    public function stats(): Model\DockerStatistics
    {
        try {
            $output = $this->worker->stats();
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not execute ps', previous: $e);
        }

        $lines = $this->jsonParser->parseLines($output, Model\DockerStatistic::class);

        $collection = new Model\DockerStatistics();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);
        return $collection;
    }

    private function newMessage(string $message, ?string $output = null): Model\Message
    {
        $return = new Model\Message();
        $return->setSuccess(true);
        $return->setMessage($message);
        $return->setOutput($output);
        return $return;
    }
}
