<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="shortcut icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon" />
    <title>Alerta - Easymeter</title>

    <style type="text/css">
        body {
            width: 100%;
            background-color: #333333;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        p,
        h1,
        h2,
        h3,
        h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 12px;
            border: 0;
        }

        .menu-space {
            padding-right: 25px;
        }

        a,
        a:hover {
            text-decoration: none;
            color: #FFF;
        }

        @media only screen and (max-width:640px) {
            body {
                width: auto !important;
            }

            table [class=main] {
                width: 440px !important;
            }

            table [class=two-left] {
                width: 420px !important;
                margin: 0px auto;
            }

            table [class=full] {
                width: 100% !important;
                margin: 0px auto;
            }

            table [class=two-left-inner] {
                width: 400px !important;
                margin: 0px auto;
            }

            table [class=menu-icon] {
                display: none;
            }
        }

        @media only screen and (max-width:479px) {
            body {
                width: auto !important;
            }

            table [class=main] {
                width: 310px !important;
            }

            table [class=two-left] {
                width: 300px !important;
                margin: 0px auto;
            }

            table [class=full] {
                width: 100% !important;
                margin: 0px auto;
            }

            table [class=two-left-inner] {
                width: 280px !important;
                margin: 0px auto;
            }

            table [class=menu-icon] {
                display: none;
            }
        }
    </style>
</head>

<body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

    <!--Main Table Start-->
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#333333">
        <tr>
            <td align="center" valign="middle">
                <!-- Top space -->
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td height="90" align="center" valign="top" style="font-size:90px; line-height:90px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- / Top space -->

                <!-- Header -->
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td align="center" valign="top" bgcolor="#03aeef" style="-moz-border-radius: 25px 25px 0px 0px; border-radius: 25px 25px 0px 0px;">
                                        <table width="380" border="0" align="center" cellpadding="0" cellspacing="0" class="two-left">
                                            <tr>
                                                <td height="35" align="center" valign="top" style="font-size:35px; line-height:35px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td align="center" valign="middle"><a href="<?php echo site_url('/'); ?>">
                                                                    <img editable="true" mc:edit="bm14-01" src="<?php echo base_url('assets/img/logo_b.png'); ?>" width="175" height="40" alt="" /></a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20" align="center" valign="top" style="font-size:20px; line-height:20px;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- / Header -->

                <!-- Body -->
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td align="center" valign="top" bgcolor="#FFFFFF">
                                        <table width="380" border="0" align="center" cellpadding="0" cellspacing="0" class="two-left">
                                            <tr>
                                                <td height="10" align="center" valign="top" style="line-height:10px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td align="center" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#4c4c4c; font-weight:bold; line-height:40px;" mc:edit="bm16-03">
                                                                <multiline>O chamado #<?= $cid; ?> foi criado.</multiline>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="10" align="center" valign="top" style="line-height:10px;">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" valign="top">
                                                                <table width="100" border="0" align="left" cellpadding="0" cellspacing="0" class="two-left-inner">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:normal; line-height:20px;" mc:edit="bm12-08">
                                                                            <multiline>Título</multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table width="280" border="0" align="left" cellpadding="0" cellspacing="0" class="full">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:bold; line-height:20px;" mc:edit="bm12-10">
                                                                            <multiline>Novo Chamado</multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" valign="top">
                                                                <table width="100" border="0" align="left" cellpadding="0" cellspacing="0" class="two-left-inner">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:normal; line-height:20px;" mc:edit="bm12-08">
                                                                            <multiline>Usuário</multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table width="280" border="0" align="left" cellpadding="0" cellspacing="0" class="full">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:bold; line-height:20px;" mc:edit="bm12-10">
                                                                            <multiline><?= $nome; ?></multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" valign="top">
                                                                <table width="100" border="0" align="left" cellpadding="0" cellspacing="0" class="two-left-inner">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:normal; line-height:16px;" mc:edit="bm12-08">
                                                                            <multiline>Mensagem</multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table width="280" border="0" align="left" cellpadding="0" cellspacing="0" class="full">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; line-height:16px; text-align: justify;" mc:edit="bm12-10">
                                                                            <multiline><?= $message; ?></multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="10" align="center" valign="top" style="line-height:10px;">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" valign="top">
                                                                <table width="100" border="0" align="left" cellpadding="0" cellspacing="0" class="two-left-inner">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#4c4c4c; font-weight:normal; line-height:20px;" mc:edit="bm12-08">
                                                                            <multiline>Previsão</multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table width="280" border="0" align="left" cellpadding="0" cellspacing="0" class="full">
                                                                    <tr>
                                                                        <td align="left" valign="top" style="font-family:'Open Sans', Verdana, Arial; font-size:12px; color:#d2322d; font-weight:bold; line-height:20px;" mc:edit="bm12-10">
                                                                            <multiline><?= $prev; ?></multiline>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20" align="center" valign="top" style="line-height:20px;">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#4c4c4c; font-weight:normal; line-height:24px;" mc:edit="bm16-04">
                                                                <multiline>
                                                                    <p>Por favor aguarde nossa próxima interação neste chamado.</p>
                                                                </multiline>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" valign="top">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- / Body -->

                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td align="center" valign="top" bgcolor="#FFFFFF" style="-moz-border-radius:0px 0px 25px 25px; border-radius:0px 0px 25px 25px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="380" border="0" align="center" cellpadding="0" cellspacing="0" class="two-left">
                                            <tr>
                                                <td height="35" align="center" valign="top" style="font-size:35px; line-height:35px;">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#FFF; font-weight:normal; line-height:28px;" mc:edit="bm14-03">
                                                    <multiline>Easymeter - UNO Robótica Ltda.</multiline>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#FFF; font-weight:bold; line-height:28px;" mc:edit="bm14-04">
                                                    <multiline>Copyright &copy; 2017-<?= date('Y'); ?> UNO Robótica</multiline>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" valign="middle">
                            <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                <tr>
                                    <td height="90" align="center" valign="top" style="font-size:90px; line-height:90px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <!--Main Table End-->
</body>

</html>