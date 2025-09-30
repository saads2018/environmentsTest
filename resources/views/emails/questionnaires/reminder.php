<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Questionnaire reminder</title>
    <style>
        body {
            background-color: #F4F4FF;
            margin: 0;
            padding: 0;
            font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%; 
        }
      
        img {
            max-width: 100%;
            border: none;
            -ms-interpolation-mode: bicubic;
        }

        table {
            width: 100%;
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        table td {
            font-size: 14px;
            vertical-align: top; 
        }

        .body {
            background-color: #F4F4FF;
            width: 100%; 
        }

        .container {
            max-width: 580px;
            width: 580px;
            margin: 0 auto !important;
            padding: 10px;
            display: block;
        }

        .content {
            max-width: 580px;
            margin: 0 auto;
            padding: 10px;
            display: block;
            box-sizing: border-box;
        }

        .main {
            background: #ffffff;
            width: 100%;
            border-radius: 8px;
        }

        .wrapper {
            padding: 20px;
            box-sizing: border-box;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .greetings {
            margin-bottom: 8px;
            font-size: 18px;
            text-align: center;
        }

        .content-card p {
            font-size: 18px;
            text-align: center;
        }

        .content-card p > span {
            font-size: 18px;
            font-weight: 600;
        }

        .bottom-block {
            background-color: #FFFFFF;
            margin-top: 16px;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .bottom-block span {
            font-size: 16px;
        }

        .footer {
            width: 100%; 
            margin-top: 10px;
            clear: both;
            text-align: center;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            font-size: 12px;
            color: #999999;
            text-align: center; 
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0 0 30px 0;
            font-weight: 400;
            color: #000000;
            line-height: 1.4;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize; 
        }

        p,
        ul,
        ol {
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: normal;
        }

            p li,
            ul li,
            ol li {
            margin-left: 5px;
            list-style-position: inside;
        }

        a {
            color: #5C90F1;
            text-decoration: underline; 
        }

        .btn {
            width: 100%;
            box-sizing: border-box;
        }

        .btn > tbody > tr > td {
          padding-bottom: 15px;
        }
        
        .btn table {
            width: auto; 
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center; 
        }

        .btn a {
            background-color: #ffffff;
            margin: 0;
            padding: 12px 25px;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            color: #5C90F1;
            border: solid 1px #5C90F1;
            border-radius: 5px;
            box-sizing: border-box;
            cursor: pointer;
            text-decoration: none;
            text-transform: capitalize; 
        }

        .btn-primary table td {
            background-color: #5C90F1; 
        }

        .btn-primary a {
            background-color: #5C90F1;
            border-color: #5C90F1;
            color: #ffffff; 
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0; 
        }

        .powered-by a {
            text-decoration: none; 
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0; 
        }

        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important; 
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 16px !important; 
            }

            table.body .wrapper {
                padding: 10px !important; 
            }

            table.body .content {
                padding: 0 !important; 
            }
            
            table.body .container {
                padding: 0 !important;
                width: 100% !important; 
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important; 
            }

            table.body .btn table {
                width: 100% !important; 
            }

            table.body .btn a {
                width: 100% !important; 
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important; 
            }
        }

        @media all {
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important; 
            }
            .btn-primary table td:hover {
                background-color: #34495e !important; 
            }
            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important; 
            } 
        }
    </style>
</head>

<body>
    <span class="preheader"></span>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>

            <td class="container">
                <div class="content">
                    <table role="presentation" class="main">
                        <tr>
                            <td>
                                <div class="header" style="border-bottom: 1px solid #E7E7E7;">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                        <td style="text-align: center;" class="content-block">
                                            <img class="img-responsive" style="width: 120px; margin-left: 20px;" src="../naviwell-logo.png" alt="Logo">
                                        </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td align="center">
                                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="content-card">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <p>Hello, {{$quiz->patient->user->first_name}} {{$quiz->patient->user->last_name}}.</p>
                                                                            <p>Our records show that you have not yet completed quarterly questionnaire. Please have this completed before your appointment on {{tenant("name")}}.</p>
                                                                            <p>Call or text if you have any questions!</p>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                <tbody>
                                                    <tr>
                                                        <td style="padding-bottom: 0;" align="center">
                                                            <a href="{{tenant('id')}}.naviwellgroup.com/profile" target="_blank">Go to your profile</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="content">
                    <div class="bottom-block">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <span>Thanks,</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span style="font-weight: bold;">{{tenant("name")}} and NaviWell team</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <span class="apple-link">9456 State Hwy 121, Frisco, Tx 75035, USA</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="powered-by">
                                    Provided by <a href="https://naviwellgroup.com">NaviWell</a>.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>

            <td>&nbsp;</td>
        </tr>
    </table>
</body>
</html>