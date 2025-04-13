<?php

namespace App\Table\Generated;

enum ProjectColumn : string implements \PSX\Sql\ColumnInterface
{
    case ID = \App\Table\Generated\ProjectTable::COLUMN_ID;
    case USER_ID = \App\Table\Generated\ProjectTable::COLUMN_USER_ID;
    case DISPLAY_ID = \App\Table\Generated\ProjectTable::COLUMN_DISPLAY_ID;
    case NAME = \App\Table\Generated\ProjectTable::COLUMN_NAME;
    case APPS = \App\Table\Generated\ProjectTable::COLUMN_APPS;
    case UPDATE_DATE = \App\Table\Generated\ProjectTable::COLUMN_UPDATE_DATE;
    case INSERT_DATE = \App\Table\Generated\ProjectTable::COLUMN_INSERT_DATE;
}