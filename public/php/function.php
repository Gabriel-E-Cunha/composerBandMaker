<?php
function loginError()
{
    console.log("clicou no login");
    $html = file_get_contents('http://localhost:8888/login/');
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    //get the element you want to append to
    $descBox = $doc->getElementById('loginBox');
    //create the element to append to #element1
    $appended = $doc->createElement('h5', 'Erro: Email ou senha incorretos!');
    //actually append the element
    $descBox->appendChild($appended);
    echo $doc->saveHTML();
}
