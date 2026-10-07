<?php
/*
 * This file is part of the Fusio Plant project (https://github.com/apioo/fusio-plant).
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

namespace App\Action\Munin;

use App\Model\MuninReport;
use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class GetDisk implements ActionInterface
{
    public function __construct(private Service\Munin $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): MuninReport
    {
        $timeUnit = null;
        $rawTimeUnit = $request->get('time_unit');
        if (is_string($rawTimeUnit)) {
            $timeUnit = Service\Munin\TimeUnit::tryFrom($rawTimeUnit);
        }

        $report = new MuninReport();
        $report->setHtml($this->service->getDisk($timeUnit ?? Service\Munin\TimeUnit::MONTH));
        return $report;
    }
}
