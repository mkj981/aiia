<?php

namespace App\Filament\Clusters\PayOptions;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

class PayOptionsCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Pay Options';

    protected static UnitEnum|string|null $navigationGroup = 'Employees';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 9;
}
