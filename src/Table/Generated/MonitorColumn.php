<?php

namespace App\Table\Generated;

enum MonitorColumn : string implements \PSX\Sql\ColumnInterface
{
    case ID = \App\Table\Generated\MonitorTable::COLUMN_ID;
    case PROJECT_ID = \App\Table\Generated\MonitorTable::COLUMN_PROJECT_ID;
    case CONTAINER_ID = \App\Table\Generated\MonitorTable::COLUMN_CONTAINER_ID;
    case NAME = \App\Table\Generated\MonitorTable::COLUMN_NAME;
    case CPU_PERC = \App\Table\Generated\MonitorTable::COLUMN_CPU_PERC;
    case MEM_PERC = \App\Table\Generated\MonitorTable::COLUMN_MEM_PERC;
    case MEM_USAGE = \App\Table\Generated\MonitorTable::COLUMN_MEM_USAGE;
    case MEM_LIMIT = \App\Table\Generated\MonitorTable::COLUMN_MEM_LIMIT;
    case NETIO_RECEIVED = \App\Table\Generated\MonitorTable::COLUMN_NETIO_RECEIVED;
    case NETIO_SENT = \App\Table\Generated\MonitorTable::COLUMN_NETIO_SENT;
    case BLOCKIO_WRITTEN = \App\Table\Generated\MonitorTable::COLUMN_BLOCKIO_WRITTEN;
    case BLOCKIO_READ = \App\Table\Generated\MonitorTable::COLUMN_BLOCKIO_READ;
    case INSERT_DATE = \App\Table\Generated\MonitorTable::COLUMN_INSERT_DATE;
}