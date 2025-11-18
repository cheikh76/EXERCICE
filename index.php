<?php
$fichier = fopen('contact.txt', 'r');
$contact = ["Alice Dupont", "John Doe", "Jean Martin"];
 
 
    while (($ligne = fgets($fichier)) !== false) {
        $ligne = trim($ligne);
        if ($ligne === "") continue;
 
        if (!in_array($ligne, $contact)) {
            $contact[] = $ligne;
        }}
    fclose($fichier);
print_r($contact);