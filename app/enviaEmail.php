<?php

require './../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$carrinho = json_decode($_POST['carrinho'], true);
$dadosUsuario = json_decode($_POST['dadosUsuario'], true);


$nome_embalagem = $dadosUsuario['nome_embalagem'];


$xml = $_POST['dados'];

$itens_carrinho = "<h3>Carrinho de compras</h3>";

$imagem = $_POST['imagem'];

foreach($carrinho as $c){
    $itens_carrinho .= "<strong>Item:</strong> ".$c['item']."<br>";
    $itens_carrinho .= "<strong>Quantidade:</strong> ".$c['quantidade']."<br><hr>";
}

$mail = new PHPMailer(); // defaults to using php "mail()"
$mail->isSMTP();
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->CharSet = 'UTF-8';

$mail->Host = "mail.nacasadajoana.com.br"; // SMTP server
$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
$mail->SMTPAuth = true;                  // enable SMTP authentication
$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
//$mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port = 587;
$mail->Username = "naoresponda@nacasadajoana.com.br";
$mail->Password = "rogerio123#";

$assunto = 'PEDIDO ('.$xml['reference'].'): O SEU QUADRO PERSONALIZADO - NA CASA DA JOANA';

if($xml['paymentMethod']['type'] == 1){
    $metodoPagto = "Cartão de Crédito";
}elseif($xml['paymentMethod']['type'] == 2){
    $metodoPagto = "Boleto Bancário";
}else{
    $metodoPagto = "Débito Online";
}

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$msg = "<html><head><meta charset='utf-8'></head><body>
    <img src='http://nacasadajoana.com.br/site/emails_do_site/topo.gif'><br><br>
    <p>Foi efetuado um pedido de compra no site Na Casa Da Joana Personalizado.</p>
    <h3>Dados do pedido</h3>
    <strong>C&oacute;digo da compra: </strong>".$xml['code']."<br>
    <strong>Referência: </strong>".$xml['reference']."<br>
    <strong>Data da compra: </strong>".$xml['date']."<br>
    <strong>Total da compra: </strong>".$xml['grossAmount']."<br>
    <strong>M&eacute;todo de pagamento: </strong>".$metodoPagto."<br>";
    if($xml['installmentCount']){
        $msg .= "<strong>Parcelas: </strong>".$xml['installmentCount']."<br>";
    }

    $msg .= "
    <br>
    {$itens_carrinho}
    
    <br>
    <h3>Dados do comprador</h3>
    <strong>Nome: </strong>".$xml['sender']['name']."<br>
    <strong>E-mail: </strong>".$xml['sender']['email']."<br>
    <strong>Telefone: </strong>".$xml['sender']['phone']['areaCode']."-".$xml['sender']['phone']['number']."<br>
    <strong>Nome na embalagem: </strong>".$nome_embalagem."<br>
    
    
    <br>
    <h3>Dados da Entrega</h3>
    <strong>Endereço: </strong>".$xml['shipping']['address']['street']."<br>
    <strong>Número: </strong>".$xml['shipping']['address']['number']."<br>";
    if(isset($xml['shipping']['address']['complement'])){
        $msg .= "<strong>Complemento: </strong>".$xml['shipping']['address']['complement']."<br>";
    }
    $msg .= "
    <strong>Bairro: </strong>".$xml['shipping']['address']['district']."<br>
    <strong>Cidade: </strong>".$xml['shipping']['address']['city']."<br>
    <strong>Estado: </strong>".$xml['shipping']['address']['state']."<br>
    <strong>CEP: </strong>".$xml['shipping']['address']['postalCode']."<br>
    <strong>País: </strong>".$xml['shipping']['address']['country']."<br>

    <br>
    <h3>Imagem gerada</h3>
    <img src='{$imagem}' style='border:1px solid #333;' />

    <br>
    <h3>C&oacute;digo de retorno PagSeguro - para uso de identificação</h3>
    <div style='padding:20px; background-color:#f9f9f9; border:1px solid #CCC; color:#666;'>
        ".json_encode($xml)."
    </div>

    <br><br>
    <div style='font-size:12px;'>Desenvolvido por <a href='http://www.ipixels.com.br'>iPixels</a></div>   
</body></html>";

$mail->SetFrom('naoresponda@nacasadajoana.com.br', 'Na Casa Da Joana');
$mail->AddReplyTo("crieseuposter@nacasadajoana.com.br", "Na Casa Da Joana");
$mail->AltBody = "Habilite a visualização em HTML!"; // optional, comment out and test
$address = "michelmfreitas@gmail.com";
$mail->AddAddress($address, "Michel");
$mail->AddAddress("crieseuposter@nacasadajoana.com.br", "Na Casa Da Joana");
$mail->Subject = $assunto;
$mail->MsgHTML($msg);
$erro = 0;
if (!$mail->send()) {
    $erro++;
} else {
    //mensagem enviada
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    //if (save_mail($mail)) {
      // echo "Message saved!";
    //}
}
//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);
    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);
    return $result;
}


//email para o cliente
$assunto = 'Recebemos seu pedido '.$xml['reference'].' com sucesso';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$msg = "<html><head><meta charset='utf-8'></head><body>
    <div style='width:600px;'>
    <img src='http://nacasadajoana.com.br/site/emails_do_site/topo.gif'><br><br>
    </p>Oi ".$xml['sender']['name'].",<br><br>
    Ficamos muito felizes em receber o seu pedido <i><u>".$xml['reference']."</i></u> :)<br><br>
    Aqui estão algumas informações importantes:<br><br>
    Produzimos nossos produtos um a um, por isso seguimos os seguintes prazos de produção:<br><br>
    
    - Pôster sem moldura e outros produtos - prazo máximo de 7 dias úteis<br>
    - Pôster com moldura - prazo máximo de 10 dias úteis<br><br>
    
    O prazo começa a ser contado a partir da aprovação do seu pagamento pelo PagSeguro ou PayPal
    ou, ainda, pela verificação do seu dep&oacute;sito bancário.<br><br>
    Após esse prazo, o seu pedido será enviado pelos Correios e você; receberá automaticamente o 
    código de rastreio para acompanhá-lo. O prazo de entrega dos Correios varia de acordo com 
    a modalidade escolhida:<br><br>
    
    - Encomendas SEDEX - até 5 dias úteis<br>
    - Encomendas PAC - até 12 dias úteis<br><br>
    
    Para falar com a gente ou acompanhar o andamento do seu pedido, acesse o menu 'Minha Conta'.
    <br><br><br>";

    if($metodoPagto == "Boleto Bancário"){
        $msg .= "<a href='".$xml['paymentLink']."' target='_blank'>Link do boleto</a><br><br><br>";
    }
    
    $msg .= "Abra um sorriso Na Casa da Joana :)<br><br><br><br>

    <table border='0' cellpadding='0' cellspacing='0' height='0' width='600' style='border-top:10px solid orangered; background-color:#f7f7ef;'>
    <tbody>
        <tr>
            <td width='71'>
                <img height='71' src='http://nacasadajoana.com.br/site/emails_do_site/coracao.gif' width='71'></td>
            <td>
                <font color='#f15a40'><a href='http://www.nacasadajoana.com.br' target='_blank'>nacasadajoana.com.br</a></td>
            <td width='25'>
                <a href='https://www.facebook.com/nacasadajoana' target='_blank'><img alt='Facebook' border='0' height='71' src='http://nacasadajoana.com.br/site/emails_do_site/facebook.gif' width='25'></a></td>
            <td width='6'>
                &nbsp;</td>
            <td width='25'>
                <a href=''><img alt='Pinterest' border='0' height='71' src='http://nacasadajoana.com.br/site/emails_do_site/pinterest.gif' width='25'></a></td>
            <td width='6'>
                &nbsp;</td>
            <td width='25'>
                <a href='https://www.instagram.com/nacasadajoana/'><img alt='Instagram' border='0' height='71' src='http://nacasadajoana.com.br/site/emails_do_site/instagram.gif' width='25'></a></td>
            <td width='20'>
                &nbsp;</td>
        </tr>
    </tbody>
</table></div>
    </body></html>";

$mail->SetFrom('naoresponda@nacasadajoana.com.br', 'Na Casa Da Joana');
$mail->AddReplyTo("crieseuposter@nacasadajoana.com.br", "Na Casa Da Joana");
$mail->AltBody = "Habilite a visualização em HTML!"; // optional, comment out and test
$address = "michelmfreitas@gmail.com";
$mail->AddAddress($address, "Michel");
$mail->AddAddress($xml['sender']['email'], $xml['sender']['name']);
$mail->Subject = $assunto;
$mail->MsgHTML($msg);

if (!$mail->send()) {
    $erro++;
} else {
    $retorno = array(
        "code"=>1,
        "error"=>$erro
    );
    echo json_encode($retorno);    
}
?>