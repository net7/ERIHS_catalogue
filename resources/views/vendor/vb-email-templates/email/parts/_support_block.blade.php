<!-- SUPPORT CALLOUT -->
<tr>
    <td bgcolor="{{config('email-templates.footer_bg_color')}}" align="center" style="padding: 30px 10px 0px 10px; background-color: {{config('email-templates.footer_bg_color')}}">
        <!--[if (gte mso 9)|(IE)]>
        <table align="center" border="0" cellspacing="0" cellpadding="0" width="{{config('email-templates.content_width')}}">
            <tr>
                <td align="center" valign="top" width="{{config('email-templates.content_width')}}">
        <![endif]-->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: {{config('email-templates.content_width')}}px; margin-bottom: 30px">
            <!-- HEADLINE -->
            <tr>
                <td bgcolor="{{config('email-templates.callout_bg_color')}}" align="center"
                    style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color:{{config('email-templates.callout_color')}}; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                    <h2 style="font-size: 20px; font-weight: 400; color: {{config('email-templates.callout_color')}}; margin: 0;">{{__('vb-email-templates::email-templates.general-labels.need-help')}}</h2>
                    <p style="margin: 0; color: {{config('email-templates.callout_color')}}; ">{{__('vb-email-templates::email-templates.general-labels.call-support')}}
                        <a href="tel:{{config('email-templates.customer-services-phone')}}" target="_blank">
                            {{config('email-templates.customer-services-phone')}}
                        </a>
                    </p>
                </td>
            </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </td>
</tr>
