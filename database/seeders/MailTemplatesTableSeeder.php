<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('mail_templates')->delete();

        \DB::table('mail_templates')->insert(array (
            0 =>
            array (
                'id' => 1,
                'mailable' => 'App\\Mail\\WelcomeMail',
                'name' => 'Welcome email example',
                'subject' => 'Welcome, {{ name }}',
                'html_template' => '<p>Hello, {{ name }}.</p>',
                'text_template' => 'Hello, {{ name }}.',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 =>
            array (
                'id' => 2,
                'mailable' => 'App\\Mail\\NewUserRegistered',
                'name' => 'User Registration',
                'subject' => 'A new user has registered',
                'html_template' => '<p>{{ user }} has registered with email {{ email }} &nbsp;</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'mailable' => 'App\\Mail\\ApplicationSubmitted',
                'name' => 'Submission application',
                'subject' => 'Application Submitted',
                'html_template' => '<p>Thank you for submitting an E-RIHS proposal.</p><p>With this e-mail, we thank you and acknowledge receipt of your application for Access through E-RIHS. You may visualize the status of your proposal {{ proposalName }} <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a>.</p><p>Your project proposal is to undergo feasibility and Peer Review Assessment. Our office will contact you should any specific questions arise, otherwise, we will provide you with the final evaluation outcome no later than two months following the call deadline.<br><br></p><p>This email has been auto-generated. Please do not reply to this account. Your email will not be read. For any questions, please write to userhelpdesk@e-rihs.eu.&nbsp;</p><p>The E-RIHS User Helpdesk will be happy to assist you.<br><br></p><p>With kind regards,</p><p><strong><em>E-RIHS User Helpdesk</em></strong><br></p><p><br></p>',
            'text_template' => '<p>&lt;p&gt;Thank you for submitting an E-RIHS proposal.&lt;/p&gt;<br>&lt;p&gt;To activate the evaluation procedure, &lt;strong&gt;please click&lt;/strong&gt; on the following link:&lt;/p&gt;<br>&lt;p&gt;&lt;a href="%%ACTIVATION LINK%%"&gt;%%ACTIVATION LINK%%&lt;/a&gt;&lt;/p&gt;<br><br>&lt;p&gt;With this e-mail, we thank you and acknowledge receipt of your application for Access through E-RIHS. You may visualize the status of your &lt;em&gt;%%ID%%&lt;/em&gt; proposal in your personal dashboard on the website (next to the log-in button).&lt;/p&gt;<br><br>&lt;p&gt;Your project proposal is to undergo feasibility and Peer Review Assessment. Our office will contact you should any specific questions arise, otherwise, we will provide you with the final evaluation outcome no later than two months following the call deadline.&lt;/p&gt;<br><br>&lt;p&gt;This email has been auto-generated. Please do not reply to this account. Your email will not be read. For any questions, please write to &lt;a href="mailto:userhelpdesk@e-rihs.eu"&gt;userhelpdesk@e-rihs.eu&lt;/a&gt;. The E-RIHS User Helpdesk will be happy to assist you.&lt;/p&gt;<br><br>&lt;p&gt;With kind regards,&lt;br&gt;E-RIHS User Helpdesk&lt;/p&gt;<br><br></p>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'mailable' => 'App\\Mail\\NewSubmission',
                'name' => 'New submission',
                'subject' => 'New proposal submission',
                'html_template' => '<p>A new proposal was submitted from \'{{ user }}\'. Click <a href="{{url}}"><span style="text-decoration: underline;">here</span></a> for details.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'mailable' => 'App\\Mail\\ApplicationFeasible',
                'name' => 'Proposal feasible',
                'subject' => 'Proposal {{ proposaName }} is feasible',
                'html_template' => '<p>The proposal {{ proposalName }}<strong> </strong>has been marked as feasible. Click <a href="{{ url }}">here</a> for details.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 =>
            array (
                'id' => 6,
                'mailable' => 'App\\Mail\\ReviewerSelected',
                'name' => 'Reviewer selection',
                'subject' => 'You have been chosen as a reviewer',
                'html_template' => '<p>You have been chosen as a reviewer for the proposal {{ proposalName }}. Click <a href="{{ url }}">here</a> for details.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 =>
            array (
                'id' => 7,
                'mailable' => 'App\\Mail\\ReviewerCheck',
                'name' => 'Reviewer check',
                'subject' => 'A Proposal is waiting for check',
                'html_template' => '<p>The initial 7-day review period has passed, and we need your immediate action.</p><p>Please conduct <a href="{{ url }}">here</a> the conflict of interest assessment within the next 3 days to ensure the integrity of our review process.&nbsp;</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 =>
            array (
                'id' => 8,
                'mailable' => 'App\\Mail\\NewReviewerSelection',
                'name' => 'Proposal of a new reviewer',
                'subject' => 'A new reviewer is needed',
                'html_template' => '<p>The reviewer {{ reviewer }} has missed the the conflict of interest assessment for the proposal {{ proposalName }}. <br></p><p>Please select another reviewer for this proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a>.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 =>
            array (
                'id' => 9,
                'mailable' => 'App\\Mail\\ReviewerConflicts',
                'name' => 'Reviewer conflicts',
                'subject' => 'A reviewer has conflict of interest',
                'html_template' => '<p>The reviewer {{ reviewer }} has conflicts of interest for the proposal {{ proposalName }}.<br>Please select another reviewer <a href="{{ url }}"><span style="text-decoration: underline;">here</span></a>.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 =>
            array (
                'id' => 10,
                'mailable' => 'App\\Mail\\WeeklyReminder',
                'name' => 'Weekly Reminder',
                'subject' => 'Weekly Reminder for proposal {{ proposalName }}',
                'html_template' => '<p>We would like to remind you about the ongoing proposal evaluation process that requires your attention.</p><p>You will receive a weekly email to prompt you to evaluate the proposal <a href="{{ url }} " rel="noopener noreferrer" target="_blank">{{ proposalName }} </a>and, if you haven\'t already, click on the "evaluation made" button.&nbsp;</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 =>
            array (
                'id' => 11,
                'mailable' => 'App\\Mail\\ProposalEvaluationUpdate',
                'name' => 'Proposal Evaluation Update',
                'subject' => 'Notification: Proposal Evaluation Update',
                'html_template' => '<p>Two of the three assigned reviewers have completed their evaluations and clicked on the "evaluation made" button on the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 =>
            array (
                'id' => 12,
                'mailable' => 'App\\Mail\\ApplicationReviewOutcome',
                'name' => 'Application Review Outcome',
                'subject' => 'Application Review Outcome',
                'html_template' => '<p>Dear {{ user }}</p><p>We would like to inform you to inform you that your application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is in the status of {{ status }}</p><p>If you have any questions or would like feedback on the specific reasons for this decision, please do not hesitate to reach out to us.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 =>
            array (
                'id' => 13,
                'mailable' => 'App\\Mail\\ApplicationRejected',
                'name' => 'Application Rejected',
                'subject' => 'Your application has been rejected',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> did not pass the review. Please contact the user help desk at <a href="mailto:userhelpdesk@e-rihs.eu" rel="noopener noreferrer" target="_blank">userhelpdesk@e-rihs.eu</a>.&nbsp;</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 =>
            array (
                'id' => 14,
                'mailable' => 'App\\Mail\\ApplicationInMainList',
                'name' => 'Application in Main List',
                'subject' => 'Application in Main List',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is now in the main list.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 =>
            array (
                'id' => 16,
                'mailable' => 'App\\Mail\\CloseProposal',
                'name' => 'Close Proposal',
                'subject' => 'Closing proposal',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is closed.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 =>
            array (
                'id' => 17,
                'mailable' => 'App\\Mail\\CloseProcess',
                'name' => 'Close process',
                'subject' => 'Closing process for {{ proposalName }}',
                'html_template' => '<p>The process for the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is successfully ended!</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 =>
            array (
                'id' => 18,
                'mailable' => 'App\\Mail\\AccessDuties',
                'name' => 'Access duties',
                'subject' => 'Access duties needs for {{ proposalName }}',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is waiting for the access duties.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 =>
            array (
                'id' => 19,
                'mailable' => 'App\\Mail\\ReviewerDeleted',
                'name' => 'Reviewer deleted',
                'subject' => 'Removed as reviewer from the proposal {{ proposalName }}',
                'html_template' => '<p>You are no longer a reviewer of the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 =>
            array (
                'id' => 20,
                'mailable' => 'App\\Mail\\ApplicationPartiallyFeasible',
                'name' => 'Proposal partially feasible',
                'subject' => 'Proposal {{ proposaName }} is partially feasible',
                'html_template' => '<p>The proposal <a href="{{ url }}">{{ proposalName }}</a> is partially feasible but could be edited again.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 =>
            array (
                'id' => 21,
                'mailable' => 'App\\Mail\\ApplicationNotFeasible',
                'name' => 'Proposal not feasible',
                'subject' => 'Proposal {{ proposaName }} is not feasible',
                'html_template' => '<p>The proposal <a href="{{ url }}">{{ proposalName }}</a> is not feasible but could be edited again.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 =>
            array (
                'id' => 22,
                'mailable' => 'App\\Mail\\HelpDeskNotificationAfterSevenDays',
                'name' => 'Help desk notification after 7 days',
                'subject' => 'It has been 7 days since the reviewer {{ reviewer }} was assigned',
                'html_template' => '<p>The reviewer {{ reviewer }} has not yet verified the presence of conflict of interest for proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 =>
            array (
                'id' => 23,
                'mailable' => 'App\\Mail\\ReviewerAcceptance',
                'name' => 'Reviewer acceptance',
                'subject' => 'Reviewer acceptance',
                'html_template' => '<p>The reviewer {{ reviewer }} declared that he had no conflict of interest in proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 =>
            array (
                'id' => 24,
                'mailable' => 'App\\Mail\\ReviewerExplicitRefusal',
                'name' => 'Reviewer explicit refusal',
                'subject' => 'Reviewer explicit refusal',
                'html_template' => '<p>The reviewer {{ reviewer }} explicitly refused to review proposal {{ proposalName }}. Click <a href="{{ url }}">here</a> for details.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 =>
            array (
                'id' => 25,
                'mailable' => 'App\\Mail\\NoMoreReviewsAvailable',
                'name' => 'No more reviews available',
                'subject' => 'No more reviews available',
                'html_template' => '<p>You have finished the number of reviews you can do in the current year. If you would like to make yourself available for more reviews, please update the information in the \'Reviewer details\' section <a href="{{ url }}" target="_blank">here</a>.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 =>
            array (
                'id' => 26,
                'mailable' => 'App\\Mail\\ApplicationInReserveList',
                'name' => 'Application in Reserve List',
                'subject' => 'Your application is in Reserve List',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is now in the reserve list. Until the end of the call your application could still be considered to be accepted.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 =>
            array (
                'id' => 27,
                'mailable' => 'App\\Mail\\ApplicationAccepted',
                'name' => 'Application has been accepted',
                'subject' => 'An application has been accepted',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> has been accepted by the applicant. All the service managers must contact the applicant for scheduling the access.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 =>
            array (
                'id' => 28,
                'mailable' => 'App\\Mail\\ThirdReviewerEvaluatedTheProposal',
                'name' => 'Third reviewer evaluated the proposal',
                'subject' => 'Notification: Proposal Evaluation Update',
                'html_template' => '<p>The third reviewer also evaluated the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            27 =>
            array (
                'id' => 29,
                'mailable' => 'App\\Mail\\NoReviewersAvailable',
                'name' => 'No reviewers available',
                'subject' => 'There is no reviewer available',
                'html_template' => '<p>The proposal {{ proposalName }} is ready for review but no reviewers were found. Please select 3 reviewers from the list of those available <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            28 =>
            array (
                'id' => 30,
                'mailable' => 'App\\Mail\\ReviewerConfirmation',
                'name' => 'Reviewer Confirmation',
                'subject' => 'Confirm reviewers',
                'html_template' => '<p>Reviewers were automatically selected to review proposal {{ proposalName }}. Please confirm them <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a> and get the review process going.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            29 =>
            array (
                'id' => 31,
                'mailable' => 'App\\Mail\\CheckFeasibility',
                'name' => 'CheckFeasibility',
                'subject' => 'Check feasibility',
                'html_template' => '<p>A new proposal, with the services offered by your organization, was submitted by \'{{ user }}\'. Click <a href="{{ url }}"><span style="text-decoration: underline;">here</span></a> to evaluate the feasibility of the chosen services.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            30 =>
            array (
                'id' => 32,
                'mailable' => 'App\\Mail\\ResubmitProposal',
                'name' => 'Resubmit proposal',
                'subject' => 'Your proposal is {{ proposalStatus }}!',
                'html_template' => '<p>The proposal <a href="{{ url }}"><span style="text-decoration: underline;">{{ proposalName }}</span></a> is <strong>{{ proposalStatus }}</strong>. Please contact the user helpDesk at userhelpdesk@e-rihs.eu.&nbsp;</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            31 =>
            array (
                'id' => 33,
                'mailable' => 'App\\Mail\\ApplicationResubmission',
                'name' => 'Proposal resubmission',
                'subject' => 'Application resubmitted',
                'html_template' => '<p>The proposal {{ proposalName }} was resubmitted. Click <a href="{{ url }}">here</a> for details.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            32 =>
            array (
                'id' => 34,
                'mailable' => 'App\\Mail\\HelpDeskApplicationFeasible',
                'name' => 'Help Desk - ApplicationFeasible',
                'subject' => 'Application  {{ proposalName }} is feasible.',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is now feasible!</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            33 =>
            array (
                'id' => 35,
                'mailable' => 'App\\Mail\\ApplicationBelowThreshold',
                'name' => 'Application below threshold',
                'subject' => 'Application rejected',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> didn\'t pass the review.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            34 =>
            array (
                'id' => 36,
                'mailable' => 'App\\Mail\\AcceptanceConfirmation',
                'name' => 'Acceptance confirmation',
                'subject' => 'Acceptance confirmation',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> has been accepted. Please upload the ownership/drone permissions in 15 days.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            35 =>
            array (
                'id' => 37,
                'mailable' => 'App\\Mail\\AcceptAccessOfferReminder',
                'name' => 'Accept access offer reminder',
                'subject' => 'Accept access offer',
                'html_template' => '<p>Remember to accept the access offer for proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> within the next 10 days.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            36 =>
            array (
                'id' => 38,
                'mailable' => 'App\\Mail\\AcceptOrDiscardProposal',
                'name' => 'Accept or discard application',
                'subject' => 'Pending confirmation',
                'html_template' => '<p>Discard the proposal {{ proposalName }} or confirm that the files are correct <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            37 =>
            array (
                'id' => 39,
                'mailable' => 'App\\Mail\\UpdateFilesReminder',
                'name' => 'Update files reminder',
                'subject' => 'Update files',
                'html_template' => '<p>Remember to update the <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> proposal files within the next 10 days</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            38 =>
            array (
                'id' => 40,
                'mailable' => 'App\\Mail\\UserNotYetAcceptedAccessOffer',
                'name' => 'User has not yet accepted the access offer',
                'subject' => 'The user has not yet accepted the access offer',
                'html_template' => '<p>The user has not yet accepted the access offer for proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            39 =>
            array (
                'id' => 41,
                'mailable' => 'App\\Mail\\UserNotYetUpdatedFiles',
                'name' => 'User has not yet updated files',
                'subject' => 'User has not yet updated files',
                'html_template' => '<p>The user has not yet updated the files of the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            40 =>
            array (
                'id' => 42,
                'mailable' => 'App\\Mail\\ProposalDiscardedFromHD',
                'name' => 'Proposal discarded from Help Desk',
                'subject' => 'Proposal discarded',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> has been discarded from the Help Desk user because {{ motivation }}.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            41 =>
            array (
                'id' => 43,
                'mailable' => 'App\\Mail\\ProposalConfirmed',
                'name' => 'Proposal confirmed',
                'subject' => 'Proposal confirmed',
                'html_template' => '<p>The files of proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> have been updated. Please contact the user to organise access.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            42 =>
            array (
                'id' => 44,
                'mailable' => 'App\\Mail\\CloseAccess',
                'name' => 'Solicit service manager to close access',
                'subject' => 'Close access',
                'html_template' => '<p>Remember to click on ‘Carry Out’ <a href="{{ url }}" rel="noopener noreferrer" target="_blank">here</a> once the service access for proposal {{ proposalName }} is carried out.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            43 =>
            array (
                'id' => 45,
                'mailable' => 'App\\Mail\\ApplicationResubmissionHdNotification',
                'name' => 'Application resubmission - HelpDesk Notification',
                'subject' => 'The proposal {{ proposalName }} was resubmitted',
                'html_template' => '<p>The user {{ user }} has resubmitted the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a></p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            44 =>
            array (
                'id' => 46,
                'mailable' => 'App\\Mail\\ApplicationInMainListSmNotification',
                'name' => 'Application in Main List - Service manager notification',
                'subject' => 'The proposal {{ proposalName }} is in main list!',
                'html_template' => '<p>The proposal <a href="{{ url }} " rel="noopener noreferrer" target="_blank">{{ proposalName }} </a>is now in the Main List.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            45 =>
            array (
                'id' => 47,
                'mailable' => 'App\\Mail\\CloseProposalHdNotification',
                'name' => 'Close proposal - Help Desk notification',
                'subject' => 'Closing proposal {{ proposalName }}',
                'html_template' => '<p>The proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is closed.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            46 =>
            array (
                'id' => 48,
                'mailable' => 'App\\Mail\\CloseProposalSmNotification',
                'name' => 'Close proposal - Service Manager notification',
                'subject' => 'Closing proposal {{ proposalName }}',
                'html_template' => '<p>Proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> was closed.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            47 =>
            array (
                'id' => 49,
                'mailable' => 'App\\Mail\\CloseProcessHdNotification',
                'name' => 'Close process - Help Desk notification',
                'subject' => 'Closing process for {{ proposalName }}',
                'html_template' => '<p>The process for the proposal <a href="http://www.erihs-catalogo.test/dashboard/mail-templates/49/{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is successfully ended!</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            48 =>
            array (
                'id' => 50,
                'mailable' => 'App\\Mail\\CloseProcessSmNotification',
                'name' => 'Close process - Service Manager notification',
                'subject' => 'Closing process for {{ proposalName }}',
                'html_template' => '<p>The process for the proposal <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is successfully ended!</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            49 =>
            array (
                'id' => 51,
                'mailable' => 'App\\Mail\\ApplicationInReserveListSmNotification',
                'name' => 'Application in Reserve list - Service manager notification',
                'subject' => 'Application {{ proposalName }} is in reserve list',
                'html_template' => '<p>The application <a href="{{ url }}" rel="noopener noreferrer" target="_blank">{{ proposalName }}</a> is now in reserve list. Please contact the user help desk at userhelpdesk@e-rihs.eu.</p>',
                'text_template' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            50 =>
                array (
                    'id' => 52,
                    'mailable' => 'App\\Mail\\PartnerProposal',
                    'name' => 'Partner selection',
                    'subject' => 'Proposal submitted',
                    'html_template' => '<p>You have been chosen as a partner in proposal {{ proposalName }} by {{ leader }}</p>',
                    'text_template' => NULL,
                    'created_at' => NULL,
                    'updated_at' => NULL,
                ),
        ));


    }
}
