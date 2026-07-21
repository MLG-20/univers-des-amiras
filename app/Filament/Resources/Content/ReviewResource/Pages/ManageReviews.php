<?php

namespace App\Filament\Resources\Content\ReviewResource\Pages;

use App\Filament\Resources\Content\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReviews extends ManageRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
