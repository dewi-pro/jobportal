<?php

namespace App\Mail;

use Botble\JobBoard\Models\RecruitmentProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    // public $recruitmentProgress;
    public $url;

    /**
     * Create a new message instance.
     *
     * @param RecruitmentProgress $recruitmentProgress
     * @param string $url
     */
    public function __construct(RecruitmentProgress $recruitmentProgress, $url)
    {
        $this->recruitmentProgress = $recruitmentProgress;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Job Application Status Updated')
                    ->view('job-board::emails.job-application-status-changed') // Use the custom namespace
                    ->with([
                        'recruitmentProgress' => $this->recruitmentProgress,
                        'url' => $this->url,
                    ]);
    }
}


