<?php

namespace App\Filament\Clusters\DefaultPayOptions;

use BackedEnum;
use Filament\Clusters\Cluster;
use UnitEnum;

class DefaultPayOptionsCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Default Pay Options';

    protected static UnitEnum|string|null $navigationGroup = 'Employers';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 5;
}
