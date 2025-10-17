<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English strings for local_socialcert.
 *
 * @package   local_socialcert
 * @category  string
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Share Certificate AI';
$string['description'] = 'Permet à l’utilisateur de partager son certificat directement sur LinkedIn.';

// Paramètres.
$string['organizationid'] = 'ID d’organisation LinkedIn';
$string['organizationid_desc'] = 'Identifiant numérique de l’entreprise/organisation utilisé par LinkedIn Add-to-Profile. Laissez vide pour désactiver jusqu’à ce que la configuration soit terminée.';
$string['organizationname'] = 'Nom de l’organisation LinkedIn';
$string['organizationname_desc'] = 'Nom de l’organisation à afficher sur LinkedIn. Doit correspondre exactement à celui utilisé sur LinkedIn. Laissez vide pour désactiver jusqu’à la configuration.';

$string['privacy:metadata'] = 'Le plugin Share Certificate AI ne stocke aucune donnée personnelle.';

$string['noissue'] = 'Vous n’avez pas encore reçu de certificat pour ce cours.';

$string['linkcertbuttontext'] = 'Partager sur LinkedIn';
$string['copyarticlebuttontext'] = 'Copier l’article LinkedIn';

$string['shareinstruction'] = 'Félicitez-vous ! Cliquez ci-dessous pour mettre en avant votre certificat sur LinkedIn et partager votre réussite avec votre réseau :';

$string['copyconfirmation'] = 'Copié ✔';

$string['airesponsebtn'] = 'Activer l’IA';

$string['generating'] = 'Génération en cours…';

$string['certificateimage'] = 'certificate.png';

// Share Certificate AI – Partage (Étape 1)
$string['sharetitle']        = 'Partagez votre réussite sur LinkedIn';
$string['sharesubtitle']     = 'Nous publierons un lien vérifiable vers votre certificat.';
$string['buttonlabelshare']  = 'Partager sur LinkedIn';
$string['whatsharelabel']    = 'Que partageons-nous ?';

// Retour/statut (optionnel mais recommandé)
$string['popupblocked']      = 'Activez les fenêtres pop-up pour continuer.';
$string['sharecompleted']    = 'Partage LinkedIn terminé.';

$string['ai_field_heading']  = 'Texte de la publication';

$string['certificate_url']   = 'Lien';

$string['ai_actioncall']   = 'Créez en un clic un message professionnel pour votre publication LinkedIn';

$string['enableai'] = 'Activer l’IA pour suggérer le texte de la publication';

$string['enableai_desc'] = 'Si cette option est désactivée, le plug-in n’appellera pas Provider AI et ne générera aucune suggestion pour les publications LinkedIn.';

$string['certerror'] = "Vous devez avoir un certificat délivré avant de pouvoir le partager sur LinkedIn.";

