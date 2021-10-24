<!DOCTYPE html>
<html>
<head>
    <title>ItsolutionStuff.com</title>
</head>
<body>
    <p>{{ $details['body'] }}</p>
    <p>Saludos cordiales</p>
    <table style="max-width:80%;border:none;padding:1em;">
      <tr>
        <td>{{ $details['user'] }}<br />
            {{ $details['from'] }}
        </td>
        <td>
            {{$details['company_name']}}<br />
            {{$details['company_address']}}<br />
            {{$details['company_phone_number']}}
        </td>
      </tr>
    </table>
    <p style="color:red;"><b>Por favor responda este correo a {{ $details['from'] }}</b></p>
    <br /><br />
    <p style="vertical-align:middle;"><img src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/8ddb414d-3f4a-4506-a330-ce8de6d9e735/de4bbqc-216f1b32-7578-40db-a5d5-eed6976450c5.png/v1/fill/w_400,h_400,strp/logo_by_xdesignsillusion_de4bbqc-fullview.png?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOiIsImlzcyI6InVybjphcHA6Iiwib2JqIjpbW3siaGVpZ2h0IjoiPD00MDAiLCJwYXRoIjoiXC9mXC84ZGRiNDE0ZC0zZjRhLTQ1MDYtYTMzMC1jZThkZTZkOWU3MzVcL2RlNGJicWMtMjE2ZjFiMzItNzU3OC00MGRiLWE1ZDUtZWVkNjk3NjQ1MGM1LnBuZyIsIndpZHRoIjoiPD00MDAifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.ol-yBbYxI_3YTyD9mEuK91Sw_BM7coePtHnd_6gc0CU" title="Neutrinus" alt="Neutrinus" style="height:1.5em;padding-right:0.75em;" />Este mensaje ha sido generado automáticamente por Neutrinus ©2020</p>
</body>
</html>
