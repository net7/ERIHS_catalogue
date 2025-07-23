<?php

namespace App\Enums;

use App\Traits\{EnumHelper};


enum ProposalResearchCategories: string
{
    use EnumHelper;

    case IDENTIFICATION_OF_INORGANIC_PIGMENTS = 'Identification of Inorganic Pigments';
    case ANALYSIS_OF_ARCHEOLOGY_POTTERY = 'Analysis of Archaeological Pottery';
    case CLASSIFICATION_OF_ANCIENT_ORGANIC_MATERIA = 'Classification of Ancient Organic Materia';
    case HYPER_SPECTRAL_IMAGING_OF_WALL_PAINTINGS = 'Hyper-spectral imaging of Wall paintings';
    case ONLINE_PREVENTIVE_CONSERVATION_TOOL = 'Online Preventive Conservation Tool';
    case COLLECTION_MANAGEMENT_TOOL = 'Collection Management Tool';
}
