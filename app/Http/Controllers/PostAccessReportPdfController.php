<?php

namespace App\Http\Controllers;

use App\Enums\LearnedAboutErihs;
use App\Enums\MolabAuthorizationDroneFlight;
use App\Enums\ProposalSocialChallenges;
use App\Enums\ProposalType;
use App\Livewire\CreateProposal;
use App\Models\Method;
use App\Models\PostAccessReport;
use App\Models\Proposal;
use App\Models\Organization;
use App\Models\Tool;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Spatie\Tags\Tag;

class PostAccessReportPdfController extends Controller
{
    public function downloadPDF($id)
    {
        $postAccessReport = PostAccessReport::find($id);
        $reportsDetails = $postAccessReport->getAttributes();
        $user = User::find($postAccessReport->user_id)->full_name;
        unset($reportsDetails['proposal_id'], $reportsDetails['user_id'], $reportsDetails['id']);
        $photos = $postAccessReport->photos;
        foreach ($photos as $photo) {
            $reportsDetails['photos'][] = env('APP_URL'). '/storage/'.$photo->image_path;
        }

        $files = $postAccessReport->files;
        foreach ($files as $file) {
            $reportsDetails['files'][] = env('APP_URL'). '/storage/'.$file->file_path;
        }
        $reportsDetails['user'] = $user;
        $details['reportDetails'] = $reportsDetails;
        $details['proposal'] = Proposal::find($postAccessReport->proposal_id)->name;

        $pdf = PDF::loadView('pdf.post-access-report-pdf', compact('details'));
        return $pdf->download('post-access-report.pdf');
    }
}
