<?php

namespace App\Service\Project;

use App\Exception\PortResolveException;

class PortNumberResolver
{
    /**
     * @throws PortResolveException
     */
    public function resolve(int $id, int $index): int
    {
        if ($index > 9) {
            throw new PortResolveException('The max allowed number of services in one project is 10, got: ' . $index);
        }

        if ($id < 100) {
            $internalPort = '9' . str_pad('' . $id, 2, '0', \STR_PAD_LEFT) . $index;
        } elseif ($id < 1000) {
            $internalPort = '1' . str_pad('' . $id, 3, '0', \STR_PAD_LEFT) . $index;
        } else {
            $id = $id + 1000;
            $internalPort = $id . $index;
        }

        $port = (int) $internalPort;
        if ($port > 65_535) {
            throw new PortResolveException('Reached max port range of ' . 65_535);
        }

        return $port;
    }
}
