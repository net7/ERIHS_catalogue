<?php

namespace App\Filament\Resources\PostAccessReportResource\Pages;

use App\Filament\Resources\PostAccessReportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePostAccessReport extends CreateRecord
{
    protected static string $resource = PostAccessReportResource::class;
    protected ?string $subheading = 'Please note that the fields marked with the asterisk * and the photo
                                     will be published on the E-RIHS website (www.erihs.eu) and to E-RIHS Zenodo
                                     community and released under the CC-BY-NC license.';
}
