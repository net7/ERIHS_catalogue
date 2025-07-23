<?php

namespace App\Http\Controllers;

use App\Enums\LearnedAboutErihs;
use App\Enums\MolabAuthorizationDroneFlight;
use App\Enums\ProposalSocialChallenges;
use App\Enums\ProposalType;
use App\Livewire\CreateProposal;
use App\Models\Method;
use App\Models\Proposal;
use App\Models\Organization;
use App\Models\Tool;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Spatie\Tags\Tag;

class PDFController extends Controller
{
    public function downloadPDF($id): \Illuminate\Http\Response
    {
        $proposal = Proposal::find($id);
        $proposalInfo = [];

        $archlabSection = self::getArchlabSection($proposal);
        $fixlabSection = self::getFixlabSection($proposal);
        $molabSection = self::getMolabSection($proposal);
        if (!empty($archlabSection)) {
            $proposalInfo['archlab_section'] = $archlabSection;
        }
        if (!empty($fixlabSection)) {
            $proposalInfo['fixlab_section'] = $fixlabSection;
        }
        if (!empty($molabSection)) {
            $proposalInfo['molab_section'] = $molabSection;
        }
        $proposalInfo['proposalDetails'] = self::getProposalDetails($proposal);

        $proposalInfo['usersDetails'] = self::getMembersDetails($proposal);
        $proposalInfo['servicesDetails'] = self::getServicesDetails($proposal);

        $pdf = PDF::loadView('pdf.proposal-pdf', compact('proposalInfo'));
        return $pdf->download('proposal_' . $id . '.pdf');
    }

    public static function getArchlabSection($proposal): array
    {
        $archlabAttributes = self::filterProposalAttributes($proposal, 'archlab');

        foreach ($archlabAttributes as &$attribute) {
            if (is_string($attribute) && json_decode($attribute) !== null) {
                $decodedArray = json_decode($attribute, true);
                $attribute = implode('; ', $decodedArray);
            } elseif (is_array($attribute)) {
                $attribute = implode('; ', $attribute);
            }
        }
        return $archlabAttributes;
    }

    public static function getFixlabSection($proposal): array|null
    {
        $fixlabAttributes = self::filterProposalAttributes($proposal, 'fixlab');
        $res = [];
        foreach ($fixlabAttributes as $key => $attribute) {
            $key = ucfirst(str_replace('fixlab_', '', $key));
            if (is_string($attribute) && json_decode($attribute) !== null) {
                $res[$key] = self::getRepeaterObjectsData($attribute);
            } elseif (is_array($attribute)) {
                $res[$key] = implode('; ', $attribute);
            } else {
                $res[$key] = $attribute;
            }
        }
        if(!empty($res) && !empty($res->Objects_data)){
            return $res;
        }

        return null;
    }

    public static function getMolabSection($proposal): array|null
    {
        $molabAttributes = self::filterProposalAttributes($proposal, 'molab');
        $res = [];
        foreach ($molabAttributes as $key => $attribute) {
            if (str_ends_with($key, '_file')) {
                $attribute = env('APP_URL') . Storage::url($attribute);
            }
            $key = ucfirst(str_replace('molab_', '', $key));
            if (is_string($attribute) && json_decode($attribute) !== null) {
                $res[$key] = self::getRepeaterObjectsData($attribute);
            } elseif (is_array($attribute)) {
                $res[$key] = implode('; ', $attribute);
            } else {
                $res[$key] = $attribute;
            }
        }
        if(!empty($res) && !empty($res->Objects_data)){
            return $res;
        }

        return null;
    }


    public static function filterProposalAttributes($proposal, $startsWith)
    {
        $attributes = self::getProposalAttributes($proposal);

        return array_filter($attributes, function ($value, $key) use ($startsWith) {
            return str_starts_with($key, $startsWith);
        }, ARRAY_FILTER_USE_BOTH);
    }

    //Get all NOT NULL attributes
    public static function getProposalAttributesExcludingPlatformsInfo($proposal): array
    {
        $attributes = $proposal->getAttributes();

        return array_filter($attributes, function ($value, $key) {
            return !is_null($value) && !preg_match('/^(archlab|fixlab|molab)/', $key);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public static function getProposalAttributes($proposal): array
    {
        $attributes = $proposal->getAttributes();
        return array_filter($attributes, function ($value) {
            return !is_null($value);
        });
    }

    public static function getProposalDetails(Proposal $proposal): array
    {
        $proposalDetails = self::getProposalAttributesExcludingPlatformsInfo($proposal);

        if (isset($proposalDetails['learned_about_erihs'])) {
            $proposalDetails['learned_about_erihs'] = LearnedAboutErihs::fromName($proposalDetails['learned_about_erihs']);
        }
        if (isset($proposalDetails['social_challenges'])) {
            $proposalDetails['social_challenges'] = self::getSocialChallenges($proposalDetails['social_challenges']);
        }

        unset(
            $proposalDetails['id'],
            $proposalDetails['activities'],
            $proposalDetails['status_history'],
            $proposalDetails['uuid'],
            $proposalDetails['published_at'],
            $proposalDetails['is_published'],
            $proposalDetails['is_current'],
            $proposalDetails['publisher_type'],
            $proposalDetails['publisher_id'],
            $proposalDetails['cv'],
            $proposalDetails['internal_status'],
            $proposalDetails['status'],
            $proposalDetails['created_at'],
            $proposalDetails['updated_at'],
            $proposalDetails['molab_objects_data'],
            $proposalDetails['fixlab_objects_data'],
            $proposalDetails['call_id'],
            $proposalDetails['terms_and_conditions'],
            $proposalDetails['consent_to_videotape_and_photography'],
            $proposalDetails['news_via_email']
        );

        $proposalDetails['call'] = $proposal->call->name;

        $proposalDetails['providers_contacted'] = $proposal['providers_contacted'] ? 'Yes' : 'No';
        $proposalDetails['facility_contacted'] = $proposal['facility_contacted'] ? 'Yes' : 'No';
        $proposalDetails['eu_or_national_projects_related'] = $proposal['eu_or_national_projects_related'] ? 'Yes' : 'No';
        $proposalDetails['training_activity'] = $proposal['training_activity'] ? 'Yes' : 'No';
        $proposalDetails['industrial_involvement'] = $proposal['industrial_involvement'] ? 'Yes' : 'No';

        if ($proposal['type'] == ProposalType::NEW->name) {
            $proposalDetails['type'] = ProposalType::NEW->value;
            unset($proposalDetails['resubmission_previous_proposal_number']);
            unset($proposalDetails['related_project']);
            unset($proposalDetails['continuation_motivation']);
            unset($proposalDetails['comment']);
        }

        if ($proposal['type'] == ProposalType::LONG_TERM_PROJECT->name) {
            $proposalDetails['type'] = ProposalType::LONG_TERM_PROJECT->value;
            unset($proposalDetails['continuation_motivation']);
            $proposalDetails['related_project'] = Proposal::find($proposal['resubmission_previous_proposal_number'])?->name;
        }

        if ($proposal['type'] == ProposalType::RESUBMISSION->name) {
            $proposalDetails['type'] = ProposalType::RESUBMISSION->value;
            unset($proposalDetails['comment']);
        }

        $attachments = $proposal->attachments;
        if (isset($attachments)) {
            foreach ($attachments as $attachment) {
                $proposalDetails['attachments'][$attachment->caption] = env('APP_URL') . Storage::url($attachment->file_path);
            }
        }
        $proposalDetails['research_disciplines'] = implode(', ', $proposal->tagsWithType('research_disciplines')->pluck('name')->toArray());

        return self::replaceLabels($proposalDetails);
    }

    public static function getSocialChallenges($socialChallenges)
    {
        $result = [];
        $socialChallengesArray = json_decode($socialChallenges);
        foreach ($socialChallengesArray as $sc) {
            $result[] = ProposalSocialChallenges::tryFrom($sc) ?? ProposalSocialChallenges::fromName($sc);
        }
        return implode('; ', $result);
    }

    public static function replaceLabels($data)
    {
        $new_data = [];

        $whom = $data['whom'] ?? '';
        $projectName = $data['name'] ?? '';
        $foundedBy = $data['founded_by'] ?? '';
        $trainingActivity = $data['training_activity_details'] ?? '';
        $numberOfAgreement = $data['number_of_grant_agreement'] ?? '';
        $industrialInvolvement =  $data['industrial_involvement_details'] ?? '';
        $otherDetails = $data['other_details'] ?? '';

        $fieldsToUnset = [
            'whom',
            'name_of_the_project',
            'training_activity_details',
            'number_of_grant_agreement',
            'industrial_involvement_details',
            'other_details',
            'founded_by'
        ];
        foreach ($fieldsToUnset as $field) {
            unset($data[$field]);
        }

        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }

            switch ($key) {
                case 'molab_x_ray':
                    $value = $value ? 'Yes' : 'No';
                    break;

                case 'molab_drone_flight':
                    $value = MolabAuthorizationDroneFlight::fromName($value);
                    break;

                case str_ends_with($key, '_file'):
                    $value = env('APP_URL') . Storage::url($value);
                    break;
            }

            $label = CreateProposal::getFieldLabels($key);
            if ($label === 'MISSING LABEL - ' . $key) {
                $label = $key;
            }

            switch ($key) {
                case 'facility_contacted':
                    $new_data['Service manager contacted'] = $value == 'Yes' ? $whom : 'No';
                    break;

                case 'eu_or_national_projects_related':
                    $htmlContent = $value == 'Yes'
                        ? new HtmlString($projectName . '<br><strong>Funded by: </strong>' . $foundedBy)
                        : '-';
                    if ($numberOfAgreement) {
                        $htmlContent = new HtmlString($htmlContent->toHtml() . '<br><strong>Number of agreement: </strong>' . $numberOfAgreement);
                    }
                    $new_data['EU national project related'] = $htmlContent;
                    break;

                case 'training_activity':
                    $new_data['Project related to initial training (PhD) or a training activity'] =
                        $value == 'Yes' ? new HtmlString($trainingActivity) : '-';
                    break;

                case 'industrial_involvement':
                    $new_data['Industrial involvement or sponsorship'] = $value == 'Yes' ?  $industrialInvolvement : 'No';
                    break;

                case 'learned_about_erihs':
                    $new_data['Learned about E-RIHS'] = $value == 'Other' ?  $otherDetails : $value;
                    break;

                default:
                    $new_data[$label] = $value;
            }
        }

        return $new_data;
    }


    public static function getRepeaterObjectsData($data)
    {
        $data = json_decode($data);
        $new_data = [];
        foreach ($data as $item) {
            $new_item = [];
            foreach ($item as $key => $value) {
                if (!empty($value)) {
                    if ($key == 'molab_object_ownership_consent_file') {
                        $value = env('APP_URL') . Storage::url($value);
                    }
                    if ($key == 'molab_object_material' || $key == 'fixlab_object_material') {
                        $value = self::getTagsFromRepeater($value);
                    }
                    $label = CreateProposal::getFieldLabels($key);
                    if ($label === 'MISSING LABEL - ' . $key) {
                        $label = $key; // Se l'etichetta è mancante, utilizzo la chiave originale
                    }
                    $new_item[$label] = $value;
                }
            }
            $new_data[] = $new_item;
        }
        return $new_data;
    }

    public static function getMembersDetails($proposal): array
    {
        $partners = $proposal->partners()->get(['full_name', 'email', 'short_cv', 'academic_background']);
        $leader = $proposal->leader()->first(['full_name', 'email', 'short_cv', 'academic_background']);

        $leaderDetails = $leader ? $leader->full_name . ' (' . $leader->email . ')' : '';

        $leaderCV = $leader ? $leader->short_cv : '';


        $partnersDetails = $partners->map(function ($item) {
            return $item->full_name . ' (' . $item->email . ')';
        })->implode(",\n");



        $data = [
            'leader' => $leaderDetails,
            'leader_CV' => $leaderCV,
            'leader_Academic_Background' => $leader ? $leader->academic_background : '',
        ];

        $i = 1;
        foreach ($partners as $partner) {
            $data['partner_' . $i] = $partner->full_name . ' (' . $partner->email . ')';
            $data['partner_' . $i . '_CV'] = $partner->short_cv;
            $data['partner_' . $i . '_Academic_Background'] = $partner->academic_background;
            $i++;
        }
        return $data;
    }

    public static function getServicesDetails($proposal): array
    {
        $services = $proposal->services()->get();
        $servicesDetails = [];
        foreach ($services as $service) {
            $organizationName = Organization::find($service->organization_id)->name;
            $categories = implode(', ', array_column($service->categories, 'category'));
            $methodAndTool = self::getMethodServiceTool($service);
            $serviceManager = implode(', ', $service->serviceManagers()->get()->pluck('email')->toArray());
            $servicesDetails[] = [
                'name' => $service->title,
                'description' => $service->description,
                'summary' => $service->summary,
                'organization' => $organizationName,
                'research_questions' => $categories,
                'method_and_tool' => $methodAndTool,
                'service_manager' => $serviceManager
            ];
        }

        return $servicesDetails;
    }

    public static function getMethodServiceTool($service)
    {
        $mst = $service->methodServiceTool()->get();
        $res = [];
        foreach ($mst as $item) {
            $tmp = [];
            if ($item->method_id) {
                $tmp['method'] = Method::find($item->method_id)->preferred_label;
            } else {
                $tmp['method'] = '';
            }

            if ($item->tool_id) {
                $tmp['tool'] =  Tool::find($item->tool_id)->name;
            } else {
                $tmp['tool'] = '';
            }
            $res[] = $tmp;


            // $res[] = ['method' => Method::find($item->method_id)->preferred_label, 'tool' => Tool::find($item->tool_id)->name];



        }
        return $res;
    }

    public static function getTagsFromRepeater($value): array
    {
        $tmp = [];
        foreach ($value as $tag) {
            $tmp[] = Tag::find($tag)->name;
        }
        return $tmp;
    }
}
