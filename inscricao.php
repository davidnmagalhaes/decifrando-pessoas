<?php
use PHPMailer\PHPMailer\PHPMailer;

require './vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $recaptchaSecretKey = '6LfOn7QnAAAAAFHcEX8F4Xg69FODtDNTTIAzdBwf';
    $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    // Verificar o reCAPTCHA
    $recaptchaData = [
        'secret' => $recaptchaSecretKey,
        'response' => $recaptchaResponse
    ];

    $recaptchaOptions = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptchaData)
        ]
    ];

    $recaptchaContext = stream_context_create($recaptchaOptions);
    $recaptchaResult = file_get_contents($recaptchaVerifyUrl, false, $recaptchaContext);
    $recaptchaResultJson = json_decode($recaptchaResult);

    if ($recaptchaResultJson->success) {
        $mail = new PHPMailer(true);

        try {
            $destinatario = "contato@decifrandopessoasoficial.com.br";
            $assunto = "Nova inscrição - Decifrando Pessoas";
            $mensagem = "Nome: $nome\n";
            $mensagem .= "E-mail: $email\n";
            $mensagem .= "Telefone: $telefone\n";

            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'mail.decifrandopessoasoficial.com.br';
            $mail->SMTPAuth = true;
            $mail->Username = 'contato@decifrandopessoasoficial.com.br';
            $mail->Password = 'DeciPessoas@2023';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';

            $mail->setFrom($destinatario);
            $mail->addAddress($destinatario);
            $mail->Subject = $assunto;
            $mail->Body = $mensagem;

            if ($mail->send()) {
                $mensagemResposta = "Você será redirecionado para pagar sua inscrição...";
                header("Location: https://pag.ae/7ZGDdMxnQ");
                exit;
            } else {
                $mensagemResposta = "Erro ao enviar.";
            }
        } catch (Exception $e) {
            $mensagemResposta = "Erro no envio de e-mail: {$mail->ErrorInfo}";
        }
    } else {
        $mensagemResposta = "Erro no reCAPTCHA. Por favor, verifique novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição - Decifrando Pessoas</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px
        }
        form{
            padding: 30px
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 96%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .g-recaptcha {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 20px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            height: 50px;
            font-weight: bold;
            width: 60%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .submit-button{
            width: 100%;
            display: flex;
            justify-content:center
        }
        .logo{
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .logo img{
            width: 35%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="./assets/logo.png"/>
        </div>
        <h2>Faça sua inscrição</h2>
        <form method="post" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br>
            
            <label for="email">Seu melhor e-mail:</label>
            <input type="email" id="email" name="email" required><br>
            
            <label for="telefone">Whatsapp:</label>
            <input type="tel" id="telefone" name="telefone" required pattern="\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}"><br>
            
            <div class="g-recaptcha" data-sitekey="6LfOn7QnAAAAAI8gUnXgfuhuiviEHB6OjZ5F6UvO"></div>
            
            <div class="submit-button">
                <input type="submit" value="PAGAR INSCRIÇÃO">
            </div>
        </form>
        <?php if (isset($mensagemResposta)): ?>
            <p><?php echo $mensagemResposta; ?></p>
        <?php endif; ?>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    var telefoneInput = document.getElementById("telefone");

    telefoneInput.addEventListener("input", function () {
        var valor = this.value.replace(/\D/g, ""); 
        var mascara = "(##) #####-####";
        var novoValor = "";

        for (var i = 0, j = 0; i < mascara.length; i++) {
            if (mascara[i] === "#") {
                if (valor[j] !== undefined) {
                    novoValor += valor[j];
                    j++;
                }
            } else {
                novoValor += mascara[i];
            }
        }

        this.value = novoValor;
    });
});
</script>
</body>
</html>