{{ header }}

<table width="100%">
    <tbody>
        <tr>
            <td class="wrapper" width="700" align="center">
                <table class="section" cellpadding="0" cellspacing="0" width="700" bgcolor="#f8f8f8">
                    <tr>
                        <td class="column" align="left">
                            <table>
                                <tbody>
                                <tr>
                                    <td align="left" style="padding: 20px 50px;">
                                        <p><strong>Hello, We received a new job application from {{ site_title }}!</strong></p>
                                        <p><img src="{{ site_url }}/vendor/core/core/base/images/emails/person.png"
                                                alt="From" width="20" style="margin-right: 10px;" /> Name: {{ job_application_name }}</p>
                                        <p><img src="{{ site_url }}/vendor/core/core/base/images/emails/edit.png"
                                                alt="Subject" width="20" style="margin-right: 10px;" />Position: {{ job_application_position }}</p>
                                        <p><img src="{{ site_url }}/vendor/core/core/base/images/emails/email.png"
                                                alt="Email" width="20" style="margin-right: 10px;" /> Email: {{ job_application_email }}</p>
                                        <p><img src="{{ site_url }}/vendor/core/core/base/images/emails/edit.png"
                                                alt="Subject" width="20" style="margin-right: 10px;" />Resume: {{ job_application_resume }}</p>
                                        <p><img src="{{ site_url }}/vendor/core/core/base/images/emails/edit.png"
                                                alt="Subject" width="20" style="margin-right: 10px;" />Cover Letter: {{ job_application_cover_letter }}</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
{{ footer }}
